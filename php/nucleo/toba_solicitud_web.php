<?php
require_once("lib/toba_parseo.php");				       		//Funciones de parseo
require_once("nucleo/lib/toba_recurso.php");					//Encapsulamiento de la llamada a toba_recursos
require_once("nucleo/lib/toba_js.php");							//Encapsulamiento de la utilidades javascript
require_once("nucleo/lib/toba_debug.php");						//DUMP de arrays, arboles y estructuras centrales
require_once("nucleo/lib/toba_memoria.php");					//Canal de comunicacion inter-ejecutable
require_once("nucleo/lib/interface/toba_formateo.php"); 		//Funciones de formateo de columnas
require_once("nucleo/lib/interface/toba_form.php");				//inputs HTML
require_once("nucleo/lib/interface/toba_ei.php"); 				//elementos basicos de interface
require_once("nucleo/tipo_pagina/toba_tipo_pagina.php");		//Clase base de Tipo de pagina generico
require_once("nucleo/menu/toba_menu.php");						//Clase base de Menu 
require_once("nucleo/lib/toba_zona.php");

/**
 * Solicitud pensada para contener el ciclo request-response http
 * La etapa de request se la denomina de 'eventos' 
 * La etapa de response se la denomina de 'servicios'
 * 
 * @package Centrales
 * 
 * @todo Al servicio pdf le falta pedir por parametro que metodo llamar para construirlo
 */
class toba_solicitud_web extends toba_solicitud
{
	protected $zona;			//Objeto que representa una zona que vincula varios items
	protected $cis;
	protected $cn;
	protected $tipo_pagina;
	
	function __construct($info)
	{
		$this->info = $info;
		toba::cronometro()->marcar('basura',apex_nivel_nucleo);
		parent::__construct(toba::memoria()->get_item_solicitado(), toba::usuario()->get_id());
		toba::cronometro()->marcar('SOLICITUD WEB: Inicializacion (ZONA, VINCULADOR)',"nucleo");
	}

	/**
	 * Crea la zona, carga los componentes, procesa los eventos y los servicios
	 */
	function procesar()
	{	
		try {
			$this->crear_zona();			
			$redirecciona = ($this->info['basica']['redirecciona']);
			// Si la pagina redirecciona, no mando los pre_servicios ahora
			if (!$redirecciona) {
				$this->pre_proceso_servicio();
			}
			$this->cargar_objetos();
			$this->procesar_eventos();
			if ($redirecciona) {
				$this->pre_proceso_servicio();
			}
			$this->procesar_servicios();
		} catch(toba_error $e) {
			toba::logger()->error($e, 'toba');
			toba::notificacion()->agregar($e->getMessage(), "error");
			toba::notificacion()->mostrar();
		}
	}
	
	/**
	 * Permite que el servicio produzca alguna salida antes de los eventos, para optimizaciones
	 */
	protected function pre_proceso_servicio()
	{
		$servicio = toba::memoria()->get_servicio_solicitado();
		$callback = "servicio_pre__$servicio";
		if (method_exists($this, $callback)) {
			$this->$callback();
		}
	}
	
