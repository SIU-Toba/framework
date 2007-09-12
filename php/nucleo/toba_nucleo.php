<?php
/**
 * Clase que brinda las puertas de acceso al núcleo de toba
 * @package Centrales
 */
class toba_nucleo
{
	static private $instancia;
	static private $dir_compilacion;
	static private $indice_archivos;
	static private $path;	
	private $solicitud;
	private $medio_acceso;
	private $solicitud_en_proceso = false;
	
	private function __construct()
	{
		self::$indice_archivos = self::get_indice_archivos();
		$this->cargar_includes_basicos();
		spl_autoload_register(array('toba_nucleo', 'cargador_clases'));
		toba::cronometro();		
	}
	
	static function instancia()
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new toba_nucleo();	
		}
		return self::$instancia;	
	}	

	/**
	 * @return solicitud
	 */
	function get_solicitud()
	{
		return $this->solicitud;
	}
		
	/**
	 * Punto de entrada http al nucleo
	 */
	function acceso_web()
	{
		try {
			$this->iniciar_contexto_ejecucion();
		    toba_http::headers_standart();
			try {
				$this->solicitud = $this->cargar_solicitud_web();
				$this->solicitud_en_proceso = true;
				$this->solicitud->procesar();
			} catch( toba_reset_nucleo $e ) {
				toba::logger()->debug('Se recargo el nucleo','toba');
				//El item puede redireccionar?
				if ( !$this->solicitud->get_datos_item('redirecciona') ) {
					throw new toba_error('ERROR: La operación no esta habilitada para provocar redirecciones.');
				}
				//TRAP para forzar la recarga de solicitud
				$this->solicitud_en_proceso = false;
				toba::memoria()->limpiar_memoria();
				$item_nuevo = $e->get_item();
				toba::memoria()->set_item_solicitado( $item_nuevo );				
				$this->solicitud = $this->cargar_solicitud_web();
				$this->solicitud->procesar();
			}
			$this->solicitud->registrar();
			$this->solicitud->finalizar_objetos();
			$this->finalizar_contexto_ejecucion();
		} catch (Exception $e) {
			toba::logger()->crit($e, 'toba');
			echo $e->getMessage() . "\n\n";
		}
		toba::logger()->debug('Tiempo utilizado: ' . toba::cronometro()->tiempo_acumulado() . ' seg.');
		toba::logger()->guardar();
	}

	/**
	 * Punto de entrada desde la consola al nucleo
	 */	
	function acceso_consola($instancia, $proyecto, $item, $usuario)
	{
		$estado_proceso = null;
		try {
			define('apex_pa_instancia', $instancia);
			define('apex_pa_proyecto' , $proyecto);
			$this->iniciar_contexto_ejecucion();			
			//$this->solicitud = new toba_solicitud_consola($proyecto, $item, $usuario);
			$this->solicitud = toba_constructor::get_runtime(array('proyecto'=>$proyecto, 'componente'=>$item), 'toba_item');
			$this->solicitud->procesar();	//Se llama a la ACTIVIDAD del ITEM
			$this->solicitud->registrar();
			$this->solicitud->finalizar_objetos();
			$estado_proceso = $this->solicitud->get_estado_proceso();
		} catch (toba_error $e) {
			toba::logger()->crit($e, 'toba');
			echo $e;
		}
		$this->finalizar_contexto_ejecucion();
		toba::logger()->debug('Estado Proceso: '.$estado_proceso, 'toba');
		toba::logger()->debug('Tiempo utilizado: ' . toba::cronometro()->tiempo_acumulado() . ' seg.');
		toba::logger()->guardar();
		exit($estado_proceso);
	}
		
	function solicitud_en_proceso()
	{
		return $this->solicitud_en_proceso;
	}

	/**
	 * Carga la SOLICITUD actual. Se determina el item y se controla el acceso al mismo
	 */
	function cargar_solicitud_web()
	{
		if (toba::manejador_sesiones()->existe_sesion_activa()) {		// Estoy dentro de una SESION
			$item = $this->get_id_item('item_inicio_sesion', false, true);
			if(!$item[0]||!$item[1]) {
				throw new toba_error('ERROR: No esta definido el ITEM de INICIO de sesion');	
			}			
			$this->iniciar_contexto_solicitud($item);
			$solicitud = toba_constructor::get_runtime(array('proyecto'=>$item[0],'componente'=>$item[1]), 'toba_item');
			if (!$solicitud->es_item_publico()) {
				$this->autorizar_acceso_item($item);
			}
			return $solicitud;
		} else {														// Estoy fuera de la sesion. Solo se puede acceder a lo publico
			$mensaje_error = 'La seccion no esta activa. Solo es posible acceder items PUBLICOS.';
			$item = $this->get_id_item('item_pre_sesion');
			if(!$item[0]||!$item[1]) {
				throw new toba_error('ERROR: No esta definido el ITEM de LOGIN');	
			}			
			$this->iniciar_contexto_solicitud($item);
			$solicitud = toba_constructor::get_runtime(array('proyecto'=>$item[0],'componente'=>$item[1]), 'toba_item');
			if (!$solicitud->es_item_publico()) {
				// Si se arrastra una URL previa despues de finalizar la sesion y se refresca la pagina
				// el nucleo trata de cargar un item explicito por URL. El mismo no va a ser publico...
				// Esto apunta a solucionar ese error: Blanqueo el item solicitado y vuelvo a intentar.
				// (NOTA: esto puede ocultar la navegacion entre items supuestamente publicos)
				if ( toba::memoria()->get_item_solicitado() ) {
					toba::logger()->debug('Fallo la carga de una operación publica. Se intenta con la operación predeterminada', 'toba');
					toba::memoria()->set_item_solicitado(null);					
					$item = $this->get_id_item('item_pre_sesion');
					$this->iniciar_contexto_solicitud($item);
					$solicitud = toba_constructor::get_runtime(array('proyecto'=>$item[0],'componente'=>$item[1]), 'toba_item');
					if (!$solicitud->es_item_publico()) {
						throw new toba_error($mensaje_error);		
					}
				} else {
					throw new toba_error($mensaje_error);				
				}
			}
			return $solicitud;
		}		
	}

	/**
	 * Averigua el ITEM ACTUAL. Si no existe y puede busca el ITEM PREDEFINIDO pasado como parametro
	 */
	protected function get_id_item($predefinido=null,$forzar_predefinido=false, $evitar_pre_sesion=false)
	{
		$item = toba::memoria()->get_item_solicitado();
		if (!$item || ($evitar_pre_sesion && 
						$item[0] == toba::proyecto()->get_id() &&
						$item[1] == toba::proyecto()->get_parametro('item_pre_sesion'))) {
			if(isset($predefinido)){
				$item[0] = toba::proyecto()->get_id();
				$item[1] = toba::proyecto()->get_parametro($predefinido);		
				toba::memoria()->set_item_solicitado($item);
			} else {
				throw new toba_error('NUCLEO: No es posible determinar la operación a cargar');
			}
		}
		return $item;
	}
	
	protected function autorizar_acceso_item($item)
	{
		if (! toba::proyecto()->puede_grupo_acceder_item($item[0], $item[1]) ) {
			throw new toba_error_autorizacion('El usuario no posee permisos para acceder al item solicitado.');
		}
	}

	protected function iniciar_contexto_ejecucion()
	{
		$this->controlar_requisitos_basicos();
		agregar_dir_include_path( toba::proyecto()->get_path_php() );
		toba::contexto_ejecucion()->conf__inicial();
		toba::manejador_sesiones()->iniciar();
	}

	protected function finalizar_contexto_ejecucion()
	{
		toba::manejador_sesiones()->finalizar();
		toba::contexto_ejecucion()->conf__final();
		$this->solicitud->guardar_cronometro();
	}
	
	function controlar_requisitos_basicos()
	{
		if (php_sapi_name() !== 'cli' && get_magic_quotes_gpc()) {
			throw new toba_error("Necesita desactivar las 'magic_quotes' en el servidor (ver http://www.php.net/manual/es/security.magicquotes.disabling.php)");
		}
	}

	//--------------------------------------------------------------------------
	//	Carga de archivos
	//--------------------------------------------------------------------------

	static function toba_dir()
	{
		if (! isset(self::$path)) {
			$dir = dirname(__FILE__);
			self::$path = substr($dir,0, -11);
		}
		return self::$path;
	}

	/**
	*	Carga de includes basicos
	*/
	protected function cargar_includes_basicos()
	{
		//Las funciones globales no puden cargarse con el autoload.
		if ($this->utilizar_archivos_compilados()) {
			require_once( self::toba_dir() . '/php/nucleo/toba_motor.php');	
		} else {
			foreach(self::get_includes_funciones_globales() as $archivo ) {
				require_once( self::toba_dir() . $archivo);
			}
		}
	}

	/**
	*	Indica si hay que usar el nucleo resumido a un archivo o el esquema de includes usual
	*	@ignore
	*/
	function utilizar_archivos_compilados()
	{
		return (defined('apex_pa_archivos_compilados') && apex_pa_archivos_compilados);
	}
		
	/**
	*	En el modo compilado, carga los metadatos necesarios para la solicitud actual
	*	@ignore
	*/
	protected function iniciar_contexto_solicitud($item)
	{
		if ($this->utilizar_metadatos_compilados() ) {
			// Arbol de componentes de la operacion
			$id = toba_manejador_archivos::nombre_valido( $item[1] );
			require_once( self::get_directorio_compilacion() . '/oper/toba_mc_oper__' . $id . '.php' );
			// Datos BASICOS
			require_once( self::get_directorio_compilacion() . '/gene/toba_mc_gene__basicos.php' );
			$grupos_acceso = toba::manejador_sesiones()->get_grupos_acceso();
			foreach( $grupos_acceso as $grupo ) {
				require_once( self::get_directorio_compilacion() . '/gene/toba_mc_gene__grupo_'.$grupo.'.php' );				
			}
		}
	}

	/**
	*	Indica si hay que usar metadatos compilados o provenientes de la base.
	*	(La compilacion se activa solo sobre el proyecto primario).
	*	@ignore
	*/
	function utilizar_metadatos_compilados($proyecto=null)
	{
		$proyecto = isset($proyecto) ? $proyecto : toba_proyecto::get_id();
		$flag_compilacion = (defined('apex_pa_metadatos_compilados') && apex_pa_metadatos_compilados);
		$proyecto_compilado = ($proyecto == apex_pa_proyecto);
		return ($flag_compilacion && $proyecto_compilado);
	}

	static function get_directorio_compilacion()
	{
		if(!self::$dir_compilacion) self::$dir_compilacion = toba_proyecto::get_path() . '/metadatos_compilados';
		return self::$dir_compilacion;
	}

	static function cargador_clases($clase)
	{
		//Busqueda de un METADATO compilado!!
		if (strpos($clase,'toba_mc_')!==false) {
			$subdir = substr($clase,8,4);
			$archivo = self::get_directorio_compilacion() . '/' . $subdir . '/' . $clase . '.php';
			require_once($archivo);
			return;
		}
		//Carga de las clases del nucleo
		if(isset(self::$indice_archivos[$clase])) {
			require_once( self::toba_dir() .'/php/'. self::$indice_archivos[$clase]);
		}
	}

	/**
	*	@ignore
	*/
	static function get_includes_funciones_globales()
	{
		return array( 
			'/php/lib/toba_varios.php',
			'/php/lib/toba_parseo.php',
			'/php/lib/toba_texto.php',
			'/php/lib/toba_sql.php',
			'/php/nucleo/lib/toba_db.php',
			'/php/nucleo/lib/interface/toba_ei.php',
			'/php/nucleo/lib/interface/toba_formateo.php');
	}

	/**
	*	@ignore
	*/
	static function get_indice_archivos()
	{
		return array(
			'toba'									=> 'nucleo/toba.php',
			'toba_solicitud'						=> 'nucleo/toba_solicitud.php',
			'toba_solicitud_web'					=> 'nucleo/toba_solicitud_web.php',
			'toba_solicitud_accion'					=> 'nucleo/toba_solicitud_accion.php',
			'toba_solicitud_consola'				=> 'nucleo/toba_solicitud_consola.php',
			'toba_cargador'							=> 'nucleo/componentes/toba_cargador.php',
			'toba_constructor'						=> 'nucleo/componentes/toba_constructor.php',
			'toba_admin_fuentes'					=> 'nucleo/lib/toba_admin_fuentes.php',
			'toba_contexto_ejecucion'				=> 'nucleo/lib/toba_contexto_ejecucion.php',
			'toba_cronometro'						=> 'nucleo/lib/toba_cronometro.php',
			'toba_dba'								=> 'nucleo/lib/toba_dba.php',
			'toba_debug'							=> 'nucleo/lib/toba_debug.php',
			'toba_editor'							=> 'nucleo/lib/toba_editor.php',
			'toba_error'							=> 'nucleo/lib/toba_error.php',
			'toba_error_db'							=> 'nucleo/lib/toba_error.php',
			'toba_error_usuario'					=> 'nucleo/lib/toba_error.php',
			'toba_error_def'						=> 'nucleo/lib/toba_error.php',
			'toba_error_permisos'					=> 'nucleo/lib/toba_error.php',
			'toba_error_autenticacion'				=> 'nucleo/lib/toba_error.php',
			'toba_error_autorizacion'				=> 'nucleo/lib/toba_error.php',
			'toba_error_validacion' 				=> 'nucleo/lib/toba_error.php',
			'toba_error_ini_sesion'					=> 'nucleo/lib/toba_error.php',
			'toba_reset_nucleo'		 				=> 'nucleo/lib/toba_error.php',
			'toba_fuente_datos'						=> 'nucleo/lib/toba_fuente_datos.php',
			'toba_http'								=> 'nucleo/lib/toba_http.php',
			'toba_instalacion'						=> 'nucleo/lib/toba_instalacion.php',
			'toba_instancia'						=> 'nucleo/lib/toba_instancia.php',
			'toba_interface_contexto_ejecucion'		=> 'nucleo/lib/toba_interface_contexto_ejecucion.php',
			'toba_interface_usuario'				=> 'nucleo/lib/toba_interface_usuario.php',
			'toba_js'								=> 'nucleo/lib/toba_js.php',
			'toba_logger'							=> 'nucleo/lib/toba_logger.php',
			'toba_manejador_sesiones'				=> 'nucleo/lib/toba_manejador_sesiones.php',
			'toba_memoria'							=> 'nucleo/lib/toba_memoria.php',
			'toba_mensajes'							=> 'nucleo/lib/toba_mensajes.php',
			'toba_notificacion'						=> 'nucleo/lib/toba_notificacion.php',
			'toba_parser_ayuda'						=> 'nucleo/lib/toba_parser_ayuda.php',
			'toba_derechos'							=> 'nucleo/lib/toba_derechos.php',
			'toba_proyecto'							=> 'nucleo/lib/toba_proyecto.php',
			'toba_proyecto_db'						=> 'nucleo/lib/toba_proyecto_db.php',
			'toba_puntos_control'					=> 'nucleo/lib/toba_puntos_control.php',
			'toba_recurso'							=> 'nucleo/lib/toba_recurso.php',
			'toba_sesion'							=> 'nucleo/lib/toba_sesion.php',
			'toba_usuario'							=> 'nucleo/lib/toba_usuario.php',
			'toba_usuario_anonimo'					=> 'nucleo/lib/toba_usuario_anonimo.php',
			'toba_usuario_basico'					=> 'nucleo/lib/toba_usuario_basico.php',
			'toba_usuario_no_autenticado'			=> 'nucleo/lib/toba_usuario_no_autenticado.php',
			'toba_vinculador'						=> 'nucleo/lib/toba_vinculador.php',
			'toba_vinculo'							=> 'nucleo/lib/toba_vinculo.php',
			'toba_zona'								=> 'nucleo/lib/toba_zona.php',
			'toba_menu'								=> 'nucleo/menu/toba_menu.php',
			'toba_menu_css'							=> 'nucleo/menu/toba_menu_css.php',
			'toba_menu_yui'							=> 'nucleo/menu/toba_menu_yui.php',
			'toba_tipo_pagina'						=> 'nucleo/tipo_pagina/toba_tipo_pagina.php',
			'toba_tp_basico'						=> 'nucleo/tipo_pagina/toba_tp_basico.php',
			'toba_tp_basico_titulo'					=> 'nucleo/tipo_pagina/toba_tp_basico_titulo.php',
			'toba_tp_logon'							=> 'nucleo/tipo_pagina/toba_tp_logon.php',
			'toba_tp_popup'							=> 'nucleo/tipo_pagina/toba_tp_popup.php',
			'toba_tp_normal'						=> 'nucleo/tipo_pagina/toba_tp_normal.php',
			'toba_db'								=> 'lib/db/toba_db.php',
			'toba_db_postgres7'						=> 'lib/db/toba_db_postgres7.php',
			'toba_db_mysql'							=> 'lib/db/toba_db_mysql.php',
			'toba_db_odbc'							=> 'lib/db/toba_db_odbc.php',
			'toba_db_informix'						=> 'lib/db/toba_db_informix.php',
 			//-----------------------------------------------------------------------------------------------
			//--------- COMPONENTES -------------------------------------------------------------------------
 			//-----------------------------------------------------------------------------------------------
			'toba_item_def'							=> 'nucleo/componentes/definicion/toba_item_def.php',
			'toba_item_info'						=> 'modelo/info/componentes/toba_item_info.php',
			'toba_componente'						=> 'nucleo/componentes/toba_componente.php',
			'toba_componente_def'					=> 'nucleo/componentes/definicion/toba_componente_def.php',
			'toba_componente_info'					=> 'modelo/info/componentes/toba_componente_info.php',
			'toba_ei'								=> 'nucleo/componentes/interface/toba_ei.php',
			'toba_ei_def'							=> 'nucleo/componentes/definicion/toba_ei_def.php',
			'toba_ei_info'							=> 'modelo/info/componentes/toba_ei_info.php',
			//- CI -
			'toba_ci'								=> 'nucleo/componentes/interface/toba_ci.php',
			'toba_ci_def'							=> 'nucleo/componentes/definicion/toba_ci_def.php',
			'toba_ci_info'							=> 'modelo/info/componentes/toba_ci_info.php',
			'toba_ei_pantalla'						=> 'nucleo/componentes/interface/toba_ei_pantalla.php',
			'toba_ci_pantalla_info'					=> 'modelo/info/componentes/toba_ci_pantalla_info.php',
			//- FORM -
			'toba_ei_formulario'					=> 'nucleo/componentes/interface/toba_ei_formulario.php',
			'toba_ei_formulario_def'				=> 'nucleo/componentes/definicion/toba_ei_formulario_def.php',
			'toba_ei_formulario_info'				=> 'modelo/info/componentes/toba_ei_formulario_info.php',
			//- ML -
			'toba_ei_formulario_ml'					=> 'nucleo/componentes/interface/toba_ei_formulario_ml.php',
			'toba_ei_formulario_ml_def'				=> 'nucleo/componentes/definicion/toba_ei_formulario_ml_def.php',
			'toba_ei_formulario_ml_info'			=> 'modelo/info/componentes/toba_ei_formulario_ml_info.php',
			//- FILTRO -
			'toba_ei_filtro'						=> 'nucleo/componentes/interface/toba_ei_filtro.php',
			'toba_ei_filtro_def'					=> 'nucleo/componentes/definicion/toba_ei_filtro_def.php',
			'toba_ei_filtro_info'					=> 'modelo/info/componentes/toba_ei_filtro_info.php',
			//- CUADRO -
			'toba_ei_cuadro'						=> 'nucleo/componentes/interface/toba_ei_cuadro.php',
			'toba_ei_cuadro_def'					=> 'nucleo/componentes/definicion/toba_ei_cuadro_def.php',
			'toba_ei_cuadro_info'					=> 'modelo/info/componentes/toba_ei_cuadro_info.php',
			//- ARBOL -
			'toba_ei_arbol'							=> 'nucleo/componentes/interface/toba_ei_arbol.php',
			'toba_ei_arbol_def'						=> 'nucleo/componentes/definicion/toba_ei_arbol_def.php',
			'toba_ei_arbol_info'					=> 'modelo/info/componentes/toba_ei_arbol_info.php',
			//- ARCHIVOS -
			'toba_ei_archivos'						=> 'nucleo/componentes/interface/toba_ei_archivos.php',
			'toba_ei_archivos_def'					=> 'nucleo/componentes/definicion/toba_ei_archivos_def.php',
			'toba_ei_archivos_info'					=> 'modelo/info/componentes/toba_ei_archivos_info.php',
			//- CALENDARIO -
			'toba_ei_calendario'					=> 'nucleo/componentes/interface/toba_ei_calendario.php',
			'toba_ei_calendario_def'				=> 'nucleo/componentes/definicion/toba_ei_calendario_def.php',
			'toba_ei_calendario_info'				=> 'modelo/info/componentes/toba_ei_calendario_info.php',
			//- ESQUEMA -
			'toba_ei_esquema'						=> 'nucleo/componentes/interface/toba_ei_esquema.php',
			'toba_ei_esquema_def'					=> 'nucleo/componentes/definicion/toba_ei_esquema_def.php',
			'toba_ei_esquema_info'					=> 'modelo/info/componentes/toba_ei_esquema_info.php',
			//- RELACION -
			'toba_datos_relacion'					=> 'nucleo/componentes/persistencia/toba_datos_relacion.php',
			'toba_datos_relacion_def'				=> 'nucleo/componentes/definicion/toba_datos_relacion_def.php',
			'toba_datos_relacion_info'				=> 'modelo/info/componentes/toba_datos_relacion_info.php',
			'toba_ap_relacion'						=> 'nucleo/componentes/persistencia/toba_ap.php',
			'toba_ap_relacion_db'					=> 'nucleo/componentes/persistencia/toba_ap_relacion_db.php',
			'toba_ap_relacion_db_info'				=> 'modelo/info/componentes/toba_ap_relacion_db_info.php',
			//- TABLA -
			'toba_datos_tabla'						=> 'nucleo/componentes/persistencia/toba_datos_tabla.php',
			'toba_datos_tabla_def'					=> 'nucleo/componentes/definicion/toba_datos_tabla_def.php',
			'toba_datos_tabla_info'					=> 'modelo/info/componentes/toba_datos_tabla_info.php',
			'toba_ap_tabla'							=> 'nucleo/componentes/persistencia/toba_ap.php',
			'toba_ap_tabla_db'						=> 'nucleo/componentes/persistencia/toba_ap_tabla_db.php',
			'toba_ap_tabla_db_mt'					=> 'nucleo/componentes/persistencia/toba_ap_tabla_db_mt.php',
			'toba_ap_tabla_db_s'					=> 'nucleo/componentes/persistencia/toba_ap_tabla_db_s.php',
			'toba_ap_tabla_db_info'					=> 'modelo/info/componentes/toba_ap_tabla_db_info.php',
			//- CN -
			'toba_cn'								=> 'nucleo/componentes/negocio/toba_cn.php',
			'toba_cn_def'							=> 'nucleo/componentes/definicion/toba_cn_def.php',
			'toba_cn_info'							=> 'modelo/info/componentes/toba_cn_info.php',
			//- Planes de generacion de operaciones -
			'toba_asistente_def'					=> 'nucleo/componentes/definicion/toba_asistente_def.php',
			'toba_asistente_abms_def'				=> 'nucleo/componentes/definicion/toba_asistente_abms_def.php',
			'toba_asistente_grilla_def'				=> 'nucleo/componentes/definicion/toba_asistente_grilla_def.php',
			'toba_catalogo_asistentes'				=> 'modelo/asistentes/toba_catalogo_asistentes.php',
			'toba_asistente'						=> 'modelo/asistentes/toba_asistente.php',
			'toba_asistente_abms'					=> 'modelo/asistentes/toba_asistente_abms.php',
			'toba_asistente_grilla'					=> 'modelo/asistentes/toba_asistente_grilla.php',
			'toba_ci_molde'                         => 'modelo/moldes_metadatos/toba_ci_molde.php',                       
			'toba_datos_relacion_molde'             => 'modelo/moldes_metadatos/toba_datos_relacion_molde.php',           
			'toba_datos_tabla_molde'                => 'modelo/moldes_metadatos/toba_datos_tabla_molde.php',              
			'toba_ei_cuadro_molde'                  => 'modelo/moldes_metadatos/toba_ei_cuadro_molde.php',                
			'toba_ei_filtro_molde'                  => 'modelo/moldes_metadatos/toba_ei_filtro_molde.php',                
			'toba_ei_formulario_ml_molde'           => 'modelo/moldes_metadatos/toba_ei_formulario_ml_molde.php',         
			'toba_ei_formulario_molde'              => 'modelo/moldes_metadatos/toba_ei_formulario_molde.php',            
			'toba_item_molde'                       => 'modelo/moldes_metadatos/toba_item_molde.php',                     
			'toba_molde_elemento'                   => 'modelo/moldes_metadatos/toba_molde_elemento.php',                 
			'toba_molde_elemento_componente'        => 'modelo/moldes_metadatos/toba_molde_elemento_componente.php',      
			'toba_molde_elemento_componente_datos'  => 'modelo/moldes_metadatos/toba_molde_elemento_componente_datos.php',
			'toba_molde_elemento_componente_ei'   	=> 'modelo/moldes_metadatos/toba_molde_elemento_componente_ei.php',   
			'toba_molde_ef'   						=> 'modelo/moldes_metadatos/toba_molde_ef.php',   
			'toba_molde_evento' 					=> 'modelo/moldes_metadatos/toba_molde_evento.php',   
			'toba_molde_cuadro_col'					=> 'modelo/moldes_metadatos/toba_molde_cuadro_col.php',   
			'toba_molde_datos_tabla_col' 			=> 'modelo/moldes_metadatos/toba_molde_datos_tabla_col.php',   
 			'toba_codigo_elemento' 					=> 'modelo/moldes_codigo/toba_codigo_elemento.php',
			'toba_codigo_clase'						=> 'modelo/moldes_codigo/toba_codigo_clase.php',
 			'toba_codigo_metodo' 					=> 'modelo/moldes_codigo/toba_codigo_metodo.php',
 			'toba_codigo_metodo_js' 				=> 'modelo/moldes_codigo/toba_codigo_metodo_js.php',
 			'toba_codigo_metodo_php' 				=> 'modelo/moldes_codigo/toba_codigo_metodo_php.php',
 			'toba_codigo_propiedad_php'				=> 'modelo/moldes_codigo/toba_codigo_propiedad_php.php',
 			'toba_codigo_separador' 				=> 'modelo/moldes_codigo/toba_codigo_separador.php',
 			'toba_codigo_separador_js' 				=> 'modelo/moldes_codigo/toba_codigo_separador_js.php',
 			'toba_codigo_separador_php' 			=> 'modelo/moldes_codigo/toba_codigo_separador_php.php',
 			//------------- Soporte a COMPONENTES -----------------------------------
			'toba_boton'							=> 'nucleo/componentes/interface/botones/toba_boton.php',
			'toba_evento_usuario'					=> 'nucleo/componentes/interface/botones/toba_evento_usuario.php',
			'toba_tab'								=> 'nucleo/componentes/interface/botones/toba_tab.php',
			'toba_ef'								=> 'nucleo/componentes/interface/efs/toba_ef.php',
			'toba_ef_combo'							=> 'nucleo/componentes/interface/efs/toba_ef_combo.php',
			'toba_ef_radio'							=> 'nucleo/componentes/interface/efs/toba_ef_combo.php',
			'toba_ef_cuit'							=> 'nucleo/componentes/interface/efs/toba_ef_cuit.php',
			'toba_ef_editable'						=> 'nucleo/componentes/interface/efs/toba_ef_editable.php',
			'toba_ef_editable_numero'				=> 'nucleo/componentes/interface/efs/toba_ef_editable.php',
			'toba_ef_editable_moneda'				=> 'nucleo/componentes/interface/efs/toba_ef_editable.php',
			'toba_ef_editable_numero_porcentaje'	=> 'nucleo/componentes/interface/efs/toba_ef_editable.php',
			'toba_ef_editable_clave'				=> 'nucleo/componentes/interface/efs/toba_ef_editable.php',
			'toba_ef_editable_fecha'				=> 'nucleo/componentes/interface/efs/toba_ef_editable.php',
			'toba_ef_editable_textarea'				=> 'nucleo/componentes/interface/efs/toba_ef_editable.php',
			'toba_ef_multi_seleccion_lista'			=> 'nucleo/componentes/interface/efs/toba_ef_multi_seleccion.php',
			'toba_ef_multi_seleccion_check'			=> 'nucleo/componentes/interface/efs/toba_ef_multi_seleccion.php',
			'toba_ef_multi_seleccion_doble'			=> 'nucleo/componentes/interface/efs/toba_ef_multi_seleccion.php',
			'toba_ef_oculto'						=> 'nucleo/componentes/interface/efs/toba_ef_oculto.php',
			'toba_ef_oculto_usuario'				=> 'nucleo/componentes/interface/efs/toba_ef_oculto.php',
			'toba_ef_popup'							=> 'nucleo/componentes/interface/efs/toba_ef_popup.php',
			'toba_ef_fieldset'						=> 'nucleo/componentes/interface/efs/toba_ef_sin_estado.php',
			'toba_ef_barra_divisora'				=> 'nucleo/componentes/interface/efs/toba_ef_sin_estado.php',
			'toba_ef_upload'						=> 'nucleo/componentes/interface/efs/toba_ef_upload.php',
			'toba_ef_checkbox'						=> 'nucleo/componentes/interface/efs/toba_ef_varios.php',
			'toba_ef_fijo'							=> 'nucleo/componentes/interface/efs/toba_ef_varios.php',
			'toba_ef_html'							=> 'nucleo/componentes/interface/efs/toba_ef_varios.php',
			'toba_nodo_arbol'						=> 'nucleo/componentes/interface/interfaces.php',
			'toba_relacion_entre_tablas'			=> 'nucleo/componentes/persistencia/toba_relacion_entre_tablas.php',
			'toba_tipo_datos'						=> 'nucleo/componentes/persistencia/toba_tipo_datos.php',
			'toba_datos_busqueda'					=> 'nucleo/componentes/persistencia/toba_datos_busqueda.php',
			'toba_form'								=> 'nucleo/lib/interface/toba_form.php',
			'toba_impr_html'						=> 'nucleo/lib/salidas/toba_impr_html.php',
			'toba_impresion'						=> 'nucleo/lib/salidas/toba_impresion.php',
			'toba_vista_pdf'						=> 'nucleo/lib/salidas/toba_vista_pdf.php',
 			'toba_componente_definicion'			=> 'nucleo/componentes/definicion/_interfaces.php',
			'toba_meta_clase'						=> 'modelo/info/componentes/toba_interface_meta_clase.php',
			'toba_datos_editores'					=> 'modelo/info/toba_datos_editores.php',
 			//-----------------------------------------------------------------------------------------------
			'toba_info_editores'					=> 'modelo/info/toba_info_editores.php',
			'toba_info_permisos'					=> 'modelo/info/toba_info_permisos.php',
			'toba_info_instancia'					=> 'modelo/info/toba_info_instancia.php',
			'toba_contexto_info'					=> 'modelo/info/toba_contexto_info.php',
			'toba_catalogo_items'					=> 'modelo/info/toba_catalogo_items.php',
			'toba_catalogo_objetos'					=> 'modelo/info/toba_catalogo_objetos.php',
			'toba_modelo_catalogo'					=> 'modelo/toba_modelo_catalogo.php',
			'toba_modelo_instalacion'				=> 'modelo/toba_modelo_instalacion.php',
			'toba_modelo_instancia'					=> 'modelo/toba_modelo_instancia.php',
			'toba_modelo_nucleo'					=> 'modelo/toba_modelo_nucleo.php',
			'toba_modelo_proyecto'					=> 'modelo/toba_modelo_proyecto.php',
			'toba_db_catalogo_general'				=> 'modelo/estructura_db/toba_db_catalogo_general.php',
			'toba_db_secuencias'					=> 'modelo/estructura_db/toba_db_secuencias.php',
			'toba_db_tablas_componente'				=> 'modelo/estructura_db/toba_db_tablas_componente.php',
			'toba_db_tablas_instancia'				=> 'modelo/estructura_db/toba_db_tablas_instancia.php',
			'toba_db_tablas_nucleo'					=> 'modelo/estructura_db/toba_db_tablas_nucleo.php',
			'toba_db_tablas_proyecto'				=> 'modelo/estructura_db/toba_db_tablas_proyecto.php',
			'toba_modelo_elemento'					=> 'modelo/lib/toba_modelo_elemento.php',
			'toba_error_modelo'						=> 'modelo/lib/toba_modelo_error.php',
			'toba_error_modelo_preexiste'			=> 'modelo/lib/toba_modelo_error.php',
			'toba_error_asistentes'					=> 'modelo/lib/toba_modelo_error.php',
			'toba_version'							=> 'modelo/lib/toba_version.php',
			'toba_proceso_gui'						=> 'modelo/lib/toba_proceso_gui.php',
			'toba_mock_proceso_gui'					=> 'modelo/lib/toba_proceso_gui.php',
			'toba_analizador_logger_fs'				=> 'modelo/lib/toba_analizador_logger.php',
			'toba_test'								=> 'modelo/lib/testing_unitario/toba_test.php',
			'toba_test_lista_casos'					=> 'modelo/lib/testing_unitario/toba_test_lista_casos.php',
			'toba_migracion'						=> 'modelo/migraciones/toba_migracion.php',
			'toba_asercion'							=> 'lib/toba_asercion.php',
			'toba_cache_db'							=> 'lib/toba_cache_db.php',
			'toba_encriptador'						=> 'lib/toba_encriptador.php',
			'toba_ini'								=> 'lib/toba_ini.php',
			'toba_editor_archivos'					=> 'lib/toba_editor_archivos.php',
			'toba_editor_texto'						=> 'lib/toba_editor_archivos.php',
			'toba_manejador_archivos'				=> 'lib/toba_manejador_archivos.php',
			'toba_sincronizador_archivos'			=> 'lib/toba_sincronizador_archivos.php',
 			'toba_archivo_php'						=> 'lib/reflexion/toba_archivo_php.php',
			'toba_clase_datos'						=> 'lib/reflexion/toba_clase_datos.php',
 			'toba_clase_php' 						=> 'lib/reflexion/toba_clase_php.php',
 			'toba_fecha' 							=> 'lib/toba_fecha.php',
 			'toba_ajax_respuesta'					=> 'nucleo/lib/toba_ajax_respuesta.php'
		);
	}

}
?>
