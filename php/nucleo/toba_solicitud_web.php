<?php
/**
 * Solicitud pensada para contener el ciclo request-response http
 * La etapa de request se la denomina de 'eventos' 
 * La etapa de response se la denomina de 'servicios'
 * 
 * TODO Al servicio pdf le falta pedir por parametro que metodo llamar para construirlo
 * 
 * @package Centrales
 */
class toba_solicitud_web extends toba_solicitud
{
	protected $zona;			//Objeto que representa una zona que vincula varios items
	protected $cis;
	protected $cn;
	protected $tipo_pagina;
	protected $autocomplete = true;
	
	function __construct($info)
	{
		$this->info = $info;
		if (toba_editor::activado()) {
			toba_editor::set_item_solicitado(toba::memoria()->get_item_solicitado());
		}
		parent::__construct(toba::memoria()->get_item_solicitado(), toba::usuario()->get_id());
	}

	/**
	 * Crea la zona, carga los componentes, procesa los eventos y los servicios
	 */
	function procesar()
	{		
		$en_mantenimiento = (toba::proyecto()->get_parametro('proyecto', 'modo_mantenimiento', false) == 1) ;
		if ($en_mantenimiento) {
			$this->pre_proceso_servicio();	//Saca css y no queda alert pelado
			$msg = toba::proyecto()->get_parametro('proyecto', 'mantenimiento_mensaje');
			toba::notificacion()->info($msg);
			toba::notificacion()->mostrar();
		} else {
			try {				
				$this->crear_zona();
				$retrasar_headers = ($this->info['basica']['retrasar_headers']);
				//Chequeo si necesito enviar la clase para google analytics (necesito ponerlo aca para que salga como basico)
				//La otra forma es agregarlo a la generacion de consumos globales al medio de la generacion de HTML
				if ($this->hacer_seguimiento() && ! $this->es_item_login()) {
					toba_js::agregar_consumos_basicos(array('basicos/google_analytics'));
				}
								
				// Si la pagina retrasa el envio de headers, no mando los pre_servicios ahora
				if (! $retrasar_headers) {
					$this->pre_proceso_servicio();
				}
				$this->cargar_objetos();
				toba::cronometro()->marcar('Procesando Eventos');
				$this->procesar_eventos();
				if ($retrasar_headers) {
					$this->pre_proceso_servicio();
				}
				toba::cronometro()->marcar('Procesando Servicio');
				$this->procesar_servicios();
			}catch(toba_error $e) {
				toba::logger()->error($e, 'toba');
				$mensaje_debug = null;
				if (toba::logger()->modo_debug()) {
					$mensaje_debug = $e->get_mensaje_log();
				}				
				toba::notificacion()->error($e->get_mensaje(), $mensaje_debug);
				toba::notificacion()->mostrar();
			}
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
		
		toba::logger()->seccion("Iniciando componentes...", 'toba');
		$this->cis = array();		
		if (count($this->info['objetos']) > 0) {
			if (toba::proyecto()->get_parametro('navegacion_ajax')) {
				toba_ci::set_navegacion_ajax(true);
			}
			$i = 0;
			//Construye los objetos ci y el cn
			foreach ($this->info['objetos'] as $objeto) {
				if ($objeto['clase'] != 'toba_cn') {
					$this->cis[] = $this->cargar_objeto($objeto['clase'],$i); 
					$i++;
				} else {
					$this->cn = $this->cargar_objeto($objeto['clase'],0); 
					$this->objetos[$this->cn]->inicializar();
				}
			}
			//Asigna el cn a los cis			
			if (isset($this->cn)) {			
				foreach ($this->cis as $ci) {
					$this->objetos[$ci]->asignar_controlador_negocio( $this->objetos[$this->cn] );
			    } 
			}
		} else { 
			if ($this->info['basica']['item_act_accion_script'] == '') {
				throw new toba_error_def("Necesita asociar un objeto CI al ítem.");
			}
	    }
	}

	/**
	 * Inicializa los componentes y dispara la atención de eventos en forma recursiva
	 */
	protected function procesar_eventos()
	{
		//--Antes de procesar los eventos toda entrada UTF-8 debe ser pasada a ISO88591
		if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'UTF-8') !== false) {
			$_POST = array_a_latin1($_POST);
		}
		//-- Se procesan los eventos generados en el pedido anterior
		foreach ($this->cis as $ci) {
			$this->objetos[$ci]->inicializar();
			try {
				toba::logger()->seccion("Procesando eventos...", 'toba');
				$this->objetos[$ci]->disparar_eventos();
			} catch (toba_error $e) {
				toba::logger()->error($e, 'toba');
				$mensaje_debug = null;
				if (toba::logger()->modo_debug()) {
					$mensaje_debug = $e->get_mensaje_log();
				}				
				toba::notificacion()->error($e->get_mensaje(), $mensaje_debug);
				toba::notificacion()->set_titulo($e->get_titulo_ventana());
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
		//--- Si existe la zona se brinda la posibilidad de configurarla
		if ($this->hay_zona() && toba::zona()->cargada()) {
				toba::zona()->conf();
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
			throw new toba_error_seguridad("El servicio $servicio no está soportado");			
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
		$this->tipo_pagina()->encabezado();
	}
	
	
	protected function generar_html_botonera_sup($objetos)
	{
		//--- Se incluyen botones en la botonera de la operacion
		foreach ($objetos as $obj) {	
			if (is_a($obj, 'toba_ci')) {
				if ($obj->es_botonera_en_barra_item()) {
					$obj->pantalla()->generar_botones('enc-botonera');					
				}
			}
			$this->generar_html_botonera_sup($obj->get_dependencias());
		}		
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
		
		//--- Se incluyen botones en la botonera de la operacion
		$this->generar_html_botonera_sup($objetos);
		
		$this->tipo_pagina()->post_encabezado();
		
		$this->tipo_pagina()->pre_contenido();
		
		//--- Abre el formulario
		$accion = $this->info['basica']['item_act_accion_script'];
		if ($accion == '') {
			$extra = "onsubmit='return false;'";
			if (! $this->autocomplete) {
				$extra .= " autocomplete='off'";
			}
			echo toba_form::abrir("formulario_toba", toba::vinculador()->get_url(), $extra);
			toba_manejador_sesiones::enviar_csrf_hidden();
			
			//HTML
			foreach ($objetos as $obj) {
				//-- Librerias JS necesarias
				toba_js::cargar_consumos_globales($obj->get_consumo_javascript());
				//-- HTML propio del objeto
				$obj->generar_html();
			}
			
			//Javascript
			echo toba_js::abrir();
			try {
				toba_js::cargar_definiciones_runtime();
				foreach ($objetos as $obj) {
					$objeto_js = $obj->generar_js();
					echo "\n". toba::escaper()->escapeJs($objeto_js).".iniciar();\n";
				}
			} catch (toba_error $e) {
				toba::logger()->error($e, 'toba');
				$mensaje_debug = null;
				if (toba::logger()->modo_debug()) {
					$mensaje_debug = $e->get_mensaje_log();
				}						
				toba::notificacion()->error($e->get_mensaje(), $mensaje_debug);
			}
			echo toba_js::cerrar();		
				
			//--- Fin del form y parte inferior del tipo de página
			echo toba_form::cerrar();
		} else {
			echo toba_js::abrir();
			toba_js::cargar_definiciones_runtime();
			echo toba_js::cerrar();
			include($accion);	
		}

		$this->tipo_pagina()->post_contenido();
		// Carga de componentes JS genericos
		echo toba_js::abrir();
		toba::vinculador()->generar_js();
		toba::notificacion()->mostrar(false);
		toba::acciones_js()->generar_js();
		$this->generar_analizador_estadistico();
		echo toba_js::cerrar();
		
		//--- Parte inferior de la zona
		if ($this->hay_zona() &&  $this->zona->cargada()) {
			$this->zona->generar_html_barra_inferior();
		}
       	$this->tipo_pagina()->pie();
	}
	
	/**
	 * Genera una salida en formato pdf
	 */
	
	protected function servicio__vista_pdf( $objetos )
	{
		$salida = new toba_vista_pdf();
		$salida->asignar_objetos( $objetos );
		$salida->generar_salida();
		$salida->enviar_archivo();
	}
	
	protected function servicio__vista_xml( $objetos )
	{
		$salida = new toba_vista_xml();
		$salida->asignar_objetos( $objetos );
		$salida->generar_salida();
		$salida->enviar_archivo();
	}

	protected function servicio__vista_xslfo( $objetos )
	{
		$salida = new toba_vista_xslfo();
		$salida->asignar_objetos( $objetos );
		$salida->generar_salida();
		$salida->enviar_archivo();
	}
	
	protected function servicio__vista_jasperreports( $objetos )
	{
		$salida = new toba_vista_jasperreports();		
		$salida->asignar_objetos($objetos);
		$salida->generar_salida();
		$salida->enviar_archivo();
	}	
	
	protected function servicio__vista_excel($objetos)
	{
		$salida = new toba_vista_excel();
		$salida->asignar_objetos( $objetos );
		$salida->generar_salida();
		$salida->enviar_archivo();
	}
	

	/**
	 * Genera una salida html pensada para impresión
	 */
	protected function servicio__vista_toba_impr_html( $objetos )
	{
		$pm = toba::proyecto()->get_parametro('pm_impresion');
		$clase = toba::proyecto()->get_parametro('salida_impr_html_c');
		$archivo = toba::proyecto()->get_parametro('salida_impr_html_a');
		if ( $clase && $archivo ) {
			//El proyecto posee un objeto de impresion HTML personalizado
			$punto = toba::puntos_montaje()->get_por_id($pm);
			$path  = $punto->get_path_absoluto().'/'.$archivo;
			require_once($path);
			$salida = new $clase();
		} else {
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
		echo "[--toba--]";		
		//-- Se reenvia el encabezado
		$this->tipo_pagina()->barra_superior();
		echo "</div>";

		//--- Parte superior de la zona
		if (toba::solicitud()->hay_zona() && toba::zona()->cargada()) {
			toba::zona()->generar_html_barra_superior();
		}
		//--- Se incluyen botones en la botonera de la operacion
		$this->generar_html_botonera_sup($objetos);		
		echo "[--toba--]";
		$ok = true;
		try {
			//--- Se envia el HTML
			foreach ($objetos as $objeto) {
				$objeto->generar_html();
			}	
		} catch(toba_error $e) {
			$ok = false;
			toba::logger()->error($e, 'toba');
			$mensaje = $e->get_mensaje();
			$mensaje_debug = null;
			if (toba::logger()->modo_debug()) {
				$mensaje_debug = $e->get_mensaje_log();
			}
			toba::notificacion()->error($mensaje, $mensaje_debug);
		}

		
		echo "[--toba--]";
		
		//-- Se envia info de debug
		if ( toba_editor::modo_prueba() ) {
			$item = toba::solicitud()->get_datos_item('item');
			$accion = toba::solicitud()->get_datos_item('item_act_accion_script');
			toba_editor::generar_zona_vinculos_item($item, $accion, false);
		}		
		echo "[--toba--]";

		//--- Se envian los consumos js		
		$consumos = array();
		foreach ($objetos as $objeto) {
			$consumos = array_merge($consumos, $objeto->get_consumo_javascript());
		}
		echo "toba.incluir(".toba_js::arreglo($consumos, false).");\n"; 
		echo "[--toba--]";
				
		//--- Se envia el javascript
		//Se actualiza el vinculo del form
		$autovinculo = toba::vinculador()->get_url();
		echo "document.formulario_toba.action='$autovinculo'\n";
		toba::vinculador()->generar_js();
		toba_js::cargar_definiciones_runtime();
		if ($ok) {
			try {
				foreach ($objetos as $objeto) {
					//$objeto->servicio__html_parcial();
					$objeto_js = $objeto->generar_js();
					echo "\nwindow." . toba::escaper()->escapeJs($objeto_js).".iniciar();\n";
				}
			} catch (toba_error $e) {
				toba::logger()->error($e, 'toba');
				$mensaje_debug = null;
				if (toba::logger()->modo_debug()) {
					$mensaje_debug = $e->get_mensaje_log();
				}				
				toba::notificacion()->error($e->get_mensaje(), $mensaje_debug);
			}
		}
		toba::notificacion()->mostrar(false);
		toba::acciones_js()->generar_js();
		$this->generar_analizador_estadistico();
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
				throw new toba_error_def("Las cascadas sólo admiten un objeto destino (actualmente: $actual)");
			}
			$objetos[0]->servicio__cascadas_efs();
		} catch(toba_error $e) {
			toba::logger()->error($e, 'toba');
			$mensaje_debug = null;
			if (toba::logger()->modo_debug()) {
				$mensaje_debug = $e->get_mensaje_log();
			}				
			toba::notificacion()->error($e->get_mensaje(), $mensaje_debug);
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
	
	
	protected function servicio__ajax($objetos)
	{
		toba::memoria()->desactivar_reciclado();
		try {
			if (count($objetos) != 1) {
				$actual = count($objetos);
				throw new toba_error_def("La invocacion AJAX sólo admite un objeto destino (actualmente: $actual)");
			}
			$objetos[0]->servicio__ajax();
		} catch(toba_error $e) {
			toba::logger()->error($e, 'toba');
			$mensaje_debug = null;
			if (toba::logger()->modo_debug()) {
				$mensaje_debug = $e->get_mensaje_log();
			}				
			toba::notificacion()->error($e->get_mensaje(), $mensaje_debug);
		}
	}
	

	function registrar()
	{
		parent::registrar();
		$id_sesion = toba::manejador_sesiones()->get_id_sesion();		
		if($this->registrar_db && isset($id_sesion)){
			toba::instancia()->registrar_solicitud_browser(	$this->info['basica']['item_proyecto'], 
															$this->id, 
															toba::proyecto()->get_id(),
															toba::manejador_sesiones()->get_id_sesion(),
															$_SERVER['REMOTE_ADDR']);
		}
 	}
 	
 	
 	/**
 	 * Indica si la operacion actual permite auto 
 	 */
 	function set_autocomplete($set)
 	{
 		$this->autocomplete = $set;
 	}

	/**
	 *@private
	 */
	function hacer_seguimiento()
	{
		$cod_ga = toba::proyecto()->get_parametro('codigo_ga_tracker');
		$hacer_seguimiento =  (isset($cod_ga) && trim($cod_ga) != '') ;
		return $hacer_seguimiento;
	}

	/**
	 *@private
	 * @return <type>
	 */
	function es_item_login()
	{
		$es_login = (toba::proyecto()->get_parametro('item_pre_sesion') == $this->item[1]);
		return $es_login;
	}
	
	/**
	 * @private
	 */
	function generar_analizador_estadistico()
	{
		$cod_ga = toba::proyecto()->get_parametro('codigo_ga_tracker');
		if (isset($cod_ga) && trim($cod_ga) != '') {		//No llamo a la funcion xq ya tengo el valor aca
			$escapador = toba::escaper();
			if (! $this->es_item_login()) {
				echo "estadista.set_codigo('". $escapador->escapeJs($cod_ga)."'); \n";		
				echo "estadista.iniciar(); \n";
				echo "estadista.add_operacion('". $escapador->escapeJs($this->item[1])."'); \n";
				echo "estadista.add_titulo('". $escapador->escapeJs( $this->get_datos_item('item_nombre'))."'); \n";
				$ventana = toba::proyecto()->get_parametro('sesion_tiempo_no_interac_min');
				if ($ventana != 0) {
					$ventana *= 60; //$ventana esta en minutos y necesito segundos
					echo "estadistica.set_timeout('". $escapador->escapeJs($ventana)."'); \n";
				}
				echo "estadista.trace()";
			} else { //Es item de login
				//Tengo que cerrar el tag js que viene abierto de antes
				echo "</script><script type=\"text/javascript\">"			
				."var gaJsHost = ((\"https:\" == document.location.protocol) ? \"https://ssl.\" : \"http://www.\");"
				."document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));"
				."</script>"
				."<script type=\"text/javascript\">"
				."try {"
				."var pageTracker = _gat._getTracker(\"". $escapador->escapeJs($cod_ga)."\");"
				."pageTracker._trackPageview();"
				."} catch(err) {}</script><script type=\"text/javascript\">";
			}
		}
	}

	//----------------------------------------------------------
	//---------------------- TIPO de PAGINA --------------------
	//----------------------------------------------------------

	/**
	 * @return toba_tp_normal
	 */
	function tipo_pagina()
	{
		if (! isset($this->tipo_pagina)) {
			//Carga el TP a demanda
			if (!class_exists($this->info['basica']['tipo_pagina_clase'])){
				if ($this->info['basica']['tipo_pagina_archivo']) {
					$punto = toba::puntos_montaje()->get_por_id($this->info['basica']['tipo_pagina_punto_montaje']);
					$path  = $punto->get_path_absoluto().'/'.$this->info['basica']['tipo_pagina_archivo'];
					require_once($path);
				}
			}
			$this->tipo_pagina = new $this->info['basica']['tipo_pagina_clase']();
		}
		return $this->tipo_pagina;
	}
	
	//----------------------------------------------------------------
	//-- Consumo de solicitudes desde los casos de testeo
	//----------------------------------------------------------------
	
	function inicializacion_pasiva()
	{
		$this->crear_zona();			
		$this->cargar_objetos();
	}

	function cn()
	{
		return $this->objetos[$this->cn];	
	}
	
	function ci()
	{
		return $this->objetos[$this->cis[0]];
	}
	
}
?>
