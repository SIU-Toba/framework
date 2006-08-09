<?php
require_once('info_instancia.php');

class info_proyecto
{
	static private $instancia;
	const prefijo_punto_acceso = 'pa_';

	static function get_id()
	{
		if(! defined('apex_pa_proyecto') ){
			throw new excepcion_toba("Es necesario definir la constante 'apex_pa_proyecto'");
		}
		return apex_pa_proyecto;
	}
	
	/**
	 * @return info_proyecto
	 */
	static function instancia()
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new info_proyecto();	
		}
		return self::$instancia;	
	}

	private function __construct()
	{
		if ( !isset($_SESSION['toba']['proyectos'][self::get_id()])) {
			$_SESSION['toba']['proyectos'][self::get_id()] = self::cargar_info_basica();
		}
	}

	function limpiar_memoria()
	{
		unset($_SESSION['toba']['proyectos'][self::get_id()]);
		self::$instancia = null;
	}

	function get_parametro($id)
	{
		if( defined( self::prefijo_punto_acceso . $id ) ){
			return constant(self::prefijo_punto_acceso . $id);
		} elseif (isset($_SESSION['toba']['proyectos'][self::get_id()][$id])) {
			return $_SESSION['toba']['proyectos'][self::get_id()][$id];
		} else {
			if( array_key_exists($id,$_SESSION['toba']['proyectos'][self::get_id()])) {
				return null;
			}else{
				throw new excepcion_toba("INFO_PROYECTO: El parametro '$id' no se encuentra definido.");
			}
		}	
	}

	function set_parametro($id, $valor)
	{
		$_SESSION['toba']['proyectos'][self::get_id()][$id] = $valor;
	}

	//----------------------------------------------------------------
	// DATOS
	//----------------------------------------------------------------	

	static function get_db()
	{
		return info_instancia::instancia()->get_db();
	}
		
	function cargar_info_basica()
	{
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
						validacion_intentos				,
						validacion_intentos_min			,
						validacion_debug				,
						sesion_tiempo_no_interac_min	,
						sesion_tiempo_maximo_min		,
						sesion_subclase					,
						sesion_subclase_archivo			,
						usuario_subclase				,
						usuario_subclase_archivo		,
						encriptar_qs					,
						combo_cambiar_proyecto			,
						registrar_solicitud				,
						registrar_cronometro			,
						item_inicio_sesion      		,
						item_pre_sesion   		       	,
						log_archivo						,
						log_archivo_nivel				,
						fuente_datos
				FROM 	apex_proyecto p LEFT OUTER JOIN apex_menu m
						ON (p.menu = m.menu)
				WHERE	proyecto = '".info_proyecto::get_id()."';";
		$rs = self::get_db()->consultar($sql);
		return $rs[0];
	}

	//--------------  Carga dinamica de COMPONENTES --------------

	function get_definicion_dependencia($objeto, $identificador)
	{
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
					o.proyecto = '".info_proyecto::get_id()."' AND
					o.clase = c.clase AND
					o.clase_proyecto = c.proyecto";
		$res = self::get_db()->consultar($sql);
		return $res[0];
	}

	//------------------------  FUENTES  -------------------------

	function get_info_fuente_datos($id_fuente)
	{
		$sql = "SELECT 	*,
						link_instancia 		as link_base_archivo,
						fuente_datos_motor 	as motor,
						host 				as profile
				FROM 	apex_fuente_datos
				WHERE	fuente_datos = '$id_fuente'
				AND 	proyecto = '".info_proyecto::get_id()."';";
		$rs = self::get_db()->consultar($sql);
		return $rs[0];
	}
	
	//------------------------  ITEMS  -------------------------

	function items_menu($solo_primer_nivel=false, $proyecto, $grupo_acceso)
	{
		$rest = "";
		if ($solo_primer_nivel) {
			$rest = " AND i.padre = '' ";
		}
		$grupo = toba::get_hilo()->obtener_usuario_grupo_acceso();
		$sql = "SELECT 	i.padre as 		padre,
						i.carpeta as 	carpeta, 
						i.proyecto as	proyecto,
						i.item as 		item,
						i.nombre as 	nombre
				FROM 	apex_item i LEFT OUTER JOIN	apex_usuario_grupo_acc_item u ON
							(	i.item = u.item AND i.proyecto = u.proyecto	)
				WHERE
					(i.menu = 1)
				AND	(u.usuario_grupo_acc = '$grupo_acceso' OR i.publico = 1)
				AND (i.item <> '')
				$rest
				AND		(i.proyecto = '$proyecto')
				ORDER BY i.padre,i.orden;";
		return self::get_db()->consultar($sql);
	}	

	static function control_acceso_item($item, $grupo_acceso)
	{
		$sql = "	SELECT	1 as ok
					FROM	apex_usuario_grupo_acc_item ui,
							apex_usuario_proyecto up
					WHERE	ui.usuario_grupo_acc = up.usuario_grupo_acc
					AND	ui.proyecto	= up.proyecto
					AND	up.usuario_grupo_acc = '$grupo_acceso'
					AND	ui.proyecto = '{$item[0]}'
					AND	ui.item =	'{$item[1]}';";
		$rs = self::get_db()->consultar($sql);
		if(empty($rs)){
			throw new excepcion_toba('El usuario no posee permisos para acceder al item solicitado.');
		}
	}

	//------------------------  Permisos  -------------------------
	
	static function get_lista_permisos($grupo)
	{
		$sql = " 
			SELECT 
				per.nombre as nombre
			FROM
				apex_permiso_grupo_acc per_grupo,
				apex_permiso per
			WHERE
				per_grupo.proyecto = '".info_proyecto::get_id()."'
			AND	per_grupo.usuario_grupo_acc = '$grupo'
			AND	per_grupo.permiso = per.permiso
			AND	per_grupo.proyecto = per.proyecto
		";
		return self::get_db()->consultar($sql);
	}
	
	static function get_descripcion_permiso($permiso)
	{
		$sql = " 
			SELECT
				per.descripcion,
				per.mensaje_particular
			FROM
				apex_permiso per
			WHERE
				per.proyecto = '".info_proyecto::get_id()."'
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
				AND proyecto = '".info_proyecto::get_id()."';";
		return self::get_db()->consultar($sql);	
	}

	static function get_mensaje_objeto($objeto, $indice)
	{
		$sql = "SELECT
					COALESCE(mensaje_customizable, mensaje_a) as m
				FROM apex_objeto_msg 
				WHERE indice = '$indice'
				AND objeto_proyecto = '".info_proyecto::get_id()."'
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
				AND		i.zona_proyecto = '".info_proyecto::get_id()."'
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
