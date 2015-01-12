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
		$db = self::get_db();
		$proyecto = $db->quote($proyecto);
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
						item_pre_sesion_popup	       	,
						item_set_sesion					,
						log_archivo						,
						log_archivo_nivel				,
						fuente_datos					,
						version							,
						version_fecha					,
						version_detalle					,
						version_link					,
						tiempo_espera_ms				,
						navegacion_ajax					,
						codigo_ga_tracker				,
						extension_toba					,
						extension_proyecto			,
						pm_impresion				  ,
						pm_sesion						,
						pm_contexto						,
						pm_usuario						,
						est.es_css3
				FROM 	apex_proyecto p 
							LEFT OUTER JOIN apex_menu_tipos m ON (p.menu = m.menu)
							LEFT OUTER JOIN apex_estilo est ON (p.estilo = est.estilo)
				WHERE	p.proyecto = $proyecto";
		return $db->consultar_fila($sql);
	}

	static function get_info_fuente_datos($proyecto, $id_fuente)
	{
		$db = self::get_db();
		$id_fuente = $db->quote($id_fuente);
		$proyecto = $db->quote($proyecto);
		$sql = "SELECT 	*,
						link_instancia 		as link_base_archivo,
						fuente_datos_motor 	as motor,
						host 				as profile
				FROM 	apex_fuente_datos
				WHERE	fuente_datos = $id_fuente
				AND 	proyecto = $proyecto";
		return $db->consultar_fila($sql);
	}
	
	static function get_info_fuente_schemas($proyecto, $id_fuente)
	{
		$db = self::get_db();
		$id_fuente = $db->quote($id_fuente);
		$proyecto = $db->quote($proyecto);
		$sql = "SELECT 	*
				FROM 	apex_fuente_datos_schemas		
				WHERE	fuente_datos = $id_fuente
				AND 	proyecto = $proyecto";
		return $db->consultar($sql);
	}		

	static function get_mapeo_tabla_dt($proyecto, $id_fuente)
	{
		$db = self::get_db();
		$id_fuente = $db->quote($id_fuente);
		$proyecto = $db->quote($proyecto);		
		$sql = "
			SELECT 
				dt.objeto,
				dt.tabla
			FROM 
				apex_objeto_db_registros as dt,
				apex_objeto as comp
			WHERE
					dt.objeto_proyecto = $proyecto
				AND dt.objeto = comp.objeto
				AND dt.objeto_proyecto = comp.proyecto
				AND comp.fuente_datos = $id_fuente
				ORDER BY dt.objeto, dt.tabla";
		$rs = $db->consultar($sql);
		return rs_convertir_asociativo($rs, array('tabla'), 'objeto');
	}

	static function get_mapeo_componentes_indice($proyecto)
	{
		$db = self::get_db();
		$proyecto = $db->quote($proyecto);		
		$sql = "
			SELECT
					identificador,
					objeto,
					clase
			FROM 
				apex_objeto
			WHERE
				proyecto = $proyecto
				AND (identificador IS NOT NULL OR trim(identificador) = '')
			ORDER BY identificador, objeto;";
		$rs = $db->consultar($sql);
		$temp = array();
		foreach($rs as $c) {
			$temp[$c['identificador']] = array('clase'=>$c['clase'],'componente'=>$c['objeto']);
		}
		return $temp;
	}

	static function get_descripcion_permiso($proyecto, $permiso)
	{
		$db = self::get_db();
		$proyecto = $db->quote($proyecto);
		$permiso = $db->quote($permiso);			
		$sql = "	SELECT
						per.descripcion,
						per.mensaje_particular
					FROM
						apex_permiso per
					WHERE
						per.proyecto = $proyecto
					AND	per.nombre = $permiso
		";
		return $db->consultar_fila($sql);
	}

	//---------------------  Puntos de control  -------------------------

    static function punto_control_parametros($proyecto, $pto_control)
    {
		$db = self::get_db();
		$proyecto = $db->quote($proyecto);
		$pto_control = $db->quote($pto_control);    	
		return $db->consultar(
	          "SELECT parametro
	             FROM apex_ptos_control_param 
	            WHERE proyecto    = $proyecto
	              AND pto_control = $pto_control
			ORDER BY parametro;
	        "); 
    }

    static function punto_control_controles($proyecto, $pto_control)
    {
		$db = self::get_db();
		$proyecto = $db->quote($proyecto);
		$pto_control = $db->quote($pto_control);    	
		return $db->consultar(
			"SELECT archivo,
					clase,
					actua_como
			   FROM apex_ptos_control_ctrl 
			  WHERE proyecto    = $proyecto
				AND pto_control = $pto_control
				ORDER BY archivo, clase;
		  ");
    }

	//---------------------  Grupos de Acceso  -------------------------

	/**
	 * Retorna las membresía del perfil
	 */
	static function get_perfiles_funcionales_asociados($proyecto, $perfil)
	{
		$db = self::get_db();		
		$proyecto_quote = $db->quote($proyecto);
		$perfil_quote = $db->quote($perfil);
		$sql = "SELECT 
					gam.usuario_grupo_acc_pertenece,
					(SELECT COUNT(*) FROM apex_usuario_grupo_acc_miembros mie WHERE mie.usuario_grupo_acc = gam.usuario_grupo_acc_pertenece) as cant_membresias
				FROM 
					apex_usuario_grupo_acc_miembros gam
				WHERE 
						gam.proyecto = $proyecto_quote
					AND	gam.usuario_grupo_acc = $perfil_quote
				ORDER BY gam.usuario_grupo_acc_pertenece
		";
		$salida = array();
		$datos =  $db->consultar($sql);		
		foreach ($datos as $fila) {
			$salida[] = $fila['usuario_grupo_acc_pertenece'];			
			if ($fila['cant_membresias'] > 0) {
				$salida = array_merge($salida, self::get_perfiles_funcionales_asociados($proyecto, $fila['usuario_grupo_acc_pertenece']));
			}
		}
		return array_unique($salida);
	}	
	
	static function get_items_menu($proyecto, $grupos_acceso)
	{
		$db = self::get_db();
		$raiz = $db->quote(self::get_item_raiz($proyecto));	
		if (empty($grupos_acceso)) {
			//Caso que el usuario no posea grupo de acceso (no_autenticado)
			$sql_grupo_acceso = "";
		} else {
			$grupos_acceso = implode(",", $db->quote($grupos_acceso));
			$sql_grupo_acceso = "auga.usuario_grupo_acc IN ($grupos_acceso) OR";
		}
		$proyecto = $db->quote($proyecto);
		$sql = "(
				SELECT 	amo.padre,
							amo.carpeta,
							amo.proyecto,
							amo.item,
							amo.descripcion as nombre,
							ai.orden as 		orden,
							ai.imagen,
							ai.imagen_recurso_origen,
							amo.padre = $raiz as es_primer_nivel
				   FROM 
							apex_usuario_grupo_acc auga
						JOIN apex_menu_operaciones amo ON (auga.menu_usuario = amo.menu_id AND auga.proyecto = amo.proyecto)
						JOIN apex_usuario_grupo_acc_item augai ON (auga.proyecto = augai.proyecto AND auga.usuario_grupo_acc = augai.usuario_grupo_acc AND augai.item = amo.item)
						JOIN apex_item ai ON (augai.item = ai.item AND augai.proyecto = ai.proyecto)	
				  WHERE (ai.menu = 1)
					AND ai.item != ai.padre
					AND	($sql_grupo_acceso ai.publico = 1)
					AND	(amo.proyecto = $proyecto)
					AND auga.menu_usuario IS NOT NULL
			UNION
				SELECT 	ai.padre,
						ai.carpeta,
						ai.proyecto,
						ai.item,
						ai.nombre as nombre,
						ai.orden as 		orden,
						ai.imagen,
						ai.imagen_recurso_origen,
						ai.padre = $raiz as es_primer_nivel
				FROM 
						apex_usuario_grupo_acc auga
					JOIN apex_usuario_grupo_acc_item augai ON (auga.proyecto = augai.proyecto AND auga.usuario_grupo_acc = augai.usuario_grupo_acc)
					JOIN apex_item ai ON (augai.item = ai.item AND augai.proyecto = ai.proyecto)	
				WHERE auga.menu_usuario is null
					AND (ai.menu = 1)
					AND ai.item != ai.padre
					AND	($sql_grupo_acceso ai.publico = 1)
					AND	(ai.proyecto = $proyecto)
			) ORDER BY 1, 6";
		
		toba::logger()->debug($sql);
		return $db->consultar($sql);
	}	

	static function get_item_raiz($proyecto)
	{
		$db = self::get_db();
		$proyecto = $db->quote($proyecto);		
		$sql = "
			SELECT 
				item
			FROM apex_item 
			WHERE 
					proyecto = $proyecto
				AND item = padre
		";
		$datos = $db->consultar_fila($sql);
		return $datos['item'];
	}	
	
	static function get_items_accesibles($proyecto, $grupos_acceso)
	{
		$db = self::get_db();
		$proyecto = $db->quote($proyecto);
		if (empty($grupos_acceso)) {
			//Caso que el usuario no posea grupo de acceso (no_autenticado)
			$sql_grupo_acceso = "";
		} else {
			$grupos_acceso = implode(",", $db->quote($grupos_acceso));
			$sql_grupo_acceso = "ui.usuario_grupo_acc IN ($grupos_acceso) OR";
		}
		$sql = "SELECT DISTINCT
						i.proyecto as proyecto,
						i.item as item
				FROM	apex_item i 
								LEFT OUTER JOIN apex_usuario_grupo_acc_item ui ON 
									(i.item = ui.item AND i.proyecto = ui.proyecto)
				WHERE	(i.carpeta <> 1 OR i.carpeta IS NULL)
				AND		i.proyecto = $proyecto
				AND		($sql_grupo_acceso i.publico = 1)
				ORDER BY i.item ";
		return $db->consultar($sql);
	}

	/**
	*	Devuelve la lista de items de la zona a los que puede acceder el grupo actual
	*/
	static function get_items_zona($proyecto, $grupos_acceso, $zona)
	{
		$db = self::get_db();
		$proyecto = $db->quote($proyecto);
		if (empty($grupos_acceso)) {
			//Caso que el usuario no posea grupo de acceso (no_autenticado)
			$sql_grupo_acceso = "";
		} else {
			$grupos_acceso = implode(",", $db->quote($grupos_acceso));
			$sql_grupo_acceso = "ui.usuario_grupo_acc IN ($grupos_acceso) OR";
		}
		$zona = $db->quote($zona);
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
				WHERE	i.zona = $zona
				AND		i.zona_proyecto = $proyecto
				AND 	ui.item = i.item
				AND		ui.proyecto = i.proyecto
				AND		($sql_grupo_acceso i.publico = 1)
				AND		i.zona_listar = 1
				ORDER BY 3;";
		return $db->consultar($sql);	
	}

	static function get_lista_permisos($proyecto, $grupos_acceso)
	{
		$db = self::get_db();
		$proyecto = $db->quote($proyecto);		
		$grupos_acceso = implode(",", $db->quote($grupos_acceso));		
		$sql = " 
			SELECT DISTINCT
				per.nombre as nombre
			FROM
				apex_permiso_grupo_acc per_grupo,
				apex_permiso per
			WHERE
				per_grupo.proyecto = $proyecto
			AND	per_grupo.usuario_grupo_acc IN ($grupos_acceso)
			AND	per_grupo.permiso = per.permiso
			AND	per_grupo.proyecto = per.proyecto
			ORDER BY per.nombre";
		return self::get_db()->consultar($sql);
	}

	//------------------------ CONSULTAS PHP -----------------------

	static function get_consulta_php($proyecto, $clase)
	{
		$db = self::get_db();
		$proyecto = $db->quote($proyecto);
		$clase = $db->quote($clase);
		$sql = "SELECT		clase,
							archivo,
							archivo_clase,
							punto_montaje
					FROM	apex_consulta_php
					WHERE	proyecto = $proyecto AND clase = $clase; ";
		return self::get_db()->consultar_fila($sql);	
	}

	//------------------------  DIMENSIONES -----------------------

	static function get_info_dimension($proyecto, $dimension)
	{
		$db = self::get_db();
		$proyecto = $db->quote($proyecto);
		$dimension = $db->quote($dimension);	
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
					WHERE proyecto = $proyecto
					AND	dimension = $dimension";
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
						WHERE proyecto = $proyecto
						AND	dimension = $dimension
						ORDER BY tipo, orden";
			$datos_dim['gatillos'] = $db->consultar($sql);
		}
		return $datos_dim;
	}

	static function get_info_relacion_entre_tablas($proyecto, $fuente_datos)
	{
		$db = self::get_db();
		$proyecto = $db->quote($proyecto);
		$fuente_datos = $db->quote($fuente_datos);	
		$sql = " 	SELECT	proyecto,
							fuente_datos_proyecto,
							fuente_datos,					
							relacion_tablas,					
							tabla_1,							
							tabla_1_cols,					
							tabla_2,							
							tabla_2_cols					
					FROM	apex_relacion_tablas
					WHERE proyecto = $proyecto
					AND fuente_datos = $fuente_datos";
		$datos = self::get_db()->consultar($sql);
		$temp = array();
		//Armo una estructura indizada de las relaciones.
		foreach($datos as $relacion) {
			$cols_tabla_1 = explode(',',$relacion['tabla_1_cols']);
			$cols_tabla_1 = array_map('trim', $cols_tabla_1);
			$cols_tabla_2 = explode(',',$relacion['tabla_2_cols']);
			$cols_tabla_2 = array_map('trim', $cols_tabla_2);
			$temp[$relacion['tabla_1']][$relacion['tabla_2']]['cols_1'] = $cols_tabla_1;
			$temp[$relacion['tabla_1']][$relacion['tabla_2']]['cols_2'] = $cols_tabla_2;
		}
		return $temp;
	}

	//------------------------  SERVICIOS WEB -----------------------

	static function get_info_servicio_web($proyecto, $servicio)
	{
		$db = self::get_db();
		$proyecto = $db->quote($proyecto);
		$servicio = $db->quote($servicio);

		$sql = " 	SELECT	
						proyecto,
						servicio_web,
						descripcion,
						tipo,
						param_to,
						param_wsa					
					FROM apex_servicio_web
					WHERE proyecto = $proyecto
					AND	servicio_web = $servicio;";
		$datos = $db->consultar_fila($sql);
		if (empty($datos)) {
			return $datos;
		}
		$datos['parametros'] = array('to' => $datos['param_to']);
		if (isset($datos['param_wsa'])) {
			$datos['parametros']['useWSA'] = $datos['param_wsa'];
		}
				
		//Parametros
		$sql = " 	SELECT	parametro,					
							valor					
					FROM apex_servicio_web_param
					WHERE proyecto = $proyecto
					AND	servicio_web = $servicio
					ORDER BY parametro;";
		foreach ($db->consultar($sql) as $fila) {
			$datos['parametros'][$fila['parametro']] = $fila['valor'];
		}
		return $datos;
	}
	
	
	//------------------------  MENSAJES  -------------------------
	
	static function get_mensaje_toba($indice)
	{
		$db = self::get_db();
		$indice = $db->quote($indice);
		$sql = "SELECT
					COALESCE(mensaje_customizable, mensaje_a) as m
				FROM apex_msg 
				WHERE indice = $indice
				AND proyecto = 'toba';";
		return $db->consultar_fila($sql, toba_db_fetch_asoc, false);	
	}
	
	static function get_mensaje_proyecto($proyecto, $indice)
	{
		$db = self::get_db();
		$indice = $db->quote($indice);
		$proyecto = $db->quote($proyecto);	
		$sql = "SELECT
					COALESCE(mensaje_customizable, mensaje_a) as m
				FROM apex_msg 
				WHERE indice = $indice
				AND proyecto = $proyecto;";
		return $db->consultar_fila($sql, toba_db_fetch_asoc, false);	
	}

	static function get_mensaje_objeto($proyecto, $objeto, $indice)
	{
		$db = self::get_db();
		$indice = $db->quote($indice);
		$proyecto = $db->quote($proyecto);
		$objeto = $db->quote($objeto);		
		$sql = "SELECT
					COALESCE(mensaje_customizable, mensaje_a) as m
				FROM apex_objeto_msg 
				WHERE indice = $indice
				AND objeto_proyecto = $proyecto
				AND objeto = $objeto;";
		return self::get_db()->consultar_fila($sql);	
	}

	//------------------------  PUNTOS DE MONTAJE  -------------------------
	/**
	 * Retorna el listado completo de los puntos de montaje de un proyecto
	 */
	static function get_pms($proyecto)
	{
		$proyecto = self::get_db()->quote($proyecto);
		$sql = "SELECT id,
					  etiqueta, 
					  proyecto,
					  proyecto_ref,
					  descripcion,
					  path_pm,
					  tipo
			FROM apex_puntos_montaje 
			WHERE proyecto = $proyecto 
			ORDER BY etiqueta, id;";
		return self::get_db()->consultar($sql);
	}

	
	//------------------------  GADGETS  -------------------------	
	static function get_gadgets_proyecto($proyecto, $usuario) {
		$db = self::get_db();
		$proyecto = $db->quote($proyecto);
		$usuario = $db->quote($usuario);
		$sql = "SELECT		g.gadget,
											g.gadget_url,
											g.titulo,
											g.descripcion,
											u.orden,
											g.proyecto,
											coalesce(u.eliminable, 'S'::varchar) as eliminable,
											g.tipo_gadget,
											g.subclase,
											g.subclase_archivo
					FROM
						apex_usuario_proyecto_gadgets u
					INNER JOIN
						apex_gadgets g ON g.gadget = u.gadget
					WHERE
						u.usuario = $usuario
					AND u.proyecto = $proyecto
					ORDER BY orden;  ";

		return $db->consultar($sql);
	}
}
?>