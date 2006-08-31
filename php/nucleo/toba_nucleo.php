<?php
require_once('lib/asercion.php');    		   	   			//Aserciones
require_once('lib/toba_db.php');				    				//Manejo de bases (utiliza abodb340)
require_once('lib/encriptador.php');						//Encriptador
require_once('lib/varios.php');								//Funciones genericas (Manejo de paths, etc.)
require_once('lib/sql.php');								//Libreria de manipulacion del SQL
require_once('nucleo/lib/toba_excepcion.php');				//Excepciones del TOBA
require_once('nucleo/lib/toba_logger.php');						//toba_logger
require_once('nucleo/lib/mensajes.php');					//Modulo de mensajes parametrizables
require_once('nucleo/lib/toba_notificaciones.php');				//Cola de mensajes utilizada durante la EJECUCION
require_once('nucleo/lib/toba_permisos.php');					//Administrador de permisos particulares
require_once('nucleo/lib/toba_recurso.php');						//Obtencion de imágenes de la aplicación
require_once('nucleo/lib/toba_usuario.php');	  			//Informacion sobre el usuario
require_once('nucleo/lib/toba_editor.php');			 	 		//Interaccion con el EDITOR
require_once('nucleo/lib/toba_cronometro.php');          		//Cronometrar ejecucion
require_once('nucleo/lib/toba_instalacion.php');			//Informacion sobre la instalacion
require_once('nucleo/lib/toba_instancia.php');				//Informacion sobre la instancia
require_once('nucleo/lib/toba_proyecto.php');	   			//Informacion sobre el proyecto
require_once('nucleo/componentes/toba_constructor.php');	//Constructor de componentes
require_once('nucleo/componentes/toba_cargador.php');		//Cargador de componentes
require_once('nucleo/componentes/toba_catalogo.php');		//Catalogo de componentes


/**
 * Clase que brinda las puertas de acceso al núcleo de toba
 */
class toba_nucleo
{
	static private $instancia;
	private $solicitud;
	private $medio_acceso;
	private $solicitud_en_proceso = false;
	
