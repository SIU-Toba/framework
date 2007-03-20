<?php
require_once('lib/toba_asercion.php');    			   	   		//Aserciones
require_once('lib/toba_db.php');			    				//Manejo de bases (utiliza abodb340)
require_once('lib/toba_encriptador.php');						//Encriptador
require_once('lib/toba_varios.php');							//Funciones genericas (Manejo de paths, etc.)
require_once('lib/toba_sql.php');								//Libreria de manipulacion del SQL
require_once('nucleo/lib/toba_error.php');						//Excepciones del TOBA
require_once('nucleo/lib/toba_logger.php');						//toba_logger
require_once('nucleo/lib/toba_mensajes.php');					//Modulo de mensajes parametrizables
require_once('nucleo/lib/toba_memoria.php');					//Administrador de memoria
require_once('nucleo/lib/toba_notificacion.php');				//Cola de mensajes utilizada durante la EJECUCION
require_once('nucleo/lib/toba_permisos.php');					//Administrador de permisos particulares
require_once('nucleo/lib/toba_recurso.php');					//Obtencion de imágenes de la aplicación
require_once('nucleo/lib/toba_editor.php');			 	 		//Interaccion con el EDITOR
require_once('nucleo/lib/toba_cronometro.php');          		//Cronometrar ejecucion
require_once('nucleo/lib/toba_instalacion.php');				//Informacion sobre la instalacion
require_once('nucleo/lib/toba_instancia.php');					//Informacion sobre la instancia
require_once('nucleo/lib/toba_proyecto.php');	   				//Informacion sobre el proyecto
require_once('nucleo/lib/toba_manejador_sesiones.php');			//Informacion sobre el proyecto
require_once('nucleo/lib/toba_puntos_control.php');	   				//Informacion sobre los puntos de control
require_once('nucleo/componentes/toba_constructor.php');		//Constructor de componentes
require_once('nucleo/componentes/toba_cargador.php');			//Cargador de componentes
require_once('nucleo/componentes/toba_catalogo.php');			//Catalogo de componentes

/**
 * Clase que brinda las puertas de acceso al núcleo de toba
 * @package Centrales
 */
class toba_nucleo
{
	static private $instancia;
	private $solicitud;
	private $medio_acceso;
	private $solicitud_en_proceso = false;
	
	private function __construct()
	{
		toba::cronometro();		
		if (php_sapi_name() !== 'cli' && get_magic_quotes_gpc()) {
			throw new toba_error("Necesita desactivar las 'magic_quotes' en el servidor (ver http://www.php.net/manual/es/security.magicquotes.disabling.php)");
		}
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
			require_once('nucleo/toba_solicitud.php');
			require_once('nucleo/lib/toba_http.php');				//Genera Encabezados de HTTP
			require_once('nucleo/lib/toba_sesion.php');				//Control de sesiones HTTP
			$this->iniciar_contexto_ejecucion();
		    toba_http::headers_standart();
			try {
				$this->solicitud = $this->cargar_solicitud();
				$this->solicitud_en_proceso = true;
				$this->solicitud->procesar();
			} catch( toba_reset_nucleo $e ) {
				toba::logger()->debug('Se recargo el nucleo','toba');
				//El item puede redireccionar?
				if ( !$this->solicitud->get_datos_item('redirecciona') ) {
					throw new toba_error('ERROR: El item no esta habilitado para provocar redirecciones.');
				}
				//TRAP para forzar la recarga de solicitud
				$this->solicitud_en_proceso = false;
				toba::memoria()->limpiar_memoria();
				$item_nuevo = $e->get_item();
				toba::memoria()->set_item_solicitado( $item_nuevo );				
				$this->solicitud = $this->cargar_solicitud();
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
		require_once('nucleo/lib/toba_sesion.php');			//Control de sesiones HTTP		
		require_once('nucleo/toba_solicitud.php');		
		require_once('nucleo/toba_solicitud_consola.php');
		$estado_proceso = null;
		try {
			define('apex_pa_instancia', $instancia);
			define('apex_pa_proyecto' , $proyecto);
			$this->iniciar_contexto_ejecucion();			
			//$this->solicitud = new toba_solicitud_consola($proyecto, $item, $usuario);
			$this->solicitud = toba_constructor::get_runtime(array('proyecto'=>$proyecto, 'componente'=>$item), 'item');
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
		toba::logger()->debug('Tiempo utilizado: ' . cronometro::instancia()->tiempo_acumulado() . ' seg.');
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
	function cargar_solicitud()
	{
		if (toba::manejador_sesiones()->existe_sesion_activa()) {		// Estoy dentro de una SESION
			$item = $this->get_id_item('item_inicio_sesion');
			$solicitud = toba_constructor::get_runtime(array('proyecto'=>$item[0],'componente'=>$item[1]), 'item');
			if (!$solicitud->es_item_publico()) {
				$this->autorizar_acceso_item($item);
			}
			return $solicitud;
		} else {														// Estoy fuera de la sesion. Solo se puede acceder a lo publico
			$mensaje_error = 'La seccion no esta activa. Solo es posible acceder items PUBLICOS.';
			$item = $this->get_id_item('item_pre_sesion');
			$solicitud = toba_constructor::get_runtime(array('proyecto'=>$item[0],'componente'=>$item[1]), 'item');
			if (!$solicitud->es_item_publico()) {
				// Si se arrastra una URL previa despues de finalizar la sesion y se refresca la pagina
				// el nucleo trata de cargar un item explicito por URL. El mismo no va a ser publico...
				// Esto apunta a solucionar ese error: Blanqueo el item solicitado y vuelvo a intentar.
				// (NOTA: esto puede ocultar la navegacion entre items supuestamente publicos)
				if ( toba::memoria()->get_item_solicitado() ) {
					toba::logger()->debug('Fallo la carga de un item publico. Se intenta con el item predeterminado', 'toba');
					toba::memoria()->set_item_solicitado(null);					
					$item = $this->get_id_item('item_pre_sesion');
					$solicitud = toba_constructor::get_runtime(array('proyecto'=>$item[0],'componente'=>$item[1]), 'item');
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
	protected function get_id_item($predefinido=null,$forzar_predefinido=false)
	{
		$item = toba::memoria()->get_item_solicitado();
		if (!$item) {
			if(isset($predefinido)){
				$item[0] = toba::proyecto()->get_id();
				$item[1] = toba::proyecto()->get_parametro($predefinido);		
				toba::memoria()->set_item_solicitado($item);
			} else {
				throw new toba_error('NUCLEO: No es posible determinar el item a cargar');
			}
		}
		return $item;
	}
	
	protected function autorizar_acceso_item($item)
	{
		$grupo_acceso = toba::manejador_sesiones()->get_grupo_acceso();
		if (! toba::proyecto()->puede_grupo_acceder_item($grupo_acceso, $item) ) {
			throw new toba_error_autorizacion('El usuario no posee permisos para acceder al item solicitado.');
		}
	}

	protected function iniciar_contexto_ejecucion()
	{
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
}
?>