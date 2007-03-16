<?php
require_once('toba_instancia.php');

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
	static function instancia()
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new toba_proyecto();	
		}
		return self::$instancia;
	}

	static function eliminar_instancia()
	{
		self::$instancia = null;
	}
	
	private function __construct()
	{
		$this->memoria =& toba::manejador_sesiones()->segmento_info_proyecto();
		if (!$this->memoria) {
			$this->memoria = self::cargar_info_basica();
			toba::logger()->debug('Inicalizacion de TOBA_PROYECTO: ' . self::get_id(),'toba');
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
	static function get_db()
	{
		return toba::instancia()->get_db();
	}
		
	function cargar_info_basica($proyecto=null)
	{
		$proyecto = isset($proyecto) ? $proyecto : self::get_id();
		$sql = "SELECT	proyecto as				nombre,
						p.descripcion as		descripcion,
						descripcion_corta				,
						estilo							,
						con_frames						,
						frames_clase					,
						frames_archivo					,
						salida_impr_html_c				,
						salida_impr_html_a				,
						m.menu as				menu,
						m.archivo as			menu_archivo,
						path_includes					,
						path_browser					,
						administrador					,
						listar_multiproyecto			,
						orden							,
						palabra_vinculo_std				,
						version_toba					,
						requiere_validacion				,
						usuario_anonimo					,
						usuario_anonimo_desc			,
						usuario_anonimo_grupos_acc		,
						validacion_intentos				,
						validacion_intentos_min			,
						validacion_debug				,
						sesion_tiempo_no_interac_min	,
						sesion_tiempo_maximo_min		,
						sesion_subclase					,
						sesion_subclase_archivo			,
						contexto_ejecucion_subclase		,
						contexto_ejecucion_subclase_archivo	,
						usuario_subclase				,
						usuario_subclase_archivo		,
						encriptar_qs					,
						registrar_solicitud				,
						registrar_cronometro			,
						item_inicio_sesion      		,
						item_pre_sesion   		       	,
						item_set_sesion					,
						log_archivo						,
						log_archivo_nivel				,
						fuente_datos					,
						version							,
						version_fecha					,
						version_detalle					,
						version_link
				FROM 	apex_proyecto p LEFT OUTER JOIN apex_menu m
						ON (p.menu = m.menu)
				WHERE	proyecto = '$proyecto';";
		$rs = self::get_db()->consultar($sql);
		if (empty($rs)) {
			throw new toba_error("El proyecto '".toba_proyecto::get_id()."' no se encuentra cargado en la instancia ".toba_instancia::get_id());	
		}
		return $rs[0];
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

	function get_definicion_dependencia($objeto, $identificador, $proyecto=null)
	{
		$proyecto = isset($proyecto) ? $proyecto : toba_proyecto::get_id() ;
		$sql = "SELECT 
					'$identificador' 	as identificador,
					o.proyecto 			as proyecto,
					o.objeto 			as objeto,
					o.fuente_datos		as fuente,
					o.clase				as clase,
					o.subclase			as subclase,
					o.subclase_archivo	as subclase_archivo,
					c.archivo			as clase_archivo
				FROM
					apex_objeto o,
					apex_clase c
				WHERE
					o.objeto = '$objeto' AND
					o.proyecto = '$proyecto' AND
					o.clase = c.clase AND
					o.clase_proyecto = c.proyecto";
		$res = self::get_db()->consultar($sql);
		return $res[0];
	}

	//------------------------  FUENTES  -------------------------

	function get_info_fuente_datos($id_fuente, $proyecto=null)
	{
		if (! isset($proyecto)) {
			$proyecto = toba_proyecto::get_id();
		}
		$sql = "SELECT 	*,
						link_instancia 		as link_base_archivo,
						fuente_datos_motor 	as motor,
						host 				as profile
				FROM 	apex_fuente_datos
				WHERE	fuente_datos = '$id_fuente'
				AND 	proyecto = '$proyecto'";
		$rs = self::get_db()->consultar($sql);
		if (empty($rs)) {
			throw new toba_error("No se puede encontrar la fuente '$id_fuente' en el proyecto '$proyecto'");	
		}
		return $rs[0];
	}
	
	//------------------------  ITEMS  -------------------------

	/**
	 * Retorna la lista de items a los que puede acceder el usuario
	 *
	 * @param unknown_type $solo_primer_nivel
	 * @param string $proyecto Por defecto el actual
	 * @param string $grupo_acceso Por defecto el del usuario actual
	 * @return array RecordSet contienendo información de los items
	 */
	function get_items_menu($solo_primer_nivel=false, $proyecto=null, $grupo_acceso=null)
	{
		$rest = "";
		if ($solo_primer_nivel) {
			$rest = " AND i.padre = '__raiz__' ";
		}
		if (!isset($proyecto)) {
			$proyecto = self::get_id();
		}
		if (!isset($grupo_acceso)) {
			$grupo_acceso = toba::manejador_sesiones()->get_grupo_acceso();	
		}
		$sql = "SELECT 	i.padre as 		padre,
						i.carpeta as 	carpeta, 
						i.proyecto as	proyecto,
						i.item as 		item,
						i.nombre as 	nombre,
						i.imagen,
						i.imagen_recurso_origen
				FROM 	apex_item i LEFT OUTER JOIN	apex_usuario_grupo_acc_item u ON
							(	i.item = u.item AND i.proyecto = u.proyecto	)
				WHERE
					(i.menu = 1)
				AND	(u.usuario_grupo_acc = '$grupo_acceso' OR i.publico = 1)
				AND (i.item <> '__raiz__')
				$rest
				AND		(i.proyecto = '$proyecto')
				ORDER BY i.padre,i.orden;";
		return self::get_db()->consultar($sql);
	}	

	/**
	*	Devuelve la lista de items a los que un grupo puede acceder
	*/
	function get_vinculos_posibles($grupo_acceso=null)
	{
		if (!isset($grupo_acceso)) {
			$grupo_acceso = toba::manejador_sesiones()->get_grupo_acceso();	
		}
		$sql = "SELECT	i.proyecto as proyecto,
						i.item as item
				FROM	apex_item i,
						apex_usuario_grupo_acc_item ui
				WHERE	(i.carpeta <> 1 OR i.carpeta IS NULL)
				AND		ui.item = i.item
				AND		ui.proyecto = i.proyecto
				AND		ui.usuario_grupo_acc = '$grupo_acceso';";
		return $this->get_db()->consultar($sql);
	}

	/**
	 * Valida que un grupo de acceso tenga acceso a un item
	 * @return toba_error Si el grupo no tiene permisos suficientes
	 */
	static function puede_grupo_acceder_item($grupo_acceso, $item)
	{
		$sql = "	SELECT	1 as ok
					FROM	apex_usuario_grupo_acc_item ui,
							apex_usuario_proyecto up
					WHERE	ui.usuario_grupo_acc = up.usuario_grupo_acc
					AND	ui.proyecto	= up.proyecto
					AND	up.usuario_grupo_acc = '$grupo_acceso'
					AND	ui.proyecto = '{$item[0]}'
					AND	ui.item =	'{$item[1]}';";
		$datos = self::get_db()->consultar($sql);
		return ( ! empty($datos) );
	}

	//------------------------  Permisos  -------------------------
	
	function get_grupo_acceso_usuario_anonimo()
	{
		//$grupos = explode(',',$this->get_parametro('usuario_anonimo_grupos_acc'));
		//$grupos = array_map('trim',$grupos);
		//return $grupos;
		return $this->get_parametro('usuario_anonimo_grupos_acc');
	}
	
	/**
	 * Retorna la lista de permisos globales (tambien llamados particulares) de un grupo de acceso en el proyecto actual
	 */
	static function get_lista_permisos($grupo)
	{
		$sql = " 
			SELECT 
				per.nombre as nombre
			FROM
				apex_permiso_grupo_acc per_grupo,
				apex_permiso per
			WHERE
				per_grupo.proyecto = '".self::get_id()."'
			AND	per_grupo.usuario_grupo_acc = '$grupo'
			AND	per_grupo.permiso = per.permiso
			AND	per_grupo.proyecto = per.proyecto
		";
		return self::get_db()->consultar($sql);
	}
	
	/**
	 * Retorna la descripción asociada a un permiso global particular del proy. actual
	 */
	static function get_descripcion_permiso($permiso)
	{
		$sql = " 
			SELECT
				per.descripcion,
				per.mensaje_particular
			FROM
				apex_permiso per
			WHERE
				per.proyecto = '".toba_proyecto::get_id()."'
			AND	per.nombre = '$permiso'
		";
		return self::get_db()->consultar($sql);
	}

	//------------------------  MENSAJES  -------------------------

	static function get_mensaje_toba($indice)
	{
		$sql = "SELECT
					COALESCE(mensaje_customizable, mensaje_a) as m
				FROM apex_msg 
				WHERE indice = '$indice'
				AND proyecto = 'toba';";
		return self::get_db()->consultar($sql);	
	}
	
	static function get_mensaje_proyecto($indice)
	{
		$sql = "SELECT
					COALESCE(mensaje_customizable, mensaje_a) as m
				FROM apex_msg 
				WHERE indice = '$indice'
				AND proyecto = '".toba_proyecto::get_id()."';";
		return self::get_db()->consultar($sql);	
	}

	static function get_mensaje_objeto($objeto, $indice)
	{
		$sql = "SELECT
					COALESCE(mensaje_customizable, mensaje_a) as m
				FROM apex_objeto_msg 
				WHERE indice = '$indice'
				AND objeto_proyecto = '".toba_proyecto::get_id()."'
				AND objeto = '$objeto';";
		return self::get_db()->consultar($sql);	
	}

	//------------------------  ZONA  -------------------------

	static function get_items_zona($zona, $usuario)
	{
		$sql = "SELECT	i.proyecto as 					item_proyecto,
						i.item as						item,
						i.zona_orden as					orden,
						i.imagen as						imagen,
						i.imagen_recurso_origen as		imagen_origen,
						i.nombre as						nombre,
						i.descripcion as				descripcion
				FROM	apex_item i,
						apex_usuario_grupo_acc_item ui,
						apex_usuario_proyecto up
				WHERE	i.zona = '$zona'
				AND		i.zona_proyecto = '".toba_proyecto::get_id()."'
				AND 	ui.item = i.item
				AND		ui.proyecto = i.proyecto
				AND		ui.usuario_grupo_acc = up.usuario_grupo_acc
                AND     ui.proyecto = up.proyecto
                AND     up.usuario = '$usuario'
				AND		i.zona_listar = 1
				ORDER BY 3;";
		return self::get_db()->consultar($sql);	
	}

	//------------------------  Descripcion ITEM consola  -------------------------

	static function get_menu_consola($proyecto, $item)
	{
		$sql = "SELECT descripcion_breve, descripcion_larga
				FROM apex_item_info
				WHERE item_proyecto = '$proyecto'
				AND item = '$item';";
		return self::get_db()->consultar($sql);
	}
}
?>
