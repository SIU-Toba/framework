<?php
require_once("nucleo/lib/recurso.php");						//Encapsulamiento de la llamada a recursos
require_once("nucleo/lib/js.php");							//Encapsulamiento de la utilidades javascript
require_once("nucleo/lib/debug.php");						//DUMP de arrays, arboles y estructuras centrales
require_once("nucleo/lib/hilo.php");						//Canal de comunicacion inter-ejecutable
require_once("nucleo/lib/interface/formateo.php"); 			//Funciones de formateo de columnas
require_once("nucleo/lib/interface/form.php");				//inputs HTML
require_once("nucleo/lib/interface/ei.php"); 				//elementos basicos de interface
require_once("nucleo/tipo_pagina/tipo_pagina.php");			//Clase base de Tipo de pagina generico
require_once("nucleo/menu/menu.php");						//Clase base de Menu 
require_once("nucleo/lib/zona.php");
require_once("lib/parseo.php");					       		//Funciones de parseo

/**
 * Solicitud pensada para contener el ciclo request-response http
 * La etapa de request se la denomina de 'eventos' 
 * La etapa de response se la denomina de 'servicios'
 * 
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
		$this->info = $info;
		toba::get_cronometro()->marcar('basura',apex_nivel_nucleo);
		parent::__construct(toba::get_hilo()->obtener_item_solicitado(), toba::get_hilo()->obtener_usuario());
		toba::get_cronometro()->marcar('SOLICITUD WEB: Inicializacion (ZONA, VINCULADOR)',"nucleo");
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
	
	/**
	 * Instancia los cis/cns de primer nivel asignados al item y los relaciona
	 */
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
			throw new excepcion_toba_def("Necesita asociar un objeto CI al ítem.");
	    }
	}

	/**
	 * Inicializa los componentes y dispara la atención de eventos en forma recursiva
	 */
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
			try {
				$this->objetos[$ci]->inicializar();
				$this->objetos[$ci]->disparar_eventos();
			} catch(excepcion_toba $e) {
				$this->log->info($e, 'toba');			
				toba::get_cola_mensajes()->agregar($e->getMessage());
			}
		}
	}
	
	/**
	 * Se configuran los componentes y se atiende el servicio en forma recursiva
	 */
	protected function procesar_servicios()
	{
		toba::get_logger()->seccion("Configurando dependencias para responder al servicio...", 'toba');
		//Se fuerza a que los Cis carguen sus dependencias (por si alguna no se cargo para atender ls eventos) y se configuren
		foreach ($this->cis as $ci) {
			$this->objetos[$ci]->pre_configurar();
			//--- Lugar para poner algun callback a nivel operacion?
			$this->objetos[$ci]->post_configurar();
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

	/**
	 * Optimización del servicio de generar html para enviar algun contenido al browser
	 */
	protected function servicio_pre__generar_html()
	{
		//--- Tipo de PAGINA
		toba::get_cronometro()->marcar('SOLICITUD BROWSER: Pagina TIPO (cabecera) ',apex_nivel_nucleo);
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
		if (toba::get_solicitud()->hay_zona() && toba::get_zona()->cargada()) {
			toba::get_zona()->generar_html_barra_superior();
		}
		echo '<div style="clear:both;"></div>';
		echo "</div>"; //-- Se finaliza aqui el div del encabezado, por la optimizacion del pre-servicio..
		$this->tipo_pagina->pre_contenido();
		
		//--- Abre el formulario
		$accion = $this->info['basica']['item_act_accion_script'];
		if ($accion == '') {
			echo form::abrir("formulario_toba", toba::get_vinculador()->crear_autovinculo());
			foreach ($objetos as $obj) {
				//-- Librerias JS necesarias
				js::cargar_consumos_globales($obj->get_consumo_javascript());
				//-- HTML propio del objeto
				$obj->generar_html();
				//-- Javascript propio del objeto
				echo js::abrir();
				$objeto_js = $obj->generar_js();
				echo "\n$objeto_js.iniciar();\n";
				echo js::cerrar();
			}
			//--- Fin del form y parte inferior del tipo de página
			echo form::cerrar();

		} else {
			include($accion);	
		}

		$this->tipo_pagina->post_contenido();
		// Carga de componentes JS genericos
		echo js::abrir();
		toba::get_vinculador()->generar_js();
		echo js::cerrar();
		
		//--- Parte inferior de la zona
		if ($this->hay_zona() &&  $this->zona->cargada()) {
			$this->zona->generar_html_barra_inferior();
		}
		//--- Muestra la cola de mensajes
		toba::get_cola_mensajes()->mostrar();		
		toba::get_cronometro()->marcar('SOLICITUD: Pagina TIPO (pie) ',apex_nivel_nucleo);
       	$this->tipo_pagina->pie();
	}
	
	protected function servicio__vista_pdf( $objetos )
	{
		require_once('nucleo/lib/salidas/pdf.php');
		$salida = new pdf();
		$salida->asignar_objetos( $objetos );
		$salida->generar_salida();
	}

	/**
	 * Genera una salida html pensada para impresión
	 */
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
			$objeto_js = $objeto->generar_js();
			echo "\nwindow.$objeto_js.iniciar();\n";
		}
	}
	
	/**
	 * Responde los valores en cascadas de un formulario específico
	 */
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

	function registrar()
	{
		parent::registrar( toba::get_hilo()->obtener_proyecto() );
		if($this->registrar_db){
			info_instancia::registrar_solicitud_browser($this->id, sesion_toba::get_id(), $_SERVER['REMOTE_ADDR']);
		}
 	}
}


?>