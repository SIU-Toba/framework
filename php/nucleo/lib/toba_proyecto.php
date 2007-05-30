<?php
/**
 * Brinda servicios de información sobre el proyecto actualmente cargado en el framework:
 *  - Información del archivo de configuración proyecto.ini, cacheandolo en la memoria
 *  - Información de la definición básica en el editor (e.i. los metadatos)
 * 
 * @package Centrales
 */
class toba_proyecto
{
	static private $instancia;
	static private $id_proyecto;
	private $memoria;								//Referencia al segmento de $_SESSION asignado
	private $id;
	private $indice_items_accesibles;
	const prefijo_punto_acceso = 'apex_pa_';

	static function get_id()
	{
		if (! isset(self::$id_proyecto)) {
			$item = toba_memoria::get_item_solicitado_original();
			//-- El proyecto viene por url
			if (isset($item) && isset($item[0])) {
				self::$id_proyecto = $item[0];
			} else {
				//--- Si no viene por url, se toma la constante
				if(! defined('apex_pa_proyecto') ){
					throw new toba_error("Es necesario definir la constante 'apex_pa_proyecto'");
				} 
				self::$id_proyecto = apex_pa_proyecto;
			}
		}
		return self::$id_proyecto;
	}

	/**
	 * @return toba_proyecto
	 */
	static function instancia($id_proyecto=null, $recargar=false)
	{
		if (! isset($id_proyecto)) {
			$id_proyecto = self::get_id();
		}
		if (!isset(self::$instancia[$id_proyecto]) || $recargar) {
			toba::logger()->debug("TOBA PROYECTO: creando instancia de '$id_proyecto'", 'toba');
			self::$instancia[$id_proyecto] = new toba_proyecto($id_proyecto, $recargar);	
		}
		return self::$instancia[$id_proyecto];
	}

	static function eliminar_instancia()
	{
		self::$instancia[self::get_id()] = null;
	}
	
	private function __construct($proyecto, $recargar=false)
	{
		toba_proyecto_db::set_db( toba::instancia()->get_db() );//Las consultas salen de la instancia actual
		$this->id = $proyecto;
		$this->memoria =& toba::manejador_sesiones()->segmento_info_proyecto($proyecto);
		if (!$this->memoria || $recargar) {
			$this->memoria = self::cargar_info_basica();
			toba::logger()->debug('Inicialización de TOBA_PROYECTO: ' . $this->id,'toba');
		}
	}

	/**
	 * Retorna el valor de un parámetro generico del proyecto (ej. descripcion) cacheado en la memoria
	 * @return toba_error si el parametro no se encuentra definido, sino el valor del parámetro
	 */
	function get_parametro($id)
	{
		if( defined( self::prefijo_punto_acceso . $id ) ){
			return constant(self::prefijo_punto_acceso . $id);
		} elseif (isset($this->memoria[$id])) {
			return $this->memoria[$id];
		} else {
			if( array_key_exists($id,$this->memoria)) {
				return null;
			}else{
				throw new toba_error("INFO_PROYECTO: El parametro '$id' no se encuentra definido.");
			}
		}	
	}

	/**
	 * Cachea en la memoria un par clave-valor del proyecto actual
	 */
	function set_parametro($id, $valor)
	{
		$this->memoria[$id] = $valor;
	}

	//----------------------------------------------------------------
	// DATOS
	//----------------------------------------------------------------	

	/**
	 * Retorna la base de datos de la instancia a la que pertenece este proyecto
	 * @return toba_db
	 */
	function cargar_info_basica($proyecto=null)
	{
		if ( toba::nucleo()->utilizar_metadatos_compilados( $this->id ) ) {
			$rs = $this->recuperar_datos_compilados('toba_mc_gene__basicos','info_basica');
		} else {
			$proyecto = isset($proyecto) ? $proyecto : $this->id;
			$rs = toba_proyecto_db::cargar_info_basica($proyecto);
		}
		if (!$rs) {
			throw new toba_error("El proyecto '".$this->id."' no se encuentra cargado en la instancia ".toba_instancia::get_id());	
		}
		return $rs;
	}

	function es_multiproyecto()
	{
		return $this->get_parametro('listar_multiproyecto');
	}

	/**
	 * Retorna el path base absoluto del proyecto
	 */
	function get_path()
	{
		return toba::instancia()->get_path_proyecto(self::get_id());
	}

	/**
	 * Retorna el path absoluto de la carpeta 'php' del proyecto
	 */
	function get_path_php()
	{
		return $this->get_path() . '/php';
	}

	/**
	 * Retorna el path base absoluto del directorio temporal no-navegable del proyecto
	 * (mi_proyecto/temp);
	 */
	function get_path_temp()
	{
		$dir = $this->get_path() . '/temp';
		if (!file_exists($dir)) {
			mkdir($dir, 0700);
		}
		return $dir;
	}	
	
