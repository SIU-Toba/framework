<?php
require_once('toba_instancia.php');
/**
 * Recuperacion de informacion del proyecto de la base de datos
 * @package Centrales
 */
class toba_proyecto_db
{
	private static $db;
	
	static function set_db($db)
	{
		self::$db = $db;
	}
	
	static function get_db()
	{
		return self::$db;
	}

	static function cargar_info_basica($proyecto)
	{
		$sql = "SELECT	p.proyecto as				nombre,
						p.descripcion as		descripcion,
						descripcion_corta				,
						p.estilo							,
						est.proyecto as 		estilo_proyecto,
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
				FROM 	apex_proyecto p 
							LEFT OUTER JOIN apex_menu m ON (p.menu = m.menu)
							LEFT OUTER JOIN apex_estilo est ON (p.estilo = est.estilo)
				WHERE	p.proyecto = '$proyecto';";
		$rs = self::get_db()->consultar($sql);
		if(isset($rs[0])) return $rs[0];
	}

	static function get_info_fuente_datos($proyecto, $id_fuente)
	{
		$sql = "SELECT 	*,
						link_instancia 		as link_base_archivo,
						fuente_datos_motor 	as motor,
						host 				as profile
				FROM 	apex_fuente_datos
				WHERE	fuente_datos = '$id_fuente'
				AND 	proyecto = '$proyecto'";
		return self::get_db()->consultar($sql);
	}

	static function get_descripcion_permiso($proyecto, $permiso)
	{
		$sql = "	SELECT
						per.descripcion,
						per.mensaje_particular
					FROM
						apex_permiso per
					WHERE
						per.proyecto = '$proyecto'
					AND	per.nombre = '$permiso'
		";
		return self::get_db()->consultar($sql);
	}

	static function get_definicion_dependencia($objeto, $identificador, $proyecto)
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
					o.proyecto = '$proyecto' AND
					o.clase = c.clase AND
					o.clase_proyecto = c.proyecto";
		return self::get_db()->consultar($sql);
	}	

	//---------------------  Grupos de Acceso  -------------------------
		
	static function get_items_menu($proyecto, $grupo_acceso)
	{
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
				AND		(i.proyecto = '$proyecto')
				ORDER BY i.padre,i.orden;";
		return self::get_db()->consultar($sql);
	}	

	function get_items_accesibles($proyecto, $grupo_acceso)
	{
		$sql = "SELECT	i.proyecto as proyecto,
						i.item as item
				FROM	apex_item i,
						apex_usuario_grupo_acc_item ui
				WHERE	(i.carpeta <> 1 OR i.carpeta IS NULL)
				AND		ui.item = i.item
				AND		ui.proyecto = i.proyecto
				AND		ui.proyecto = '$proyecto'
				AND		ui.usuario_grupo_acc = '$grupo_acceso';";
		return self::get_db()->consultar($sql);
	}

	/**
	*	Devuelve la lista de items de la zona a los que puede acceder el grupo actual
	*/
	static function get_items_zona($proyecto, $zona, $grupo_acceso)
	{
		$sql = "SELECT	i.proyecto as 					item_proyecto,
						i.item as						item,
						i.zona_orden as					orden,
						i.imagen as						imagen,
						i.imagen_recurso_origen as		imagen_origen,
						i.nombre as						nombre,
						i.descripcion as				descripcion
				FROM	apex_item i,
						apex_usuario_grupo_acc_item ui
				WHERE	i.zona = '$zona'
				AND		i.zona_proyecto = '$proyecto'
				AND 	ui.item = i.item
				AND		ui.proyecto = i.proyecto
				AND		ui.usuario_grupo_acc = '$grupo_acceso'
				AND		i.zona_listar = 1
				ORDER BY 3;";
		return self::get_db()->consultar($sql);	
	}

	static function get_lista_permisos($proyecto, $grupo)
	{
		$sql = " 
			SELECT 
				per.nombre as nombre
			FROM
				apex_permiso_grupo_acc per_grupo,
				apex_permiso per
			WHERE
				per_grupo.proyecto = '$proyecto'
			AND	per_grupo.usuario_grupo_acc = '$grupo'
			AND	per_grupo.permiso = per.permiso
			AND	per_grupo.proyecto = per.proyecto
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
	
	static function get_mensaje_proyecto($proyecto, $indice)
	{
		$sql = "SELECT
					COALESCE(mensaje_customizable, mensaje_a) as m
				FROM apex_msg 
				WHERE indice = '$indice'
				AND proyecto = '$proyecto';";
		return self::get_db()->consultar($sql);	
	}

	static function get_mensaje_objeto($proyecto, $objeto, $indice)
	{
		$sql = "SELECT
					COALESCE(mensaje_customizable, mensaje_a) as m
				FROM apex_objeto_msg 
				WHERE indice = '$indice'
				AND objeto_proyecto = '$proyecto'
				AND objeto = '$objeto';";
		return self::get_db()->consultar($sql);	
	}
}
?>