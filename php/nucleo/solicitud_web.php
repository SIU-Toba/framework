<?php
require_once("nucleo/browser/recurso.php");					//Encapsulamiento de la llamada a recursos
require_once("nucleo/browser/js.php");						//Encapsulamiento de la utilidades javascript
require_once("nucleo/browser/debug.php");					//DUMP de arrays, arboles y estructuras centrales
require_once("nucleo/browser/hilo.php");					//Canal de comunicacion inter-ejecutable
require_once("nucleo/browser/interface/formateo.php"); 		//Funciones de formateo de columnas
require_once("nucleo/browser/interface/ei.php");			//Elementos de interface
require_once("nucleo/browser/logica.php");					//Elementos de logica
require_once("nucleo/lib/parseo.php");			       		//Funciones de parseo
require_once("nucleo/lib/configuracion.php");	      		//Acceso a la configuracion del sistema
require_once("nucleo/browser/tipo_pagina/tipo_pagina.php");	//Clase base de Tipo de pagina generico
require_once("nucleo/browser/menu/menu.php");				//Clase base de Menu 

/**
 * @todo Hacer que el disparo de los servicios sea mas dinamico y no dependa de un switch
 * @todo Al servicio pdf le falta pedir por parametro que metodo llamar para construirlo
 */
class solicitud_web extends solicitud
{
	protected $zona;			//Objeto que representa una zona que vincula varios items
	protected $cis;
	protected $cn;
	
	function __construct($info)
	{
		$this->info = $info;
		toba::get_cronometro()->marcar('basura',apex_nivel_nucleo);
        
		parent::__construct(toba::get_hilo()->obtener_item_solicitado(),toba::get_hilo()->obtener_usuario());
		
		//Le pregunto al HILO si se solicito cronometrar la PAGINA
		if(toba::get_hilo()->usuario_solicita_cronometrar()){
			$this->registrar_db = true;
			$this->cronometrar = true;
		}
		toba::get_cronometro()->marcar('SOLICITUD BROWSER: Inicializacion (ZONA, VINCULADOR)',"nucleo");
	}
//--------------------------------------------------------------------------------------------

	function procesar()
	{	
		try {
			$this->crear_zona();
			$this->cargar_objetos();
			$this->procesar_eventos();
			$this->procesar_servicios();
		} catch(excepcion_toba $e) {
			toba::get_logger()->debug($e);
			toba::get_cola_mensajes()->agregar($e->getMessage(), "error");
			toba::get_cola_mensajes()->mostrar();
		}
	}
	
	protected function crear_zona()
	{
		if(trim($this->info['item_zona'])!=""){
			require_once($this->info['item_zona_archivo']);
			$this->zona = new $this->info['item_zona']($this->info['item_zona'], 
														$this->info['item_zona_proyecto'],
														$this);
		}		
	}
	
	protected function cargar_objetos()
	{
		if ($this->info_objetos > 0) {
			$this->cis = array();
			$i = 0;
			//Construye los objetos ci y el cn
			foreach ($this->info_objetos as $objeto) {
				if ($objeto['clase'] != 'objeto_cn') {
					$this->cis[] = $this->cargar_objeto($objeto['clase'],$i); 
					$i++;
				} else {
					$this->cn = $this->cargar_objeto($objeto['clase'],0); 
				}
			}
			//Asigna el cn a los cis			
			if (isset($this->cn)) {			
				foreach ($this->cis as $ci) {
					$this->objetos[$ci]->asignar_controlador_negocio( $this->objetos[$cn] );
			    } 
			}
		} else { 
			echo ei_mensaje("Necesita asociar un objeto CI al ítem.");
	    }
	}
	
	protected function procesar_eventos()
	{
		//-- Se procesan los eventos generados en el pedido anterior
		foreach ($this->cis as $ci) {
			$this->objetos[$ci]->procesar_eventos();
		}
	}
	