	/**
	 * Retorna path y URL de la carpeta navegable del proyecto actual
	 * (mi_proyecto/www);
	 * @return array con claves 'path' (en el sist.arch.) y 'url' (URL navegable)
	 */
	function get_www($archivo="")
	{
		$path_real = $this->get_path() . "/www/" . $archivo;
		$path_browser = toba_recurso::url_proyecto();
		if ($archivo != "") {
		 	$path_browser .= "/" . $archivo;
		}
		return array(	"path" => $path_real,
						"url" => $path_browser);
	}
	
	/**
	 * Retorna el path y url del directorio temporal navegable del proyecto (mi_proyecto/www/temp);
	 * En caso de no existir, crea el directorio
	 * Si se pasa un path relativo como parámetro retorna el path absoluto del archivo en el directorio temporal
	 * @return array con claves 'path' (en el sist.arch.) y 'url' (URL navegable)
	 */
	function get_www_temp($archivo='')
	{
		if (!file_exists($this->get_path() . "/www/temp")) {
			mkdir($this->get_path() . "/www/temp", 0700);
		}
		if ($archivo != '') {
			return $this->get_www('temp/'. $archivo);
		} else {
			return $this->get_www('temp');
		}
	}
	
	//--------------  Carga dinamica de COMPONENTES --------------

	function get_definicion_dependencia($id_componente, $proyecto=null)
	{
		$proyecto = isset($proyecto) ? $proyecto : self::get_id() ;
		if ( toba::nucleo()->utilizar_metadatos_compilados( $this->id ) ) {
			$temp = toba_constructor::get_metadatos_compilados(array('proyecto'=>$proyecto, 'componente'=>$id_componente));
			$rs = $temp['_info'];
		} else {
			//Busco la definicion del componente
			$sql = toba_componente_def::get_vista_extendida($proyecto, $id_componente);
			$rs = toba_proyecto_db::get_db()->consultar_fila($sql['_info']['sql']);
		}
		return $rs;
	}

	//--------------------  Puntos de Control  ------------------------

	function get_info_punto_control($punto_control, $proyecto=null)	
	{
		if (! isset($proyecto)) $proyecto = self::get_id();
		$info = array();
		if ( toba::nucleo()->utilizar_metadatos_compilados( $this->id ) ) {
			$info = $this->recuperar_datos_compilados('toba_mc_gene__pcontrol_'.$punto_control, 'get_info');
		} else {
			$info['parametros'] = toba_proyecto_db::punto_control_parametros($proyecto, $punto_control);
			$info['controles'] = toba_proyecto_db::punto_control_controles($proyecto, $punto_control);
		}
		return $info;	
	}

	//------------------------  FUENTES  -------------------------

	function get_info_fuente_datos($id_fuente, $proyecto=null)
	{
		if ( toba::nucleo()->utilizar_metadatos_compilados( $this->id ) ) {
			$rs = $this->recuperar_datos_compilados('toba_mc_gene__basicos','info_fuente__'.$id_fuente);
		} else {
			if (! isset($proyecto)) $proyecto = self::get_id();
			$rs = toba_proyecto_db::get_info_fuente_datos($proyecto, $id_fuente);
		}
		if (empty($rs)) {
			throw new toba_error("No se puede encontrar la fuente '$id_fuente' en el proyecto '$proyecto'");	
		}
		return $rs;
	}
	
	//------------------------  Grupos de acceso & ITEMS  -------------------------

	/**
	 * Retorna la lista de items a los que puede acceder el usuario
	 *
	 * @param unknown_type $solo_primer_nivel
	 * @param string $proyecto Por defecto el actual
	 * @param string $grupos_acceso Por defecto el del usuario actual
	 * @return array RecordSet contienendo información de los items
	 */
	function get_items_menu($proyecto=null, $grupos_acceso=null)
	{
		if (!isset($grupos_acceso)) $grupos_acceso = toba::manejador_sesiones()->get_grupos_acceso();
		if ( toba::nucleo()->utilizar_metadatos_compilados( $this->id ) ) {
			$rs = $this->recuperar_datos_compilados_grupo(	'toba_mc_gene__grupo_', 
															$grupos_acceso, 
															'get_items_menu',
															true,
															array('padre','orden'));
		} else {
			if (!isset($proyecto)) $proyecto = self::get_id();
			$rs = toba_proyecto_db::get_items_menu($proyecto, $grupos_acceso);
		}
		return $rs;
	}	

	/**
	 * Valida que un grupo de acceso tenga acceso a un item
	 */
	function puede_grupo_acceder_item($proyecto, $item)
	{
		$grupos_acceso = toba::manejador_sesiones()->get_grupos_acceso();	
		//Recupero los items y los formateo en un indice consultable
		if(!isset($this->indice_items_accesibles)) {
			$this->indice_items_accesibles = array();
			if ( toba::nucleo()->utilizar_metadatos_compilados( $this->id ) ) {
				$this->indice_items_accesibles = $this->recuperar_datos_compilados_grupo('toba_mc_gene__grupo_', 
																							$grupos_acceso, 
																							'get_items_accesibles',
																							false);
			} else {
				$rs = toba_proyecto_db::get_items_accesibles(self::get_id(), $grupos_acceso);
				foreach( $rs as $accesible ) {
					$this->indice_items_accesibles[$accesible['proyecto'].'-'.$accesible['item']] = 1;
				}
			}
		}
		return isset($this->indice_items_accesibles[$proyecto.'-'.$item]);
	}

