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
	private $items_excluidos = array();
	private $mapeo_componentes = array();
	private $ini_proyecto;
	private $personalizacion_iniciada;
	const prefijo_punto_acceso = 'apex_pa_';

	/**
	 * Retorna el id del proyecto actualmente cargado en el pedido de página
	 */
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
					if (isset($_SERVER['TOBA_PROYECTO'])) {
						define('apex_pa_proyecto', $_SERVER['TOBA_PROYECTO']);
					} else {
						throw new toba_error("Es necesario definir la constante 'apex_pa_proyecto'");
					}
				} 
				self::$id_proyecto = apex_pa_proyecto;
			}
		}
		return self::$id_proyecto;
	}

	/**
	 * @return toba_proyecto
	 */
	static function instancia()
	{
		if (!isset(self::$instancia)) {
			$id_proyecto = self::get_id();			
			//toba::logger()->debug("TOBA PROYECTO: creando instancia de '$id_proyecto'", 'toba');
			self::$instancia = new toba_proyecto($id_proyecto);
		}
		return self::$instancia;
	}
	
	static function hay_instancia()
	{
		return isset(self::$instancia);
	}

	static function eliminar_instancia()
	{
		self::$instancia = null;
	}
	
	private function __construct($proyecto)
	{
		if (! in_array($proyecto, toba::instancia()->get_id_proyectos())) {		//El proyecto no existe o no esta cargado en la instancia
			if (! toba::instalacion()->es_produccion()) {
						throw new toba_error("El proyecto '".$proyecto."' no se encuentra cargado en la instancia");
			} else {
						die;		//En produccion no se loguea nada, se mata inmediatamente el proceso
			}
		}
		toba_proyecto_db::set_db( toba::instancia()->get_db() );//Las consultas salen de la instancia actual
		$this->id = $proyecto;
		$this->memoria =& toba::manejador_sesiones()->segmento_info_proyecto($proyecto);
		if (!$this->memoria) {
			$this->memoria = self::cargar_info_basica();
			//toba::logger()->debug('Inicialización de TOBA_PROYECTO: ' . $this->id,'toba');
		}
		$path_ini = self::get_path().'/proyecto.ini';
		if (file_exists($path_ini)) {
			$this->ini_proyecto = new toba_ini($path_ini);
		}
		$this->configurar_logger();
	}


	/**
	 * Retorna el valor de un parámetro generico del proyecto (ej. descripcion) cacheado en la memoria
	 * @return toba_error si el parametro no se encuentra definido, sino el valor del parámetro
	 */
	function get_parametro($seccion, $parametro=null, $obligatorio=true)
	{
		$id = $seccion;
		if (isset($parametro)) {
			$id = $seccion.'.'.$parametro;
		}
		if( defined(self::prefijo_punto_acceso.$id) ){
			return constant(self::prefijo_punto_acceso.$id);
		} elseif (isset($this->memoria[$id])) {
			return $this->memoria[$id];
		} elseif (isset($this->ini_proyecto) && $this->ini_proyecto->existe_entrada($seccion, $parametro)) {
			return $this->ini_proyecto->get($seccion, $parametro);
		} else {
			if( array_key_exists($id, $this->memoria)) {
				return null;
			}else{
				if ($obligatorio) {
					throw new toba_error("INFO_PROYECTO: El parametro '$id' no se encuentra definido.");
				} else {
					return null;
				}
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
	 * Retorna el número de versión propio del proyecto
	 * @return toba_version
	 */
	function get_version()
	{
		if (isset($this->ini_proyecto) && $this->ini_proyecto->existe_entrada('proyecto', 'version')) {
			return new toba_version($this->ini_proyecto->get('proyecto', 'version'));
		} else {
			//Se asume que si el proyecto no da un numero de version, se toma la del nucleo (para no mantener la de toba_referencia, usuarios, etc)
			return toba::instalacion()->get_version();
		}
	}	
	/**
	 * Retorna la base de datos de la instancia a la que pertenece este proyecto
	 * @return toba_db
	 */
	function cargar_info_basica($proyecto=null)
	{
		$proyecto = isset($proyecto) ? $proyecto : $this->id ;
		if ( toba::nucleo()->utilizar_metadatos_compilados( $proyecto ) ) {
			$rs = $this->recuperar_datos_compilados('toba_mc_gene__basicos','info_basica');
		} else {
			$rs = toba_proyecto_db::cargar_info_basica($proyecto);
		}
		if (!$rs) {
			throw new toba_error("El proyecto '".$proyecto."' no se encuentra cargado en la instancia ".toba_instancia::get_id());	
		}
		return $rs;
	}

	function es_multiproyecto()
	{
		return $this->get_parametro('listar_multiproyecto');
	}

	function permite_cambio_perfiles()
	{
		$permite = $this->get_parametro('proyecto', 'permite_cambio_perfil_funcional', false);
		return (! is_null($permite) && $permite);
	}
	
	function es_personalizable()
	{
		$rs = $this->get_parametro('extension_proyecto');
		
		return $rs;
	}
	
	function personalizacion_activa()
	{
		if (! isset($this->personalizacion_iniciada)) {
			$this->personalizacion_iniciada = toba_personalizacion::get_personalizacion_iniciada(self::get_id());
		}
		return $this->personalizacion_iniciada;
	}

	function get_clases_extendidas()
	{
		return array(
			'extension_toba' => $this->get_parametro('extension_toba'),
			'extension_proyecto'  => $this->get_parametro('extension_proyecto')
		);
	}

	/**
	 * Retorna el path base absoluto del proyecto
	 */
	static function get_path()
	{
		return toba::instancia()->get_path_proyecto(self::get_id());
	}

	static function get_path_pers()
	{
		return self::get_path().'/'.toba_personalizacion::dir_personalizacion;
	}
	
	/**
	 * Retorna el flag de compilacion del proyecto
	 */
	static function get_directiva_compilacion()
	{
		return toba::instancia()->get_directiva_compilacion(self::get_id());
	}

	/**
	 * Retorna el path absoluto de la carpeta 'php' del proyecto
	 */
	static function get_path_php()
	{
		return self::get_path() . '/php';
	}

	/**
	 * Retorna el path absoluto de la carpeta 'php' de la personalizacion
	 */
	static function get_path_pers_php()
	{
		return self::get_path_pers() . '/php';
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

    function get_www_pers($archivo="")
	{
		$path_real = $this->get_path_pers() . "/www/" . $archivo;
		$path_browser = toba_recurso::url_proyecto($this->id, true);
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
		$proyecto = isset($proyecto) ? $proyecto : $this->id ;
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

	function get_id_componente_por_indice($identificador, $proyecto=null)
	{
		if(! $this->mapeo_componentes ) {
			if (! isset($proyecto)) { $proyecto = $this->id; }
			if ( toba::nucleo()->utilizar_metadatos_compilados( $proyecto ) ) {
				$this->mapeo_componentes = $this->recuperar_datos_compilados('toba_mc_gene__basicos', 'info_indices_componentes');
			} else {
				$this->mapeo_componentes = toba_proyecto_db::get_mapeo_componentes_indice($proyecto);
			}
		}
		if( ! isset($this->mapeo_componentes[$identificador]) ) {
			throw new toba_error("Error buscando el componente '$identificador'. El INDICE no existe");
		}
		return $this->mapeo_componentes[$identificador];
	}

	//--------------------  Dimensiones  ------------------------
	
	function get_info_dimension($dimension, $proyecto=null)
	{
		if (! isset($proyecto)) { $proyecto = $this->id;}
		if ( toba::nucleo()->utilizar_metadatos_compilados( $proyecto ) ) {
			return $this->recuperar_datos_compilados('toba_mc_gene__dim_'.$dimension, 'get_info');
		} else {
			return toba_proyecto_db::get_info_dimension($proyecto, $dimension);
		}
	}

	static function get_info_relacion_entre_tablas($fuente_datos, $proyecto)
	{
		if ( toba::nucleo()->utilizar_metadatos_compilados( $proyecto ) ) {
			return self::recuperar_datos_compilados('toba_mc_gene__relacion_tablas_'.$fuente_datos, 'get_info');
		} else {
			return toba_proyecto_db::get_info_relacion_entre_tablas($proyecto, $fuente_datos);
		}
	}

	//--------------------  Puntos de Control  ------------------------

	function get_info_punto_control($punto_control, $proyecto=null)	
	{
		if (! isset($proyecto)) { $proyecto = $this->id;}
		$info = array();
		if ( toba::nucleo()->utilizar_metadatos_compilados( $proyecto ) ) {
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
		if (! isset($proyecto)) {$proyecto = $this->id;}
		if ( toba::nucleo()->utilizar_metadatos_compilados( $proyecto ) ) {
			$rs = $this->recuperar_datos_compilados('toba_mc_gene__basicos','info_fuente__'.$id_fuente);
		} else {
			if (! isset($proyecto)) {$proyecto = $this->id;}
			$rs = toba_proyecto_db::get_info_fuente_datos($proyecto, $id_fuente);
			//-- No se carga aqui la relacion entre tabla y dt por un tema de eficiencia, se hace con un lazyload en toba_fuente_datos
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
		if (!isset($proyecto)) { $proyecto = $this->id;}
		if (!isset($grupos_acceso)) { $grupos_acceso = toba::manejador_sesiones()->get_perfiles_funcionales_activos();}
		if ( toba::nucleo()->utilizar_metadatos_compilados( $proyecto ) ) {
			$rs = $this->recuperar_datos_compilados_grupo(	'toba_mc_gene__grupo_', 
													$grupos_acceso, 
													'get_items_menu',
													true,
													array('padre','orden'));
		} else {
			$rs = toba_proyecto_db::get_items_menu($proyecto, $grupos_acceso);
		}
		// Se quitan los items excluidos de la lista de items que puede acceder el usuario.
		$res = array();
		foreach ($rs as $r) {
			if (!in_array($r['item'],$this->items_excluidos)){
				$res[] = $r;
			}
		}
		return $res;
	}	

	function quitar_item_menu($item)
	{
		$this->items_excluidos[] = $item;
	}

	/**
	 * Valida que un grupo de acceso tenga acceso a un item
	 */
	function puede_grupo_acceder_item($item)
	{
		$grupos_acceso = toba::manejador_sesiones()->get_perfiles_funcionales_activos();
		//Recupero los items y los formateo en un indice consultable
		if(!isset($this->indice_items_accesibles)) {
			$this->indice_items_accesibles = array();
			if ( toba::nucleo()->utilizar_metadatos_compilados( $this->id, 'compilados' ) ) {
				///-- Metadatos compilados
				if (! empty($grupos_acceso)) {
					//-- Busca los items accesibles por grupo
					$this->indice_items_accesibles = $this->recuperar_datos_compilados_grupo('toba_mc_gene__grupo_', 
																								$grupos_acceso, 
																								'get_items_accesibles',
																								false);
				} else {
					//-- Si no tiene grupo, busca aquellos items que son públicos
					$this->indice_items_accesibles = $this->recuperar_datos_compilados_grupo('toba_mc_gene__items_', 
																								array('publicos'), 
																								'get_items_accesibles',
																								false);			
				}
			} else {
				//-- Metadatos salen de la base
				$rs = toba_proyecto_db::get_items_accesibles($this->id, $grupos_acceso);
				foreach( $rs as $accesible ) {
					$this->indice_items_accesibles[$accesible['proyecto'].'-'.$accesible['item']] = 1;
				}
			}
		}
		return isset($this->indice_items_accesibles[$this->id.'-'.$item]);
	}

	/**
	*	Devuelve la lista de items de la zona a los que puede acceder el grupo actual
	*/
	function get_items_zona($zona, $grupos_acceso=null)
	{
		if (!isset($grupos_acceso)) { $grupos_acceso = toba::manejador_sesiones()->get_perfiles_funcionales_activos();}
		if ( toba::nucleo()->utilizar_metadatos_compilados( $this->id ) ) {
			$rs = $this->recuperar_datos_compilados_grupo(	'toba_mc_gene__grupo_', 
															$grupos_acceso,
															'get_items_zona__'.$zona,
															true,
															array('orden') );
		} else {
			$rs = toba_proyecto_db::get_items_zona($this->id, $grupos_acceso, $zona);	
		}
		return $rs;
	}

	function get_perfiles_funcionales_usuario_anonimo()
	{
		$grupos = explode(',',$this->get_parametro('usuario_anonimo_grupos_acc'));
		$grupos = array_map('trim',$grupos);
		return $grupos;
	}
	
	/**
	 * @deprecated Desde 1.5 usar get_perfiles_funcionales_usuario_anonimo
	 */
	function get_grupos_acceso_usuario_anonimo()
	{
		return $this->get_perfiles_funcionales_usuario_anonimo();
	}	
	
	
	function get_perfiles_funcionales_asociados($perfil)
	{
		if ( toba::nucleo()->utilizar_metadatos_compilados( $this->id ) ) {
			$rs = $this->recuperar_datos_compilados_grupo(	'toba_mc_gene__grupo_',  array($perfil),	'get_membresia', true);
			$perfiles = $rs;
			foreach ($rs as $perfil_miembro) {
				$perfiles = array_merge($perfiles, $this->get_perfiles_funcionales_asociados($perfil_miembro));
			}
		} else {
			$perfiles = toba_proyecto_db::get_perfiles_funcionales_asociados($this->id, $perfil);	
		}
		return $perfiles;		
	}

	//------------------------  Permisos  -------------------------
	
	/**
	 * Retorna la lista de permisos globales (tambien llamados derechos) de un grupo de acceso en el proyecto actual
	 */
	function get_lista_permisos($grupos_acceso=null)
	{
		$grupos_acceso = isset($grupos_acceso) ? $grupos_acceso : toba::manejador_sesiones()->get_perfiles_funcionales_activos();
		if ( toba::nucleo()->utilizar_metadatos_compilados( $this->id ) ) {
			$rs = $this->recuperar_datos_compilados_grupo('toba_mc_gene__grupo_', $grupos_acceso, 'get_lista_permisos');
		} else {
			$rs = toba_proyecto_db::get_lista_permisos($this->id, $grupos_acceso);
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
			$rs = toba_proyecto_db::get_descripcion_permiso($this->id, $permiso);
		}
		return $rs;
	}

	//------------------------  MENSAJES  -------------------------

	function get_mensaje_toba($indice)
	{
		if ( toba::nucleo()->utilizar_metadatos_compilados( $this->id ) ) {
			$clase = 'toba_mc_gene__msj_toba';
			$metodo = 'get__'.$indice;
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
			$rs = toba_proyecto_db::get_mensaje_proyecto($this->id, $indice);
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
				$rs = false;	//Para ser coherentes en la respuesta con el else
			}
		} else {
			$rs = toba_proyecto_db::get_mensaje_objeto($this->id, $objeto, $indice);	
		}
		return $rs;
	}

	//------------------------  CONSULTAS PHP  -------------------------

	function get_info_consulta_php($clase, $proyecto=null)
	{
		if (! isset($proyecto)) { $proyecto = $this->id;}
		if ( toba::nucleo()->utilizar_metadatos_compilados( $proyecto ) ) {
			$rs = $this->recuperar_datos_compilados('toba_mc_gene__consultas_php','info_consulta_php__'.$clase);
		} else {
			$rs = toba_proyecto_db::get_consulta_php($proyecto, $clase);
		}
		if (empty($rs)) {
			throw new toba_error("No se puede encontrar la consulta PHP '$clase' en el proyecto '$proyecto'");	
		}
		return $rs;
	}

	//------------------------  SERVICIOS WEB  -------------------------

	function get_info_servicios_web_acc($id, $proyecto=null)
	{
		if (! isset($proyecto)) {$proyecto = $this->id;}
		if ( toba::nucleo()->utilizar_metadatos_compilados( $proyecto ) ) {
			$rs = $this->recuperar_datos_compilados('toba_mc_gene__servicios_web','servicio__'.$id);
		} else {
			$rs = toba_proyecto_db::get_info_servicio_web($proyecto, $id);
		}
		if (empty($rs)) {
			throw new toba_error("No se puede encontrar la definición del Servicio Web '$id' en el proyecto '$proyecto'");	
		}
		return $rs;
	}	

	//------------------------  PUNTOS DE MONTAJE  -------------------------

	function get_info_pms($proyecto = null)
	{
		if (is_null($proyecto)) {$proyecto = $this->id;}
		if (toba::nucleo()->utilizar_metadatos_compilados($proyecto)) {
			$rs = $this->recuperar_datos_compilados('toba_mc_gene__pms', 'get_pms');
		} else {
			$rs = toba_proyecto_db::get_pms($proyecto);
		}
		if (empty($rs)) {
			throw new toba_error("No se pueden encontrar puntos de montaje en el proyecto '$proyecto'");
		}

		return $rs;
	}

	
	//------------------------  GADGETS  -------------------------
	/**
	 * Recupera los gadgets disponibles en la base de datos
	 * para el usuario en un proyecto especifico
	 * @param string $usuario	Id del usuario
	 * @param string $proyecto	Id del proyecto
	 * @return array Arreglo con objetos toba_gadget o vacio en su defecto.
	 */
	function get_gadgets_proyecto($usuario, $proyecto=null)
	{
		if (is_null($proyecto)) { $proyecto = $this->id; }			//Si no pasan proyecto tomo el actual
		$gadgets = array();
		$info_gadgets = toba_proyecto_db::get_gadgets_proyecto($proyecto, $usuario);
		if (! empty($info_gadgets)){
			foreach($info_gadgets as $info) {
				$gadgets[] = $this->get_objeto_gadget($info);
			}
		}
		return $gadgets;
	}

	/**
	 * Instancia el objeto correspondiente de acuerdo a la informacion recibida
	 * ademas asigna los valores de las propiedades existentes
	 * @param <type> $info
	 * @ignore
	 */
	private function get_objeto_gadget($info)
	{
		switch ($info['tipo_gadget']) {
			case apex_tipo_gadget_shindig:

								$objeto = new toba_gadget_shindig($info['gadget']);
								$objeto->set_gadget_url($info['gadget_url']);
								break;
			case apex_tipo_gadget_interno:
				
							if (! isset($info['subclase']) || ! isset($info['subclase_archivo'])) {
								throw new toba_error_def('La definición de subclase para el gadget esta incompleta');
							} else {
								$clase = $info['subclase'];
								require_once($info['subclase_archivo']);
								$objeto = new $clase($info['gadget']);
								$objeto->set_clase($clase, $info['subclase_archivo']);
							}
							break;
			default:
							throw new toba_error_def('El tipo de gadget recuperado no es válido.');
		}
		if (isset($info['titulo'])) {$objeto->set_titulo($info['titulo']);}
		if (isset($info['descripcion'])) {$objeto->set_descripcion($info['descripcion']);}
		if (isset($info['orden'])) {$objeto->set_orden($info['orden']);}
		if (isset($info['eliminable'])) {$objeto->set_eliminable($info['eliminable']);}

		return $objeto;
	}

	//-- Soporte a la compilacion ----------------------
	
	static function existe_dato_compilado($clase, $metodo)
	{
		return in_array($metodo, get_class_methods($clase));
	}
	
	static function recuperar_datos_compilados($clase, $metodo)
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
			$temp = rs_ordenar_por_columnas($temp, $orden);
		}
		return $temp;
	}
	
	//-- Configuracion minima del logger de proyecto -------------------
	function configurar_logger()
	{
		if (defined('apex_pa_log_archivo_nivel')) {
			toba::logger()->set_nivel(apex_pa_log_archivo_nivel);
		} else {
			toba::logger()->set_nivel($this->memoria['log_archivo_nivel']);
		}
		if ((!defined('apex_pa_log_archivo') && !$this->memoria['log_archivo'])
				|| (defined('apex_pa_log_archivo') && !apex_pa_log_archivo)) {
			toba::logger()->desactivar();
		}
	}
	
	function configurar_logger_ws()
	{
		if (defined('apex_pa_log_archivo_nivel')) {
			toba::logger_ws()->set_nivel(apex_pa_log_archivo_nivel);
		} else {
			toba::logger_ws()->set_nivel($this->memoria['log_archivo_nivel']);
		}
		if ((!defined('apex_pa_log_archivo') && !$this->memoria['log_archivo'])
				|| (defined('apex_pa_log_archivo') && !apex_pa_log_archivo)) {
			toba::logger_ws()->desactivar();
		}	
	}
}
?>