	protected function procesar_servicios()
	{
		//--- Se fuerza a que los Cis carguen sus dependencias (por si alguna no se cargo para atender ls eventos)
		foreach ($this->cis as $ci) {
			$this->objetos[$ci]->cargar_dependencias_gi();
		}	
		
		//--- Se determina el destino del servicio
		$objetos_destino = toba::get_hilo()->obtener_id_objetos_destino();
		if (!isset($objetos_destino)) {
			//En caso que el servicio no especifique destino, se asume que son todos los cis asociados al item
			$destino = array();
			foreach ($this->cis as $ci) {
				$destino[] = $this->objetos[$ci];	
			}
		} else {
			//Se especifico un destino, se buscan los objetos referenciados
			$destino = array();
			foreach ($objetos_destino as $obj) {
				$destino[] = constructor_toba::buscar_runtime(array('proyecto' => $obj[0],
																'componente' => $obj[1]));
			}
		}

		//--- Se atiende el sevicio, esto esta mal, debería ser más dinamico para que se puedan agregar mas servicio
		$servicio = toba::get_hilo()->obtener_servicio_solicitado();
		switch ($servicio) {
			case 'obtener_html':
				$this->servicio__obtener_html($destino);
				break;
			case 'obtener_pdf':
				$this->servicio__obtener_pdf($destino);
				break;
			default:
				throw new excepcion_toba("El servicio $servicio no está soportado");
		}
	}

	protected function servicio__obtener_html($objetos)
	{
		//--- Tipo de PAGINA
		toba::get_cronometro()->marcar('SOLICITUD BROWSER: Pagina TIPO (cabecera) ',apex_nivel_nucleo);
		if (isset($this->info['tipo_pagina_archivo'])) {
			require_once($this->info['tipo_pagina_archivo']);
		}
		$tipo_pagina = new $this->info['tipo_pagina_clase']();
		$tipo_pagina->encabezado();		
		
		//--- Abre el formulario
		$autovinculo = toba::get_vinculador()->generar_solicitud(null,null,null,true);
		echo form::abrir("formulario_toba", $autovinculo);
		
		//--- Parte superior de la zona
		if ($this->hay_zona() &&  $this->zona->controlar_carga()) {
			$this->zona->obtener_html_barra_superior();
		}
		
		//--- Genera la interfaz gráfica de todos los CIs		
		echo "\n<div align='center' class='cuerpo'>\n";
		foreach ($objetos as $obj) {
			//-- Librerias JS necesarias
			js::cargar_consumos_globales($obj->consumo_javascript_global());
			//-- HTML propio del objeto
			$obj->obtener_html();
			//-- Javascript propio del objeto
			echo js::abrir();
			$objeto_js = $obj->obtener_javascript();
			echo "\n$objeto_js.iniciar();\n";
			echo js::cerrar();
		}
		echo "\n</div>\n";
		
		//--- Parte inferior de la zona
		if ($this->hay_zona() &&  $this->zona->controlar_carga()) {
			$this->zona->obtener_html_barra_inferior();
		}
		//--- Muestra la cola de mensajes y el logger
		toba::get_cola_mensajes()->mostrar();		
		echo toba::get_logger()->mostrar_pantalla();
		
		//--- Fin del form y parte inferior del tipo de página 
		echo form::cerrar();
		toba::get_cronometro()->marcar('SOLICITUD BROWSER: Pagina TIPO (pie) ',apex_nivel_nucleo);
       	$tipo_pagina->pie();
	}
	
	protected function servicio__obtener_pdf($objetos)
	{
		require_once('nucleo/lib/pdf.php');
		$pdf = new pdf();
		$pdf->asignar_objetos( $objetos );
		$pdf->generar_salida();
	}
	
//--------------------------------------------------------------------------------------------
	/**
	 * @return zona
	 */
	function zona()
	{
		return $this->zona;
	}
	
	function hay_zona()
	{
		return isset($this->zona);	
	}	
	
//--------------------------------------------------------------------------------------------

	function registrar( )
	{
		parent::registrar( toba::get_hilo()->obtener_proyecto() );
		if($this->registrar_db){
			$sql = "INSERT INTO apex_solicitud_browser (solicitud_browser, sesion_browser, ip)
					VALUES ('$this->id','".$_SESSION["toba"]["id"]."','".$_SERVER["REMOTE_ADDR"]."');";
			toba::get_db('instancia')->ejecutar($sql);
		}
 	}
}
?>