	/**
	*	Devuelve la lista de items de la zona a los que puede acceder el grupo actual
	*/
	function get_items_zona($zona, $grupos_acceso=null)
	{
		if (!isset($grupos_acceso)) $grupos_acceso = toba::manejador_sesiones()->get_grupos_acceso();
		if ( toba::nucleo()->utilizar_metadatos_compilados( $this->id ) ) {
			$rs = $this->recuperar_datos_compilados_grupo(	'toba_mc_gene__grupo_', 
															$grupos_acceso,
															'get_items_zona__'.$zona,
															true,
															array('orden') );
		} else {
			$rs = toba_proyecto_db::get_items_zona(self::get_id(), $grupos_acceso, $zona);	
		}
		return $rs;
	}

	function get_grupos_acceso_usuario_anonimo()
	{
		$grupos = explode(',',$this->get_parametro('usuario_anonimo_grupos_acc'));
		$grupos = array_map('trim',$grupos);
		return $grupos;
	}

	//------------------------  Permisos  -------------------------
	
	/**
	 * Retorna la lista de permisos globales (tambien llamados particulares) de un grupo de acceso en el proyecto actual
	 */
	function get_lista_permisos($grupos_acceso=null)
	{
		$grupos_acceso = isset($grupos_acceso) ? $grupos_acceso : toba::manejador_sesiones()->get_grupo_acceso();	
		if ( toba::nucleo()->utilizar_metadatos_compilados( $this->id ) ) {
			$rs = $this->recuperar_datos_compilados_grupo('toba_mc_gene__grupo_', $grupos_acceso, 'get_lista_permisos');
		} else {
			$rs = toba_proyecto_db::get_lista_permisos(self::get_id(), $grupos_acceso);
		}
		return $rs;
	}
	
	/**
	 * Retorna la descripción asociada a un permiso global particular del proy. actual
	 */
	function get_descripcion_permiso($permiso)
	{
		if ( toba::nucleo()->utilizar_metadatos_compilados( $this->id ) ) {
			$rs = $this->recuperar_datos_compilados('toba_mc_gene__basicos', 'info_permiso__'.$permiso);
		} else {
			$rs = toba_proyecto_db::get_descripcion_permiso(self::get_id(), $permiso);
		}
		return $rs;
	}

	//------------------------  MENSAJES  -------------------------

	function get_mensaje_toba($indice)
	{
		if ( toba::nucleo()->utilizar_metadatos_compilados( $this->id ) ) {
			$clase = 'toba_mc_gene__msj_toba';
			$metodo = 'get__'.$objeto;
			if ( $this->existe_dato_compilado($clase, $metodo) ) {
				$rs = $this->recuperar_datos_compilados($clase, $metodo);
			} else {
				$rs = array();	
			}
		} else {
			$rs = toba_proyecto_db::get_mensaje_toba($indice);	
		}
		return $rs;
	}
	
	function get_mensaje_proyecto($indice)
	{
		if ( toba::nucleo()->utilizar_metadatos_compilados( $this->id ) ) {
			$clase = 'toba_mc_gene__msj_proyecto';
			$metodo = 'get__'.$indice;
			if ( $this->existe_dato_compilado($clase, $metodo) ) {
				$rs = $this->recuperar_datos_compilados($clase, $metodo);
			} else {
				$rs = array();	
			}
		} else {
			$rs = toba_proyecto_db::get_mensaje_proyecto(self::get_id(), $indice);	
		}
		return $rs;
	}

	function get_mensaje_objeto($objeto, $indice)
	{
		if ( toba::nucleo()->utilizar_metadatos_compilados( $this->id ) ) {
			$clase = 'toba_mc_gene__msj_proyecto_objeto';
			$metodo = 'get__'.$objeto.'__'.$indice;
			if ( $this->existe_dato_compilado($clase, $metodo) ) {
				$rs = $this->recuperar_datos_compilados($clase, $metodo);
			} else {
				$rs = array();	
			}
		} else {
			$rs = toba_proyecto_db::get_mensaje_objeto(self::get_id(), $objeto, $indice);	
		}
		return $rs;
	}

	//-- Soporte a la compilacion ----------------------
	
	function existe_dato_compilado($clase, $metodo)
	{
		return in_array($metodo, get_class_methods($clase));
	}
	
	function recuperar_datos_compilados($clase, $metodo)
	{
		return call_user_func(array($clase, $metodo));
	}

	function recuperar_datos_compilados_grupo($prefijo_clase, $grupos, $metodo, $reindexar=true, $orden=null)
	{
		$temp = array();
		foreach( $grupos as $grupo ) {
			$clase = $prefijo_clase . $grupo;
			$temp = array_merge($temp, call_user_func(array($clase, $metodo)));
		}
		if($reindexar) {
			$temp = array_values($temp);	
		}
		if(isset($orden)){
			//Se necesita??
		}
		return $temp;
	}
}
?>