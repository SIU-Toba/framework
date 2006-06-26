<?php
require_once("nucleo/browser/recurso.php");					//Encapsulamiento de la llamada a recursos
require_once("nucleo/browser/js.php");						//Encapsulamiento de la utilidades javascript
require_once("nucleo/browser/debug.php");					//DUMP de arrays, arboles y estructuras centrales
require_once("nucleo/browser/hilo.php");					//Canal de comunicacion inter-ejecutable
require_once("nucleo/lib/formateo.php"); 		//Funciones de formateo de columnas
require_once("nucleo/lib/ei.php"); 							//Elementos de interface
require_once("nucleo/browser/logica.php");					//Elementos de logica
require_once("nucleo/lib/parseo.php");			       		//Funciones de parseo
require_once("nucleo/lib/configuracion.php");	      		//Acceso a la configuracion del sistema
require_once("nucleo/browser/tipo_pagina/tipo_pagina.php");	//Clase base de Tipo de pagina generico
require_once("nucleo/browser/menu/menu.php");				//Clase base de Menu 
require_once("nucleo/lib/form.php");

/**
 * @todo Al servicio pdf le falta pedir por parametro que metodo llamar para construirlo
 */
class solicitud_web extends solicitud
{
	protected $zona;			//Objeto que representa una zona que vincula varios items
	protected $cis;
	protected $cn;
	protected $tipo_pagina;
	
