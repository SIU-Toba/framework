<?php
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
						validacion_bloquear_usuario		,
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
		return self::get_db()->consultar_fila($sql);
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
		return self::get_db()->consultar_fila($sql);
	}

	static function get_mapeo_tabla_dt($proyecto, $id_fuente)
	{
		$sql = "
			SELECT 
				dt.objeto,
				dt.tabla
			FROM 
				apex_objeto_db_registros as dt,
				apex_objeto as comp
			WHERE
					dt.objeto_proyecto = '$proyecto'
				AND dt.objeto = comp.objeto
				AND dt.objeto_proyecto = comp.proyecto
				AND comp.fuente_datos = '$id_fuente'
		";
		$rs = self::get_db()->consultar($sql);
		return rs_convertir_asociativo($rs, array('tabla'), 'objeto');
	}

	static function get_mapeo_cn($proyecto)
	{
		$sql = "
			SELECT objeto, nombre
			FROM apex_objeto
			WHERE proyecto = '$proyecto'
			AND clase = 'toba_cn'";
		$rs = self::get_db()->consultar($sql);
		return rs_convertir_asociativo($rs, array('nombre'), 'objeto');
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
		return self::get_db()->consultar_fila($sql);
	}

	//---------------------  Puntos de control  -------------------------

    static function punto_control_parametros($proyecto, $pto_control)
    {
      return self::get_db()->consultar(
          "SELECT parametro
             FROM apex_ptos_control_param 
            WHERE proyecto    = '$proyecto'
              AND pto_control = '$pto_control'
        "); 
    }

    static function punto_control_controles($proyecto, $pto_control)
    {
      return self::get_db()->consultar(
          "SELECT archivo,
                  clase,
                  actua_como
             FROM apex_ptos_control_ctrl 
            WHERE proyecto    = '$proyecto'
              AND pto_control = '$pto_control'
        ");
    }

	//---------------------  Grupos de Acceso  -------------------------
		
	static function get_items_menu($proyecto, $grupos_acceso)
	{
		$raiz = self::get_item_raiz($proyecto);
		$grupos_acceso = "'" . implode("','", $grupos_acceso) . "'";
		$sql = "SELECT DISTINCT
						i.padre as 		padre,
						i.carpeta as 	carpeta, 
						i.proyecto as	proyecto,
						i.item as 		item,
						i.nombre as 	nombre,
						i.orden as 		orden,
						i.imagen,
						i.imagen_recurso_origen,
						i.padre = '$raiz' as es_primer_nivel
				FROM 	apex_item i 
							LEFT OUTER JOIN	apex_usuario_grupo_acc_item u 
								ON	(	i.item = u.item AND i.proyecto = u.proyecto	)
				WHERE
					(i.menu = 1)
				AND i.item != i.padre	--no es raiz
				AND	(u.usuario_grupo_acc IN ($grupos_acceso) OR i.publico = 1)
				AND		(i.proyecto = '$proyecto')
				ORDER BY i.padre,i.orden;";
		return self::get_db()->consultar($sql);
	}	

	static function get_item_raiz($proyecto)
	{
		$sql = "
			SELECT 
				item
			FROM apex_item 
			WHERE 
					proyecto = '$proyecto'
				AND item = padre
		";
		$datos = self::get_db()->consultar_fila($sql);
		return $datos['item'];
	}	
	
	static function get_items_accesibles($proyecto, $grupos_acceso)
	{
		$grupos_acceso = "'" . implode("','", $grupos_acceso) . "'";
		$sql = "SELECT DISTINCT
						i.proyecto as proyecto,
						i.item as item
				FROM	apex_item i 
								LEFT OUTER JOIN apex_usuario_grupo_acc_item ui ON 
									(i.item = ui.item AND i.proyecto = ui.proyecto)
				WHERE	(i.carpeta <> 1 OR i.carpeta IS NULL)
				AND		i.proyecto = '$proyecto'
				AND		(ui.usuario_grupo_acc IN ($grupos_acceso) OR i.publico = 1)";
		return self::get_db()->consultar($sql);
	}

	/**
	*	Devuelve la lista de items de la zona a los que puede acceder el grupo actual
	*/
	static function get_items_zona($proyecto, $grupos_acceso, $zona)
	{
		$grupos_acceso = "'" . implode("','", $grupos_acceso) . "'";
		$sql = "SELECT	DISTINCT
						i.proyecto as 					item_proyecto,
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
				AND		ui.usuario_grupo_acc IN ($grupos_acceso)
				AND		i.zona_listar = 1
				ORDER BY 3;";
		return self::get_db()->consultar($sql);	
	}

	static function get_lista_permisos($proyecto, $grupos_acceso)
	{
		$grupos_acceso = "'" . implode("','", $grupos_acceso) . "'";
		$sql = " 
			SELECT DISTINCT
				per.nombre as nombre
			FROM
				apex_permiso_grupo_acc per_grupo,
				apex_permiso per
			WHERE
				per_grupo.proyecto = '$proyecto'
			AND	per_grupo.usuario_grupo_acc IN ($grupos_acceso)
			AND	per_grupo.permiso = per.permiso
			AND	per_grupo.proyecto = per.proyecto
		";
		return self::get_db()->consultar($sql);
	}

	//------------------------  DIMENSIONES -----------------------

	static function get_info_dimension($proyecto, $dimension)
	{
		$datos_dim = array();
		$sql = " 	SELECT	proyecto,
							dimension,					
							nombre,						
							descripcion,					
							schema,						
							tabla,						
							col_id,						
							col_desc,					
							col_desc_separador,			
							multitabla_col_tabla,		
							multitabla_id_tabla,			
							fuente_datos_proyecto,			
							fuente_datos					
					FROM apex_dimension
					WHERE proyecto = '$proyecto'
					AND	dimension = '$dimension'";
		$datos_dim = self::get_db()->consultar_fila($sql);
		if($datos_dim) {
			$sql = " 	SELECT	proyecto,					
								dimension,					
								gatillo,						
								tipo,						
								orden,						
								tabla_rel_dim,				
								columnas_rel_dim,			
								tabla_gatillo,				
								ruta_tabla_rel_dim			
						FROM apex_dimension_gatillo
						WHERE proyecto = '$proyecto'
						AND	dimension = '$dimension'
						ORDER BY tipo, orden";
			$datos_dim['gatillos'] = self::get_db()->consultar($sql);
		}
		return $datos_dim;
	}

	static function get_relaciones_entre_tablas($proyecto)
	{
		$sql = " 	SELECT	proyecto,
							fuente_datos_proyecto,
							fuente_datos,					
							relacion_tablas,					
							tabla_1,							
							tabla_1_cols,					
							tabla_2,							
							tabla_2_cols					
					FROM	apex_relacion_tablas
					WHERE proyecto = '$proyecto'";
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
		return self::get_db()->consultar_fila($sql, toba_db_fetch_asoc, false);	
	}
	
	static function get_mensaje_proyecto($proyecto, $indice)
	{
		$sql = "SELECT
					COALESCE(mensaje_customizable, mensaje_a) as m
				FROM apex_msg 
				WHERE indice = '$indice'
				AND proyecto = '$proyecto';";
		return self::get_db()->consultar_fila($sql, toba_db_fetch_asoc, false);	
	}

	static function get_mensaje_objeto($proyecto, $objeto, $indice)
	{
		$sql = "SELECT
					COALESCE(mensaje_customizable, mensaje_a) as m
				FROM apex_objeto_msg 
				WHERE indice = '$indice'
				AND objeto_proyecto = '$proyecto'
				AND objeto = '$objeto';";
		return self::get_db()->consultar_fila($sql);	
	}
}
?>