	private function __construct()
	{
		if (php_sapi_name() !== 'cli' && get_magic_quotes_gpc()) {
			throw new toba_excepcion("Necesita desactivar las 'magic_quotes' en el servidor (ver http://www.php.net/manual/es/security.magicquotes.disabling.php)");
		}
		toba::get_cronometro();
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
			require_once('nucleo/lib/toba_sesion.php');			//Control de sesiones HTTP
			session_start();
		    toba_http::headers_standart();
			$this->preparar_include_path();
			$this->iniciar_contexto_proyecto();
			try {
				$this->solicitud = $this->cargar_solicitud();
				$this->solicitud_en_proceso = true;
				$this->solicitud->procesar();
			} catch( excepcion_reset_nucleo $e ) {
				//El item puede redireccionar?
				if ( !$this->solicitud->get_datos_item('redirecciona') ) {
					throw new toba_excepcion('ERROR: El item no esta habilitado para provocar redirecciones.');
				}
				//TRAP para forzar la recarga de solicitud
				$this->solicitud_en_proceso = false;
				toba::get_hilo()->limpiar_memoria();
				$item_nuevo = $e->get_item();
				toba::get_hilo()->set_item_solicitado( $item_nuevo );				
				$this->solicitud = $this->cargar_solicitud();
				$this->solicitud->procesar();
			}
			$this->solicitud->registrar();
			$this->solicitud->finalizar_objetos();
		} catch (Exception $e) {
			toba::get_logger()->crit($e, 'toba');
			echo $e->getMessage() . "\n\n";
		}
		toba::get_logger()->guardar();		
		//echo cronometro::instancia()->tiempo_acumulado();
	}

	/**
	 * Punto de entrada desde la consola al nucleo
	 */	
	function acceso_consola($instancia, $proyecto, $item, $usuario)
	{
		require_once('nucleo/lib/toba_sesion.php');			//Control de sesiones HTTP		
		require_once('nucleo/solicitud.php');		
		require_once("nucleo/toba_solicitud_consola.php");		
		$estado_proceso = null;
		try {
			define('apex_pa_instancia', $instancia);
			define('apex_pa_proyecto' , $proyecto);
			$this->preparar_include_path();			
			$this->iniciar_contexto_proyecto();						
			toba::get_sesion()->iniciar($usuario);
			//$this->solicitud = new toba_solicitud_consola($proyecto, $item, $usuario);
			$this->solicitud = toba_constructor::get_runtime(array('proyecto'=>$proyecto, 'componente'=>$item), 'item');
			$this->iniciar_contexto_proyecto();
			$this->solicitud->procesar();	//Se llama a la ACTIVIDAD del ITEM
			$this->solicitud->registrar();
			$this->solicitud->finalizar_objetos();
			$estado_proceso = $this->solicitud->get_estado_proceso();
		} catch (toba_excepcion $e) {
			toba::get_logger()->crit($e, 'toba');
			echo $e;
		}
		toba::get_logger()->debug('Estado Proceso: '.$estado_proceso, 'toba');
		toba::get_logger()->guardar();
		exit($estado_proceso);
	}
		
	/**
	 * Se determia el item y se controla el acceso
	 */
	function cargar_solicitud()
	{
		if (toba::get_sesion()->controlar_estado_activacion()) {
			$item = $this->get_id_item('item_inicio_sesion');
			$grupo_acceso = toba::get_sesion()->get_grupo_acceso();
			$solicitud = toba_constructor::get_runtime(array('proyecto'=>$item[0],'componente'=>$item[1]), 'item');
			if (!$solicitud->es_item_publico()) {
				toba_proyecto::control_acceso_item($item, $grupo_acceso);
			}
			return $solicitud;
		} else {
			$mensaje_error = 'La seccion no esta activa. Solo es posible acceder items PUBLICOS.';
			$item = $this->get_id_item('item_pre_sesion');
			$solicitud = toba_constructor::get_runtime(array('proyecto'=>$item[0],'componente'=>$item[1]), 'item');
			if (!$solicitud->es_item_publico()) {
				// Si se arrastra una URL previa despues de finalizar la sesion y se refresca la pagina
				// el nucleo trata de cargar un item explicito por URL. El mismo no va a ser publico...
				// Esto apunta a solucionar ese error: Blanqueo el item solicitado y vuelvo a intentar.
				// (NOTA: esto puede ocultar la navegacion entre items supuestamente publicos)
				if ( toba::get_hilo()->obtener_item_solicitado() ) {
					toba::get_logger()->debug('Fallo la carga de un item publico. Se intenta con el item predeterminado', 'toba');
					toba::get_hilo()->set_item_solicitado(null);					
					$item = $this->get_id_item('item_pre_sesion');
					$solicitud = toba_constructor::get_runtime(array('proyecto'=>$item[0],'componente'=>$item[1]), 'item');
					if (!$solicitud->es_item_publico()) {
						throw new toba_excepcion($mensaje_error);				
					}
				} else {
					throw new toba_excepcion($mensaje_error);				
				}
			}
			return $solicitud;
		}		
	}

	/**
	 * Averigua el ITEM ACTUAL. Si no existe y puede busca un ITEM PREDEFINIDO
	 */
	function get_id_item($predefinido=null,$forzar_predefinido=false)
	{
		$item = toba::get_hilo()->obtener_item_solicitado();
		if (!$item) {
			if(isset($predefinido)){
				$item[0] = toba_proyecto::instancia()->get_id();
				$item[1] = toba_proyecto::instancia()->get_parametro($predefinido);		
				toba::get_hilo()->set_item_solicitado($item);
			} else {
				throw new toba_excepcion('NUCLEO: No es posible determinar el item a cargar');
			}
		}
		return $item;
	}
	
	/**
	 * Incluye la ruta del php de toba y del proyecto actual
	 */
	protected function preparar_include_path()
	{
		$proyecto = toba_proyecto::instancia()->get_id();
		$i_proy = toba_instancia::instancia()->get_path_proyecto($proyecto);
		$i_path = ini_get("include_path");
		if (substr(PHP_OS, 0, 3) == 'WIN'){
			$i_proy_php = $i_proy  . "/php";
			ini_set("include_path", $i_path . ";.;" . $i_proy_php );
		}else{
			$i_proy_php = $i_proy . "/php";
			ini_set("include_path", $i_path . ":.:" . $i_proy_php);
		}
		$_SESSION['toba']["path_proyecto"] = $i_proy;
		$_SESSION['toba']["path_proyecto_php"] = $i_proy_php;
		//echo "PROYECTO: $proyecto - INCLUDE_PATH= \"" . ini_get("include_path") ."\"";
	}

	/**
	 * Incluye la ventana de inicialización del proyecto
	 */
	protected function iniciar_contexto_proyecto()
	{
		include_once("inicializacion.php");
	}

	function solicitud_en_proceso()
	{
		return $this->solicitud_en_proceso;
	}
}
?>