	function __construct($info)
	{
		$this->tipo_solicitud = 'web';
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
	/*
		NOTA: esto hay que arreglarlo: esta cambiado porque no se lleva bien con el TRAP
			$this->pre_proceso_servicio();
			$this->crear_zona();
			$this->cargar_objetos();
			$this->procesar_eventos();
			$this->procesar_servicios();
	*/
			$this->crear_zona();
			$this->cargar_objetos();
			$this->procesar_eventos();
			$this->pre_proceso_servicio();
			$this->procesar_servicios();
		} catch( excepcion_reset_nucleo $e ) {
			// Recarga del nucleo
			throw $e;
		} catch(excepcion_toba $e) {
			toba::get_logger()->error($e, 'toba');
			toba::get_cola_mensajes()->agregar($e->getMessage(), "error");
			toba::get_cola_mensajes()->mostrar();
		}
	}
	
	/**
	 * Permite que el servicio produzca alguna salida antes de los eventos, para optimizaciones
	 */
	protected function pre_proceso_servicio()
	{
		$servicio = toba::get_hilo()->obtener_servicio_solicitado();
		$callback = "servicio_pre__$servicio";
		if (method_exists($this, $callback)) {
			$this->$callback();
		}
	}
	
	protected function crear_zona()
	{
		if(trim($this->info['basica']['item_zona'])!=""){
			require_once($this->info['basica']['item_zona_archivo']);
			$this->zona = new $this->info['basica']['item_zona']($this->info['basica']['item_zona'], 
														$this->info['basica']['item_zona_proyecto'],
														$this);
		}		
	}
	
	protected function cargar_objetos()
	{
		toba::get_logger()->seccion("Cargando objetos...", 'toba');
		$this->cis = array();		
		if ($this->info['objetos'] > 0) {
			$i = 0;
			//Construye los objetos ci y el cn
			foreach ($this->info['objetos'] as $objeto) {
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
					$this->objetos[$ci]->asignar_controlador_negocio( $this->objetos[$this->cn] );
			    } 
			}
		} else { 
			echo ei_mensaje("Necesita asociar un objeto CI al ítem.");
	    }
	}
	
	protected function procesar_eventos()
	{
		toba::get_logger()->seccion("Procesando eventos...", 'toba');		
		//--Antes de procesar los eventos toda entrada UTF-8 debe ser pasada a ISO88591
		if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'UTF-8') !== false) {
			foreach ($_POST as $clave => $valor) {
				$_POST[$clave] = utf8_decode($valor);
			}
		}
		
		//-- Se procesan los eventos generados en el pedido anterior
		foreach ($this->cis as $ci) {
			$this->objetos[$ci]->procesar_eventos();
		}
	}
	
	protected function procesar_servicios()
	{
		toba::get_logger()->seccion("Cargando dependencias para responder al servicio...", 'toba');
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

		$servicio = toba::get_hilo()->obtener_servicio_solicitado();
		$callback = "servicio__$servicio";
		toba::get_logger()->seccion("Respondiendo al $callback...", 'toba');		
		if (method_exists($this, $callback)) {
			$this->$callback($destino);
		} elseif (count($destino) == 1 && method_exists(current($destino), $callback)) {
			//-- Se pide un servicio desconocido para la solicitud, pero conocido para el objeto destino
			$obj = current($destino);
			$obj->$callback();
		} else {
			throw new excepcion_toba("El servicio $servicio no está soportado");			
		}
	}

		//---------------------------------------------------------------
		//-------------------------- SERVICIOS --------------------------
		//---------------------------------------------------------------
	
	protected function servicio_pre__obtener_html()
	{
		//--- Tipo de PAGINA
		toba::get_cronometro()->marcar('SOLICITUD BROWSER: Pagina TIPO (cabecera) ',apex_nivel_nucleo);
		if (isset($this->info['basica']['tipo_pagina_archivo'])) {
			require_once($this->info['basica']['tipo_pagina_archivo']);
		}
		$this->tipo_pagina = new $this->info['basica']['tipo_pagina_clase']();
		$this->tipo_pagina->encabezado();		
	}
	
	protected function servicio__obtener_html($objetos)
	{
		//--- Abre el formulario
		echo form::abrir("formulario_toba", toba::get_vinculador()->crear_autovinculo());

		//--- Parte superior de la zona
		if ($this->hay_zona() &&  $this->zona->controlar_carga()) {
			$this->zona->obtener_html_barra_superior();
		}			
		
		$this->tipo_pagina->pre_contenido();

		foreach ($objetos as $obj) {
			//-- Librerias JS necesarias
			js::cargar_consumos_globales($obj->get_consumo_javascript());
			//-- HTML propio del objeto
			$obj->obtener_html();
			//-- Javascript propio del objeto
			echo js::abrir();
			$objeto_js = $obj->obtener_javascript();
			echo "\n$objeto_js.iniciar();\n";
			echo js::cerrar();
		}

		$this->tipo_pagina->post_contenido();

		// Carga de componentes JS genericos
		echo js::abrir();
		toba::get_vinculador()->obtener_javascript();
		echo js::cerrar();
		
		//--- Parte inferior de la zona
		if ($this->hay_zona() &&  $this->zona->controlar_carga()) {
			$this->zona->obtener_html_barra_inferior();
		}
		//--- Muestra la cola de mensajes
		toba::get_cola_mensajes()->mostrar();		
		
		//--- Fin del form y parte inferior del tipo de página
		echo form::cerrar();
		toba::get_cronometro()->marcar('SOLICITUD BROWSER: Pagina TIPO (pie) ',apex_nivel_nucleo);
       	$this->tipo_pagina->pie();
	}
	
	protected function servicio__vista_pdf( $objetos )
	{
		require_once('nucleo/lib/salidas/pdf.php');
		$salida = new pdf();
		$salida->asignar_objetos( $objetos );
		$salida->generar_salida();
	}
	
	protected function servicio__vista_html_impr( $objetos )
	{
		$clase = info_proyecto::instancia()->get_parametro('salida_impr_html_c');
		$archivo = info_proyecto::instancia()->get_parametro('salida_impr_html_a');
		if ( $clase && $archivo ) {
			//El proyecto posee un objeto de impresion HTML personalizado
			require_once($archivo);
			$salida = new $clase();	
		} else {
			require_once('nucleo/lib/salidas/html_impr.php');
			$salida = new html_impr();
		}
		$salida->asignar_objetos( $objetos );
		$salida->generar_salida();
	}

	/**
	 * Retorna el html y js localizado de un componente y sus dependencias
	 */
	protected function servicio__html_parcial($objetos)
	{
		//--- Se envia el HTML
		foreach ($objetos as $objeto) {
			$objeto->obtener_html();
		}	
				
		//--- Se envian los consumos js
		echo "<--toba-->";
		$consumos = array();
		foreach ($objetos as $objeto) {
			$consumos = array_merge($consumos, $objeto->get_consumo_javascript());
		}
		echo "toba.incluir(".js::arreglo($consumos, false).");\n"; 
		
		//--- Se envia el javascript
		echo "<--toba-->";
		//Se actualiza el prefijo de los vinculos
		$autovinculo = toba::get_vinculador()->crear_autovinculo();
		echo "window.toba_prefijo_vinculo='$autovinculo';\n";
		//Se actualiza el vinculo del form
		echo "document.formulario_toba.action='$autovinculo'\n";
		foreach ($objetos as $objeto) {
			//$objeto->servicio__html_parcial();
			$objeto_js = $objeto->obtener_javascript();
			echo "\nwindow.$objeto_js.iniciar();\n";
		}
	}
	
	protected function servicio__cascadas_efs($objetos)
	{
		toba::get_hilo()->desactivar_reciclado();
		try {
			if (count($objetos) != 1) {
				$actual = count($objetos);
				throw new excepcion_toba("Las cascadas sólo admiten un objeto destino (actualmente: $actual)");
			}
			$objetos[0]->servicio__cascadas_efs();
		} catch(excepcion_toba $e) {
			toba::get_logger()->error($e, 'toba');
		}
	}
		
	/**
	 * Servicio genérico de acceso a objetos a través de parámetros
	 */
	protected function servicio__ejecutar($objetos)
	{
		foreach ($objetos as $objeto) {
			$objeto->servicio__ejecutar();
		}
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

/**
 * Función temporal para no cambiar en profundidad la implementación actual de cascadas
 * @todo Esta funcion no debe estar cuando se refactorizen las cascadas en forma completa
 */
function responder($rs)
{
	toba::get_logger()->debug("Respuesta Cascadas: ".var_export($rs, true), "toba");
	$json = new Services_JSON();
	$respuesta = $json->encode($rs);
	echo $respuesta;	
}
?>