	/**
	 * Instancia los cis/cns de primer nivel asignados al item y los relaciona
	 */
	protected function cargar_objetos()
	{
		toba::logger()->seccion("Cargando objetos...", 'toba');
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
			throw new toba_error_def("Necesita asociar un objeto CI al ítem.");
	    }
	}

	/**
	 * Inicializa los componentes y dispara la atención de eventos en forma recursiva
	 */
	protected function procesar_eventos()
	{
		toba::logger()->seccion("Procesando eventos...", 'toba');		
		//--Antes de procesar los eventos toda entrada UTF-8 debe ser pasada a ISO88591
		if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'UTF-8') !== false) {
			foreach ($_POST as $clave => $valor) {
				$_POST[$clave] = utf8_decode($valor);
			}
		}
		//-- Se procesan los eventos generados en el pedido anterior
		foreach ($this->cis as $ci) {
			$this->objetos[$ci]->inicializar();
			try {
				$this->objetos[$ci]->disparar_eventos();
			} catch(toba_error $e) {
				$this->log->info($e, 'toba');			
				toba::notificacion()->agregar($e->getMessage());
			}
		}
	}
	
	/**
	 * Se configuran los componentes y se atiende el servicio en forma recursiva
	 */
	protected function procesar_servicios()
	{
		toba::logger()->seccion("Configurando dependencias para responder al servicio...", 'toba');
		//Se fuerza a que los Cis carguen sus dependencias (por si alguna no se cargo para atender ls eventos) y se configuren
		foreach ($this->cis as $ci) {
			$this->objetos[$ci]->pre_configurar();
			//--- Lugar para poner algun callback a nivel operacion?
			$this->objetos[$ci]->post_configurar();
		}
		
		//--- Se determina el destino del servicio
		$objetos_destino = toba::memoria()->get_id_objetos_destino();
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
				$destino[] = toba_constructor::buscar_runtime(array('proyecto' => $obj[0],
																'componente' => $obj[1]));
			}
		}

		$servicio = toba::memoria()->get_servicio_solicitado();
		$callback = "servicio__$servicio";
		toba::logger()->seccion("Respondiendo al $callback...", 'toba');		
		if (method_exists($this, $callback)) {
			$this->$callback($destino);
		} elseif (count($destino) == 1 && method_exists(current($destino), $callback)) {
			//-- Se pide un servicio desconocido para la solicitud, pero conocido para el objeto destino
			$obj = current($destino);
			$obj->$callback();
		} else {
			throw new toba_error("El servicio $servicio no está soportado");			
		}
	}

	//---------------------------------------------------------------
	//-------------------------- SERVICIOS --------------------------
	//---------------------------------------------------------------

	/**
	 * Optimización del servicio de generar html para enviar algun contenido al browser
	 */
	protected function servicio_pre__generar_html()
	{
		//--- Tipo de PAGINA
		toba::cronometro()->marcar('SOLICITUD BROWSER: Pagina TIPO (cabecera) ',apex_nivel_nucleo);
		if (isset($this->info['basica']['tipo_pagina_archivo'])) {
			require_once($this->info['basica']['tipo_pagina_archivo']);
		}
		$this->tipo_pagina = new $this->info['basica']['tipo_pagina_clase']();
		$this->tipo_pagina->encabezado();
	}
	
	/**
	 * Servicio común de generación html
	 */
	protected function servicio__generar_html($objetos)
	{
		//--- Parte superior de la zona
		if (toba::solicitud()->hay_zona() && toba::zona()->cargada()) {
			toba::zona()->generar_html_barra_superior();
		}
		echo '<div style="clear:both;"></div>';
		echo "</div>"; //-- Se finaliza aqui el div del encabezado, por la optimizacion del pre-servicio..
		$this->tipo_pagina->pre_contenido();
		
		//--- Abre el formulario
		$accion = $this->info['basica']['item_act_accion_script'];
		if ($accion == '') {
			echo toba_form::abrir("formulario_toba", toba::vinculador()->crear_autovinculo(), "onsubmit='return false;'");
			foreach ($objetos as $obj) {
				//-- Librerias JS necesarias
				toba_js::cargar_consumos_globales($obj->get_consumo_javascript());
				//-- HTML propio del objeto
				$obj->generar_html();
				//-- Javascript propio del objeto
				echo toba_js::abrir();
				$objeto_js = $obj->generar_js();
				echo "\n$objeto_js.iniciar();\n";
				echo toba_js::cerrar();
			}
			//--- Fin del form y parte inferior del tipo de página
			echo toba_form::cerrar();

		} else {
			include($accion);	
		}

		$this->tipo_pagina->post_contenido();
		// Carga de componentes JS genericos
		echo toba_js::abrir();
		toba::vinculador()->generar_js();
		echo toba_js::cerrar();
		
		//--- Parte inferior de la zona
		if ($this->hay_zona() &&  $this->zona->cargada()) {
			$this->zona->generar_html_barra_inferior();
		}
		//--- Muestra la cola de mensajes
		toba::notificacion()->mostrar();		
		toba::cronometro()->marcar('SOLICITUD: Pagina TIPO (pie) ',apex_nivel_nucleo);
       	$this->tipo_pagina->pie();
	}
	
	protected function servicio__vista_pdf( $objetos )
	{
		require_once('nucleo/lib/salidas/toba_pdf.php');
		$salida = new toba_pdf();
		$salida->asignar_objetos( $objetos );
		$salida->generar_salida();
	}

	/**
	 * Genera una salida html pensada para impresión
	 */
	protected function servicio__vista_toba_impr_html( $objetos )
	{
		$clase = toba::proyecto()->get_parametro('salida_impr_html_c');
		$archivo = toba::proyecto()->get_parametro('salida_impr_html_a');
		if ( $clase && $archivo ) {
			//El proyecto posee un objeto de impresion HTML personalizado
			require_once($archivo);
			$salida = new $clase();	
		} else {
			require_once('nucleo/lib/salidas/toba_impr_html.php');
			$salida = new toba_impr_html();
		}
		$salida->asignar_objetos( $objetos );
		$salida->generar_salida();
	}

	/**
	 * Retorna el html y js localizado de un componente y sus dependencias.
	 * Pensado como respuesta a una solicitud AJAX
	 */
	protected function servicio__html_parcial($objetos)
	{
		//--- Se envia el HTML
		foreach ($objetos as $objeto) {
			$objeto->generar_html();
		}	
				
		//--- Se envian los consumos js
		echo "<--toba-->";
		$consumos = array();
		foreach ($objetos as $objeto) {
			$consumos = array_merge($consumos, $objeto->get_consumo_javascript());
		}
		echo "toba.incluir(".toba_js::arreglo($consumos, false).");\n"; 
		
		//--- Se envia el javascript
		echo "<--toba-->";
		//Se actualiza el prefijo de los vinculos
		$autovinculo = toba::vinculador()->crear_autovinculo();
		echo "window.toba_prefijo_vinculo='$autovinculo';\n";
		//Se actualiza el vinculo del form
		echo "document.formulario_toba.action='$autovinculo'\n";
		foreach ($objetos as $objeto) {
			//$objeto->servicio__html_parcial();
			$objeto_js = $objeto->generar_js();
			echo "\nwindow.$objeto_js.iniciar();\n";
		}
	}
	
	/**
	 * Responde los valores en cascadas de un formulario específico
	 */
	protected function servicio__cascadas_efs($objetos)
	{
		toba::memoria()->desactivar_reciclado();
		try {
			if (count($objetos) != 1) {
				$actual = count($objetos);
				throw new toba_error("Las cascadas sólo admiten un objeto destino (actualmente: $actual)");
			}
			$objetos[0]->servicio__cascadas_efs();
		} catch(toba_error $e) {
			toba::logger()->error($e, 'toba');
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

	function registrar()
	{
		parent::registrar( toba::proyecto()->get_id() );
		if($this->registrar_db){
			toba_instancia::registrar_solicitud_browser($this->id, toba_sesion::get_id(), $_SERVER['REMOTE_ADDR']);
		}
 	}
}


?>