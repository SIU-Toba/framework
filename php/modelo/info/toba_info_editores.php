<?php

class toba_info_editores
{
	//------------------------------------------------------------------------------
	//----------  TIPOS de COMPONENTES  --------------------------------------------
	//------------------------------------------------------------------------------

	/**
	*	listado de tipos de componentes basico utilizado en el administrador
	*/
	static function get_info_tipos_componente($contenedor=null, $excluir_internos=true, $id_contenedor=null)
	{
		$where = '';
		if(isset($contenedor)){
			$where = " AND c.clase IN ('". implode("','", self::get_clases_validas_contenedor($contenedor, $id_contenedor) ) ."') ";
			if ($contenedor == 'toba_item' && isset($id_contenedor) ) {
				$proyecto = toba_contexto_info::get_proyecto();
				$where = " AND solicitud_tipo = (SELECT solicitud_tipo FROM apex_item 
					WHERE item = '$id_contenedor' AND proyecto = '$proyecto')";
			}
		}
		if($excluir_internos) {
			$where .= " AND ct.clase_tipo <> 10 ";
		}
		$sql = "SELECT
					c.proyecto,
					c.clase,
					c.editor_proyecto,
					c.editor_item,
					c.icono,
					c.descripcion_corta,
					ct.clase_tipo,
					ct.descripcion_corta as clase_tipo_desc
				FROM 
					apex_clase c,
					apex_clase_tipo ct
				WHERE
					c.clase_tipo = ct.clase_tipo 
					$where
					AND c.proyecto = 'toba'
				ORDER BY ct.orden DESC, c.descripcion_corta";
		return toba_contexto_info::get_db()->consultar($sql);
	}

	static function get_lista_tipo_componentes($excluir_internos=true)
	{
		$datos = array();
		foreach ( self::get_info_tipos_componente(null,$excluir_internos) as $c ) {
			$datos[] = $c['clase'];
		}
		return $datos;
	}

	static function get_clases_validas_contenedor($contenedor, $id=null)
	{
		if( $contenedor=='toba_item' ) {
			return array('toba_ci', 'toba_cn', 'toba_servicio_web');
		} elseif ($contenedor == 'toba_ci_pantalla') {
			//Pantalla: de las cosas que pueden ir en un CI, filtro los elementos de interface y controladores
			return self::get_lista_componentes_validos_en_contenedor('toba_ci', array(7,8));
		} else {
			return self::get_lista_componentes_validos_en_contenedor($contenedor);
		}
	}	

	/**
	*	Busca en la base los conte
	*/
	static function get_lista_componentes_validos_en_contenedor($contenedor, $categorias = null)
	{
		$where = '';
		if(isset($categorias)) {
			$where = "AND	c.clase_tipo IN (".implode(',',$categorias).")";
		}
		$contenedor = toba_contexto_info::get_db()->quote($contenedor);
		$sql = "SELECT 	c.clase as clase
				FROM 	apex_clase c, 
					apex_clase_relacion r
				WHERE 	r.clase_contenida = c.clase
				$where
				AND	r.clase_contenedora = $contenedor";
		$datos = array();
		foreach ( toba_contexto_info::get_db()->consultar($sql) as $c ) {
			$datos[] = $c['clase'];
		}
		return $datos;
	}

	static function get_contenedores_validos($componente)
	{
		switch($componente) {
			case 'toba_ci':
				return array_merge(array('toba_item'), self::get_lista_componentes_contenedores($componente));
			case 'toba_cn':
				return array('toba_item');
			default:
				return self::get_lista_componentes_contenedores($componente);
		}
	}

	static function get_lista_componentes_contenedores($tipo_componente_contenido)
	{
		$tipo_componente = toba_contexto_info::get_db()->quote($tipo_componente_contenido);
		$sql = "SELECT 	c.clase as clase
				FROM 	apex_clase c, 
						apex_clase_relacion r
				WHERE 	r.clase_contenedora = c.clase
				AND	r.clase_contenida = $tipo_componente";
		$datos = array();
		foreach ( toba_contexto_info::get_db()->consultar($sql) as $c ) {
			$datos[] = $c['clase'];
		}
		return $datos;
	}
	
	static function get_lista_clases_item()
	{
		return self::get_lista_clases_toba(false, self::get_clases_validas_contenedor('toba_item'));
	}
	
	static function get_lista_clases_validas_en_ci()
	{
		return self::get_lista_clases_toba(false, self::get_clases_validas_contenedor('toba_ci'));
	}	
	
	static function get_lista_clases_validas_en_cn()
	{
		return self::get_lista_clases_toba(false, self::get_clases_validas_contenedor('toba_cn'));
	}	

	/*
		Lista de tipos de componponentes en COMBOS
		Las clases usan un ID concatenado para que las cascadas
		las soporten (actualmente pasan un parametro solo)
	*/
	static function get_lista_clases_toba($todas=false, $especificas=null)
	{
		if ($todas) {
			$sql_todas = "";
		} else {
			$clases = (isset($especificas)) ? $especificas : self::get_lista_tipo_componentes();
			$sql_todas = "clase IN ('". implode("','", $clases) ."') AND";
		}
			
		$sql = "SELECT 	proyecto || ',' || clase as clase, 
						descripcion_corta as descripcion
				FROM apex_clase 
				WHERE 
					$sql_todas
					(proyecto = '". toba_contexto_info::get_proyecto() ."' OR proyecto='toba')
				ORDER BY descripcion_corta";
		return toba_contexto_info::get_db()->consultar($sql);
	}	

	/**
	*	Retorna el ID de componente del CI editor de un tipo de componente
	*/
	static function get_ci_editor_clase($proyecto, $clase)
	{
		$clase = toba_contexto_info::get_db()->quote($clase);
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$sql = "SELECT
				 	o.proyecto,
					o.objeto
				FROM
					apex_clase c,
					apex_item_objeto io,
					apex_objeto o
				WHERE
					c.clase = $clase AND
					c.proyecto = $proyecto  AND
					c.editor_item = io.item AND				-- Se busca el item editor
					c.editor_proyecto = io.proyecto AND
					io.objeto = o.objeto AND				-- Se busca el CI del item
					io.proyecto = o.proyecto AND
					o.clase = 'toba_ci'";
		$res = toba_contexto_info::get_db()->consultar($sql);
		return $res[0];
	}
	
	/**
	*	Retorna el id del objeto datos_relacion asociado a la clase
	*/
	static function get_dr_de_clase($clase)
	{
		if ($clase == 'toba_item' ) {
			return array( toba_editor::get_id(), '1554' );
		}
		$clase = toba_contexto_info::get_db()->quote($clase);
		$sql = "SELECT objeto_dr_proyecto, objeto_dr
				FROM apex_clase
				WHERE clase = $clase;";
		$datos = toba_contexto_info::get_db()->consultar($sql);
		if( ! $datos ) {
			throw new toba_error("No hay definido un datos_relacion para la clase $clase");
		} else {
			return array( $datos[0]['objeto_dr_proyecto'], $datos[0]['objeto_dr'] );
		}
	}
		
	static function get_clases_con_fuente_datos()
	{
		$sql = 'SELECT clase
				FROM apex_clase
				WHERE utiliza_fuente_datos = 1;';
		$datos = array();
		foreach ( toba_contexto_info::get_db()->consultar($sql) as $c ) {
			$datos[] = $c['clase'];
		}
		return $datos;
	}

	/**
	 * Categorias que clasifican los tipos de componente (interface, persistencia, controladores, negocio)
	 */
	static function get_lista_tipos_clase()
	{
		$sql = 'SELECT
					clase_tipo, 
					descripcion_corta
				FROM apex_clase_tipo
				ORDER BY descripcion_corta
				';
		return toba_contexto_info::get_db()->consultar($sql);	
	}
	
	//----------------------------------------------------------------------------
	//---------------- PROYECTOS--------------------------------------------------
	//----------------------------------------------------------------------------
	
	/**
	 * Retorna la lista de proyectos que el usuario actual puede modificar
	 * @todo Utilizar la clase instancia
	 */
	static function get_proyectos_accesibles()
	{
		$usuario = toba_contexto_info::get_db()->quote(toba::usuario()->get_id());
		$sql = "
			SELECT 	
				p.proyecto, 
				p.descripcion_corta
			FROM
			 	apex_proyecto p,
				apex_usuario_proyecto up
			WHERE 	
				p.proyecto = up.proyecto
			AND	up.usuario = $usuario
			ORDER BY orden;";
		return toba_contexto_info::get_db()->consultar($sql);
	}

	static function get_descripcion_proyecto($id_proyecto)
	{
		$proyecto = toba_contexto_info::get_db()->quote($id_proyecto);
		$sql = "SELECT descripcion || ' [' || proyecto || ']' as proyecto_nombre FROM apex_proyecto WHERE proyecto = $proyecto";
		return  toba_contexto_info::get_db()->consultar($sql);
	}
	
	static function get_clases_extendidas_proyecto($id_proyecto)
	{
		$proyecto = toba_contexto_info::get_db()->quote($id_proyecto);
		$sql =" SELECT	extension_toba, 
						extension_proyecto						
			    FROM	apex_proyecto p
			    WHERE	proyecto = $proyecto";
		return toba_contexto_info::get_db()->consultar_fila($sql);
	}
	
	/**
	*	Retorna la lista de proyectos que contiene items exportables
	*/
	static function get_proyectos_con_items_exportables()
	{
		$usuario = toba_contexto_info::get_db()->quote(toba::usuario()->get_id());
		$sql = "
			SELECT 	
				p.proyecto, 
				p.descripcion_corta
			FROM
			 	apex_proyecto p,
				apex_usuario_proyecto up
			WHERE 	
				p.proyecto = up.proyecto
			AND	up.usuario = $usuario
			AND (SELECT COUNT(*) FROM apex_item 
					WHERE proyecto = p.proyecto AND exportable=1) > 0
			ORDER BY orden;";
		return toba_contexto_info::get_db()->consultar($sql);
	}		
	
	
	/**
	*	Retorna la lista de items exportables de un proyecto dado
	*/
	static function get_items_exportables($proyecto=null)
	{
		$proyecto = isset($proyecto) ? 
					toba_contexto_info::get_db()->quote($proyecto) : 
					toba_contexto_info::get_db()->quote(toba_contexto_info::get_proyecto());
		$sql = "
			SELECT 
				proyecto, 
				item,
				nombre
			FROM apex_item 
			WHERE 
					(carpeta <> '1' OR carpeta IS NULL) 
				AND proyecto = $proyecto
				AND exportable = 1
			ORDER BY nombre;
		";
		return toba_contexto_info::get_db()->consultar($sql);	
	}	

	/**
	*	Retorna la lista de items que corresponden con servicios_web
	*/
	static function get_items_servicios_web($proyecto=null)
	{
		$proyecto = isset($proyecto) ? 
					toba_contexto_info::get_db()->quote($proyecto) : 
					toba_contexto_info::get_db()->quote(toba_contexto_info::get_proyecto());
		$sql = "
			SELECT 
				item,
				nombre
			FROM apex_item 
			WHERE 
					proyecto = $proyecto
				AND solicitud_tipo = 'servicio_web'
			ORDER BY item;
		";
		return toba_contexto_info::get_db()->consultar($sql);	
	}		
	
	//-------------------------------------------------
	//---------------- ITEMS --------------------------
	//-------------------------------------------------

	static function get_item_raiz($proyecto=null)
	{
		$proyecto = isset($proyecto) ? 
						toba_contexto_info::get_db()->quote($proyecto) : 
						toba_contexto_info::get_db()->quote(toba_contexto_info::get_proyecto());
		$sql = "
			SELECT 
				item
			FROM apex_item 
			WHERE 
					proyecto = $proyecto
				AND item = padre
		";
		$datos = toba_contexto_info::get_db()->consultar_fila($sql);
		return $datos['item'];
	}
	
	/**
	*	Retorna la lista de todos los items del proyecto actual (no carpetas)
	*/
	static function get_lista_items($proyecto=null, $sin_carpetas=true)
	{
		$proyecto = isset($proyecto) ? 
						toba_contexto_info::get_db()->quote($proyecto) : 
						toba_contexto_info::get_db()->quote(toba_contexto_info::get_proyecto());
		
		if ($sin_carpetas) {
			$where = "(carpeta <> '1' OR carpeta IS NULL)";
		} else {
			$where = 'TRUE';
		}
		
		$sql = "
			SELECT 
				proyecto, 
				item 						as id, 
				nombre						as descripcion,
				carpeta
			FROM apex_item 
			WHERE
					proyecto = $proyecto
				AND $where
			ORDER BY nombre, item;
		";
		toba::logger()->debug($sql);
		return toba_contexto_info::get_db()->consultar($sql);
	}

	/**
	*	Retorna la existencia de un item particular en un proyecto determinado
	*/
	static function existe_item($item, $proyecto = null)
	{
		$item = toba_contexto_info::get_db()->quote($item);
		$proyecto = (! is_null($proyecto)) ?
						toba_contexto_info::get_db()->quote($proyecto) :
						toba_contexto_info::get_db()->quote(toba_contexto_info::get_proyecto());
		$sql = "
			SELECT
				nombre						as descripcion
			FROM apex_item
			WHERE
				(carpeta <> '1' OR carpeta IS NULL) AND
				proyecto = $proyecto AND
				item = $item
			ORDER BY nombre;
		";
		return toba_contexto_info::get_db()->hay_datos($sql);
	}

	/**
	*	Retorna la lista de items en un formato adecuado para un combo
	*/
	static function get_items_para_combo($proyecto = null, $incluir_carpetas = false)
	{
		if (! isset($proyecto)) {
			$proyecto = toba_contexto_info::get_proyecto();
		}
		$catalogador = new toba_catalogo_items($proyecto);
		$catalogador->cargar_todo();	
		$esp = html_entity_decode('&nbsp;', ENT_COMPAT | ENT_HTML401, "ISO-8859-1");
		
		foreach($catalogador->items() as $item) {
			if (! $item->es_carpeta() || $incluir_carpetas) {
				$nivel = $item->get_nivel_prof() - 1;
				if($nivel >= 1){
					$inden = $esp . str_repeat('|' . str_repeat($esp, 8), $nivel -1) . "|__$esp";
				}else{
					$inden = "";
				}
				$datos[] =  array('proyecto' => toba_contexto_info::get_proyecto(),
									'id' => $item->get_id(), 
									'padre' => $item->get_id_padre(),
									'descripcion' => $inden . $item->get_nombre());
			}
		}
		return $datos;		
	}
		
	/**
	*	Retorna la lista de carpetas en un formato adecuado para un combo
	*/
	static function get_carpetas_posibles($proyecto=null)
	{
		if (! isset($proyecto)) {
			$proyecto = toba_contexto_info::get_proyecto();
		}
		$catalogador = new toba_catalogo_items($proyecto);
		$catalogador->cargar_todo(array('solo_carpetas' => 1));
		if (! defined('ENT_HTML401')) {
			define('ENT_HTML401', 0);
		}		
		$esp = html_entity_decode('&nbsp;', ENT_COMPAT | ENT_HTML401, "ISO-8859-1");
		foreach($catalogador->items() as $carpeta) {
			$nivel = $carpeta->get_nivel_prof() - 1;
			if($nivel >= 0) {
				$inden = $esp . str_repeat("|" . str_repeat($esp,8), $nivel) . "|__$esp";
			} else {
				$inden = "";
			}
			$datos[] =  array('proyecto' => $proyecto,
								'id' => $carpeta->get_id(), 
								'padre' => $carpeta->get_id(),		//Necesario para el macheo por agrupacion
								'nombre' => $inden . $carpeta->get_nombre());
		}
		return $datos;
	}

	/**
	*	Retorna los items de una carpeta
	*/
	static function get_items_carpeta($carpeta, $proyecto=null)
	{
		if (! isset($proyecto)) {
			$proyecto = toba_contexto_info::get_proyecto();
		}
		$carpeta = toba_contexto_info::get_db()->quote($carpeta);
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$sql = "
			SELECT 
				item 									as id, 
				nombre || ' - (' || item || ')'			as descripcion
			FROM apex_item 
			WHERE 
				(carpeta <> '1' OR carpeta IS NULL) AND
				( (padre = $carpeta) AND (padre_proyecto= $proyecto) )
					AND	proyecto = $proyecto
			ORDER BY nombre;
		";
		return toba_contexto_info::get_db()->consultar($sql);
	}
	
	static function get_carpeta_de_item($item, $proyecto)
	{
		$sql = "
			SELECT 
				padre
			FROM apex_item 
			WHERE 
					item = '$item'
				AND	proyecto = '$proyecto'
		";
		$rs = toba_contexto_info::get_db()->consultar($sql);
		if (!empty($rs)) {
			return $rs[0]['padre'];	
		}
	}

	//---------------------------------------------------
	//--------------- COMPONENTES -----------------------
	//---------------------------------------------------

	/**
	*	Retorna el nombre de la clase del objeto
	*/
	static function get_clase_de_objeto($id)
	{
		$obj = toba_contexto_info::get_db()->quote($id[1]);
		$proy = toba_contexto_info::get_db()->quote($id[0]);
		$sql = "
			SELECT 
				o.clase
			FROM 
				apex_objeto o
			WHERE 
				(o.objeto = $obj) AND
				(o.proyecto = $proy)
		";
		$datos = toba_contexto_info::get_db()->consultar($sql);
		return $datos[0]['clase'];
	}

	static function get_lista_objetos_toba($clase)
	{
		$clase = explode(",",$clase);
		$clase = toba_contexto_info::get_db()->quote($clase[1]);
		$proyecto = toba_contexto_info::get_db()->quote(toba_contexto_info::get_proyecto());
		$sql = "SELECT 	proyecto, 
						objeto, 
						objeto							   as id,
						'[' || objeto || '] -- ' || nombre as descripcion
				FROM apex_objeto 
				WHERE 	clase = $clase
				AND 	proyecto = $proyecto
				ORDER BY nombre";
		return toba_contexto_info::get_db()->consultar($sql);
	}
	
	static function get_info_dependencia($objeto_proyecto, $objeto)
	//Carga externa para un db_registros de dependencias
	{
		$proyecto = toba_contexto_info::get_db()->quote($objeto_proyecto);
		$objeto = toba_contexto_info::get_db()->quote($objeto);
		$sql = "SELECT 	o.clase || ' - ' || '[' || o.objeto || '] - ' || o.nombre as nombre_objeto,
						'[' || o.objeto || '] - ' || o.nombre as descripcion,
						o.clase_proyecto || ',' || o.clase as clase
				FROM 	apex_clase c, apex_objeto o
				WHERE 	o.clase = c.clase
				AND 	o.clase_proyecto = c.proyecto
				AND 	o.proyecto = $proyecto
				AND 	o.objeto = $objeto";
		return toba_contexto_info::get_db()->consultar($sql);
	}

	//***************************************************
	//**  Devuelve el ARBOL de componentes de un ITEM
	//***************************************************

	/**
	*	Devuelve la lista de dependencias de un ITEM
	*/
	static function get_arbol_componentes_item($proyecto, $item)
	{
		$resultado[0] = array( 'tipo' => 'toba_item', 'componente'=> $item, 'proyecto' => $proyecto, 
								'icono' => 'item.gif', 'consumidores_externos' => 0, 'nombre' => '');
		$item = toba_contexto_info::get_db()->quote($item);
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$sql = "SELECT 	proyecto,
						objeto
				FROM 	apex_item_objeto 
				WHERE 	item = $item
				AND 	proyecto = $proyecto
				ORDER BY objeto";
		$datos = toba_contexto_info::get_db()->consultar($sql);
		foreach($datos as $componente) {
			$resultado = array_merge($resultado, self::get_arbol_componentes_componente($componente['proyecto'], $componente['objeto']));
		}
		return $resultado;
	}

	/**
	 * Dado un componente retorna la subclase asociada (si no existe retorna null)
	 * @return array subclase,subclase_archivo
	 */
	static function get_subclase_componente($id, $proyecto=null)
	{
		if (! isset($proyecto)) {
			$proyecto = toba_contexto_info::get_proyecto();
		}
		$id = toba_contexto_info::get_db()->quote($id);
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$sql = "SELECT
					subclase,
					subclase_archivo
				FROM
					apex_objeto
				WHERE 	
						objeto = $id
					AND proyecto = $proyecto";
		$datos = toba_contexto_info::get_db()->consultar_fila($sql);
		return $datos;				
	}
	
	/*
	*	Devuelve la lista de dependencias de un ITEM
	*/
	static function get_arbol_componentes_componente($proyecto, $componente, $componente_padre=null)
	{
		static $id = 1;
		$excluir_padre = isset($componente_padre) ? 'AND objeto_consumidor <> '.toba_contexto_info::get_db()->quote($componente_padre) : "";
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$componente_sano = toba_contexto_info::get_db()->quote($componente);
		$sql = "SELECT 	o.proyecto as 			proyecto,
						o.objeto as 			objeto,
						o.nombre as 			nombre,
						o.clase as 				clase,
						o.subclase as			subclase,
						o.subclase_archivo as	subclase_archivo,
						o.punto_montaje as		punto_montaje,
						c.icono as				icono,
						(SELECT COUNT(*) 
							FROM apex_objeto_dependencias dd
							WHERE dd.objeto_proveedor = o.objeto
							AND dd.proyecto = $proyecto $excluir_padre ) as consumidores_externos,
						d.objeto_proveedor as 	dep
				FROM 	apex_objeto o LEFT OUTER JOIN apex_objeto_dependencias d
						ON o.objeto = d.objeto_consumidor AND o.proyecto = d.proyecto,
						apex_clase c
				WHERE 	
						o.objeto = $componente_sano
					AND o.proyecto = $proyecto
					AND o.clase = c.clase
		";
		$datos = toba_contexto_info::get_db()->consultar($sql);
		$resultado[$id] = array( 	'tipo' => $datos[0]['clase'], 
									'componente'=> $datos[0]['objeto'],
									'proyecto' => $datos[0]['proyecto'],
									'subclase' => $datos[0]['subclase'],
									'subclase_archivo' => $datos[0]['subclase_archivo'],
									'punto_montaje' => $datos[0]['punto_montaje'],
									'icono' => $datos[0]['icono'],
									'nombre' => $datos[0]['nombre'],
									'consumidores_externos' => $datos[0]['consumidores_externos']);
		//ei_arbol($datos, $componente_padre. ' '.$resultado[$id]['componente']);									
		foreach($datos as $componente_hijo) {
			if(isset($componente_hijo['dep'])) {
				$id++;
				$nietos = self::get_arbol_componentes_componente($componente_hijo['proyecto'], $componente_hijo['dep'], $componente);
				foreach ($nietos as $id_nieto => $nieto) {
					$nietos[$id_nieto]['consumidores_externos'] +=  $datos[0]['consumidores_externos'];
				}
				$resultado = array_merge($resultado, $nietos);
			}
		}
		return $resultado;
	}

	//***************************************************
	//**  CI  *******************************************
	//***************************************************

	static function get_pantallas_de_ci($objeto)
	{
		if (is_numeric($objeto)) {
			$proyecto = toba_contexto_info::get_db()->quote(toba_contexto_info::get_proyecto());
			$objeto = toba_contexto_info::get_db()->quote($objeto);			
			$sql = "SELECT
						pantalla,
						identificador || ' - ' || COALESCE(etiqueta, '') as descripcion
					FROM
						apex_objeto_ci_pantalla
					WHERE
						objeto_ci_proyecto = $proyecto AND
						objeto_ci = $objeto";
			return toba_contexto_info::get_db()->consultar($sql);
		} else {
			return array();	
		}
	}

	//***************************************************
	//** DATOS RELACION *********************************
	//***************************************************
	
	static function get_lista_objetos_dr()
	//Listar objetos que son datos_relacion
	{
		$proyecto = toba_contexto_info::get_db()->quote(toba_contexto_info::get_proyecto()) ;
		$sql = "SELECT 	proyecto, 
						objeto, 
						nombre as descripcion_corta,
						'[' || objeto || '] -- ' || nombre as descripcion
				FROM apex_objeto 
				WHERE 	clase = 'toba_datos_relacion'
				AND		clase_proyecto = 'toba'
				AND 	proyecto = $proyecto
				ORDER BY descripcion_corta";
		return toba_contexto_info::get_db()->consultar($sql);
	}

	//***************************************************
	//** DATOS TABLAS ***********************************
	//***************************************************

	static function get_lista_objetos_dt()
	//Listar objetos que son datos_tabla
	{
		$proyecto = toba_contexto_info::get_db()->quote(toba_contexto_info::get_proyecto());
		$sql = "SELECT 	o.proyecto as 								proyecto, 
						o.objeto as 								objeto, 
						'[' || o.objeto || '] -- ' || o.nombre as 	descripcion,
						d.tabla as									tabla
				FROM 	apex_objeto o,
						apex_objeto_db_registros d
				WHERE 	o.clase = 'toba_datos_tabla'
				AND		o.objeto = d.objeto
				AND		o.proyecto = d.objeto_proyecto
				AND		o.clase_proyecto = 'toba'
				AND 	o.proyecto = $proyecto
				ORDER BY tabla";
		return toba_contexto_info::get_db()->consultar($sql);
	}

	//---------------------------------------------------	
	
	static function get_tabla_fuente_de_dt($id=null, $proyecto=null)
	{
		if (!isset($proyecto)) {
			$proyecto = toba_contexto_info::get_proyecto();
		}
		$objeto = '';
		if (isset($id)) {
			$id_sano = toba_contexto_info::get_db()->quote($id);
			$objeto = "					dt.objeto = $id_sano AND";
		}
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$sql = "
			SELECT 
				dt.tabla,
				comp.fuente_datos,
				comp.objeto as id
			FROM 
				apex_objeto_db_registros as dt,
				apex_objeto as comp
			WHERE
				$objeto
					dt.objeto_proyecto = $proyecto
				AND dt.objeto = comp.objeto
				AND dt.objeto_proyecto = comp.proyecto
			ORDER BY tabla				
		";
		if (isset($id)) {
			return toba_contexto_info::get_db()->consultar_fila($sql);
		} else {
			return toba_contexto_info::get_db()->consultar($sql);
		}
	}
	//---------------------------------------------------
	
	static function get_dt_de_tabla_fuente($tabla, $fuente, $proyecto=null)
	{
		if (!isset($proyecto)) {
			$proyecto = toba_contexto_info::get_proyecto();
		}
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$fuente = toba_contexto_info::get_db()->quote($fuente);
		$tabla = toba_contexto_info::get_db()->quote($tabla);
		$sql = "
			SELECT 
				dt.objeto as id
			FROM 
				apex_objeto_db_registros as dt,
				apex_objeto as comp
			WHERE
					dt.tabla = $tabla
				AND	dt.objeto_proyecto = $proyecto
				AND dt.objeto = comp.objeto
				AND dt.objeto_proyecto = comp.proyecto
				AND comp.fuente_datos = $fuente";
		return toba_contexto_info::get_db()->consultar_fila($sql);		
	}
	
	
	//---------------------------------------------------	

	static function get_lista_dt_columnas($objeto)
	/*
		Lista las columnas de los DATOS_TABLA (supone que el objeto pasado como parametro lo es)
		Esta pregunta hay que hacercela a una clase de dominio (un datos_tabla)
	*/
	{
		$objeto = toba_contexto_info::get_db()->quote($objeto);
		$proyecto = toba_contexto_info::get_db()->quote(toba_contexto_info::get_proyecto());
		$sql = "SELECT 		columna as 	clave,
											columna as 	descripcion,
											col_id  as	col_id,
											objeto_proyecto as proyecto,
											objeto
				FROM apex_objeto_db_registros_col 
				WHERE 	objeto = $objeto
				AND 	objeto_proyecto = $proyecto
				ORDER BY 3";
		return toba_contexto_info::get_db()->consultar($sql);
	}

	//---------------------------------------------------
	//------------ PUNTOS DE CONTROL --------------------
	//---------------------------------------------------

	static function get_puntos_de_control($filtro, $id_contenedor = null, $id_objeto = null, $campos = null)
	{
		$proyecto = toba_contexto_info::get_db()->quote(toba_contexto_info::get_proyecto());
		$sql = "SELECT pto_control,
						 descripcion
					FROM apex_ptos_control
				   WHERE proyecto = $proyecto";

		// Elimina los puntos de control que ya se utilizaron en
		// pantallas anteriores del mismo item.
		if ($filtro == 'P' || $filtro == 'C')
		{
		  if (isset($id_objeto)) {
			$id_objeto = toba_contexto_info::get_db()->quote($id_objeto);
			$sql .= "    AND pto_control NOT IN (

							SELECT pce.pto_control
							 FROM apex_ptos_control_x_evento pce,
								  apex_objeto_dependencias ode,
								  apex_objeto_dependencias ode2,
								  apex_objeto_eventos oe

							 WHERE ode.proyecto          =  $proyecto
							   AND ode.objeto_proveedor  =  $id_objeto

							   AND ode.proyecto          =  ode2.proyecto
							   AND ode.objeto_consumidor =  ode2.objeto_consumidor

							   AND ode2.proyecto         =  oe.proyecto
							   AND ode2.objeto_proveedor =  oe.objeto
							   AND ode2.objeto_proveedor <> $id_objeto

							   AND oe.proyecto           =  pce.proyecto
							   AND oe.evento_id          =  pce.evento_id
						 )
				";
			}
			if (isset($id_contenedor)) {
				$id_contenedor = toba_contexto_info::get_db()->quote($id_contenedor);
				$sql .= "    AND pto_control NOT IN (

							SELECT pce.pto_control
							 FROM apex_ptos_control_x_evento pce,
								  apex_objeto_dependencias ode,
								  apex_objeto_eventos oe

							 WHERE ode.proyecto          =  $proyecto
							   AND ode.objeto_consumidor =  $id_contenedor

							   AND ode.proyecto          =  oe.proyecto
							   AND ode.objeto_proveedor  =  oe.objeto

							   AND oe.proyecto           =  pce.proyecto
							   AND oe.evento_id          =  pce.evento_id
						 )
				";
			}
		}

		// Solo muestra los puntos de control cuyos parametros tengan el mismo
		// nombre que los definidos en el cuadro en edición
		if ($filtro == 'C')
		{
		   $sql .= "  AND pto_control IN (

						 SELECT DISTINCT pcp.pto_control
						   FROM apex_ptos_control_param pcp
						  WHERE pcp.proyecto = $proyecto
							AND pcp.parametro IN ('"
							. implode("','",$campos)
						  . "')
					 )
		 ";
		}

		$sql .= '  ORDER BY descripcion   ';

		return toba_contexto_info::get_db()->consultar($sql);  
	}

	//-------------------------------------------------
	//---------------- VARIOS -------------------------
	//-------------------------------------------------

	/**
	* Tipos de pagina
	*/
	static function get_tipos_pagina($proyecto = null)
	{
		if (!isset($proyecto)) {
			$proyecto =  toba_contexto_info::get_db()->quote(toba_contexto_info::get_proyecto());
		} else {
			$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		}
		$sql = "SELECT proyecto, pagina_tipo, descripcion
				FROM apex_pagina_tipo 
				WHERE ( proyecto = 'toba' OR proyecto = $proyecto)
				ORDER BY 3";
		return toba_contexto_info::get_db()->consultar($sql);
	}
	
	static function get_tipos_pagina_proyecto()
	{
		$proyecto = toba_contexto_info::get_db()->quote(toba_contexto_info::get_proyecto());
		$sql = "SELECT proyecto, pagina_tipo, descripcion, clase_archivo
				FROM apex_pagina_tipo 
				WHERE ( proyecto = $proyecto)
				ORDER BY 3";
		return toba_contexto_info::get_db()->consultar($sql);
	}	
	
	/**
	 * Retorna el tipo de pagina predeterminado para el proyecto actual
	 */
	static function get_tipo_pagina_defecto($proyecto=null)
	{
		if (!isset($proyecto)) {
			$proyecto = toba_contexto_info::get_proyecto();
		}
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$sql = "
			SELECT 
				COALESCE(t.proyecto, 'toba')		 as proyecto,
				COALESCE(t.pagina_tipo, 'normal')	 as pagina_tipo
			FROM 
				apex_proyecto p LEFT OUTER JOIN apex_pagina_tipo t 
					ON (p.proyecto = t.proyecto AND p.pagina_tipo = t.pagina_tipo) 
			WHERE ( p.proyecto = $proyecto)
		";
		return toba_contexto_info::get_db()->consultar_fila($sql);
	}

	/**
	* BUFFERs
	*/
	static function get_buffers()
	{
		$proyecto = toba_contexto_info::get_db()->quote(toba_contexto_info::get_proyecto());
		$sql = "SELECT proyecto, buffer, descripcion_corta 
				FROM apex_buffer 
				WHERE ( proyecto = 'toba' OR proyecto = $proyecto)
				ORDER BY 2";
		return toba_contexto_info::get_db()->consultar($sql);		
	}

	/**
	* Patrones
	*/
	static function get_comportamientos()
	{
		$proyecto = toba_contexto_info::get_db()->quote(toba_contexto_info::get_proyecto());
		$sql = "SELECT proyecto, patron, descripcion_corta FROM apex_patron 
				WHERE patron != 'especifico'
				AND ( proyecto = 'toba' OR proyecto = $proyecto )
				ORDER BY 3";
		return toba_contexto_info::get_db()->consultar($sql);
	}

	/**
	* Zonas
	*/
	static function get_zonas()
	{
		$proyecto = toba_contexto_info::get_db()->quote(toba_contexto_info::get_proyecto());
		$sql = "SELECT proyecto, zona, nombre
				FROM apex_item_zona
				WHERE ( proyecto = 'toba' OR proyecto = $proyecto)
				ORDER BY nombre";
		return toba_contexto_info::get_db()->consultar($sql);		
	}			

	/**
	* Tipos de solicitud
	*/
	static function get_tipo_observaciones_solicitud()
	{
		$proyecto = toba_contexto_info::get_db()->quote(toba_contexto_info::get_proyecto()) ;
		$sql = "SELECT proyecto, solicitud_obs_tipo, 
						descripcion 
				FROM apex_solicitud_obs_tipo 
				WHERE ( proyecto = 'toba' OR proyecto = $proyecto) ";
		return toba_contexto_info::get_db()->consultar($sql);
	}

	/**
	* Lista de FUENTEs de DATOS
	*/
	static function get_fuentes_datos($proyecto=null)
	{
		if (!isset($proyecto)) {
			$proyecto = toba_contexto_info::get_proyecto();
		}
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$sql = "SELECT 
						proyecto, 
						fuente_datos, 
						descripcion as descripcion_corta, 
						descripcion, 
						schema,
						permisos_por_tabla,
						tiene_auditoria
				FROM apex_fuente_datos
				WHERE ( proyecto = $proyecto )
				ORDER BY 2 ASC;";
		return toba_contexto_info::get_db()->consultar($sql);	
	}
	
	static function get_fuente_datos_defecto($proyecto=null)
	{
		if (!isset($proyecto)) {
			$proyecto = toba_contexto_info::get_proyecto();
		}
		$sql = 'SELECT fuente_datos FROM apex_proyecto WHERE proyecto='.toba_contexto_info::get_db()->quote($proyecto);
		$rs = toba_contexto_info::get_db()->consultar_fila($sql);	
		return $rs['fuente_datos'];
	}	
	
	/**
	 * Lista de motores de base de datos disponibles para definir una fuente
	 */
	static function get_lista_motores()
	{
		$sql = 'SELECT
					fuente_datos_motor, 
					nombre
				FROM apex_fuente_datos_motor
				ORDER BY nombre
				';
		return toba_contexto_info::get_db()->consultar($sql);	
	}

	static function get_puerto_motor($motor=null)
	{
		$resultado = 0;
		switch($motor){
			case 'informix': $resultado = 1526;
										break;
			case 'odbc': $resultado = 0;
									break;
			case 'mysql': $resultado = 3306;
									break;
			case 'postgres7' : $resultado = 5432;
											break;
		}

		return $resultado;
	}

	/**
	* Propiedadesd de una FUENTEs de DATOS
	*/
	static function get_info_fuente_datos($id_fuente, $proyecto=null)
	{
		if (!isset($proyecto)) {
			$proyecto = toba_contexto_info::get_proyecto();
		}
		$id_fuente = toba_contexto_info::get_db()->quote($id_fuente);
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$sql = "SELECT 	*,
						link_instancia 		as link_base_archivo,
						fuente_datos_motor 	as motor,
						host 				as profile,
						tiene_auditoria
				FROM 	apex_fuente_datos
				WHERE	fuente_datos = $id_fuente
				AND 	proyecto = $proyecto";
		$rs = toba_contexto_info::get_db()->consultar($sql);
		if (empty($rs)) {
			throw new toba_error("No se puede encontrar la fuente $id_fuente en el proyecto $proyecto");	
		}
		return $rs[0];
	}
	
	/**
	 * Determina si el proyecto cuenta con una fuente de datos propia
	 */
	static function hay_fuente_definida($proyecto)
	{
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$sql = "SELECT count(*) as cantidad
				FROM 	apex_fuente_datos
				WHERE	proyecto = $proyecto";
		$rs = toba_contexto_info::get_db()->consultar($sql);
		return ($rs[0]['cantidad'] > 0);
	}
	
	/**
	 * Obtiene la lista de schemas configurados para la fuente de datos particular
	 */
	static function get_schemas_fuente($proyecto, $fuente)
	{
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$fuente = toba_contexto_info::get_db()->quote($fuente);
		
		$sql = "SELECT	nombre as \"schema\",
						principal
				FROM  apex_fuente_datos_schemas 
				WHERE	
						proyecto = $proyecto
				AND		fuente_datos = $fuente;"; 
		$rs = toba_contexto_info::get_db()->consultar($sql);
		return $rs;
	}
	
	/**
	*	Lista de Skins
	*/
	static function get_lista_skins()
	{
		$proyecto = toba_contexto_info::get_db()->quote(toba_contexto_info::get_proyecto());
		$sql = "SELECT
					estilo, 
					proyecto,
					descripcion
				FROM apex_estilo
				WHERE
						proyecto = $proyecto
					OR	proyecto = 'toba'
				ORDER BY descripcion";
		return toba_contexto_info::get_db()->consultar($sql);
	}

	static function get_lista_estilos_columnas($id_estilo = null)
	{
		$sql = 'SELECT columna_estilo, css FROM apex_columna_estilo';
		if (! is_null($id_estilo)){
			$sql .= ' WHERE columna_estilo = '. quote($id_estilo);
		}
		
		return toba_contexto_info::get_db()->consultar($sql);
	}
	
	static function get_nombre_clase_css($id_estilo)
	{
		$nombre = '';
		$disponibles = self::get_lista_estilos_columnas($id_estilo);
		if (! empty($disponibles)) {
			$nombre = $disponibles[0]['css'];
		}	
		return $nombre;
	}
	
	static function get_estilo_defecto_formateo_columna($formateo)
	{
		$formateo = toba_contexto_info::get_db()->quote($formateo);
		$sql = "SELECT estilo_defecto FROM apex_columna_formato WHERE columna_formato= $formateo";
		$rs = toba_contexto_info::get_db()->consultar_fila($sql);
		return $rs['estilo_defecto'];
	}
	
	static function get_mensajes($proyecto=null)
	{
		if (!isset($proyecto)) $proyecto = toba_contexto_info::get_proyecto();
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);		
		$clausula = array("proyecto = $proyecto");
		return self::get_mensajes_filtrados($clausula);
	}

	static function get_mensajes_filtrados($clausulas = array())
	{
		$sql = 'SELECT proyecto, msg, indice, msg_tipo as tipo, descripcion_corta
				FROM 	apex_msg
				ORDER BY msg, indice';
		if (! empty($clausulas)) {
			$sql = sql_concatenar_where($sql, $clausulas);
		}
		toba::logger()->debug("mensajes : $sql");
		return toba_contexto_info::get_db()->consultar($sql);
	}

	static function get_mensajes_objeto($objeto, $proyecto=null)
	{
		if (!isset($proyecto)) $proyecto = toba_contexto_info::get_proyecto();
		$clausulas = array(	'objeto_proyecto ='. toba_contexto_info::get_db()->quote($proyecto),
											'objeto = '.toba_contexto_info::get_db()->quote($objeto));
		return self::get_mensajes_objeto_filtrados($clausulas);
	}

	static function get_mensajes_objeto_filtrados($clausulas = array())
	{
		$sql = 'SELECT objeto_proyecto, objeto_msg, indice, msg_tipo as tipo, descripcion_corta
				FROM 	apex_objeto_msg
				ORDER BY objeto_msg, indice';
		if (! empty($clausulas)) {
			$sql = sql_concatenar_where($sql, $clausulas);
		}
		toba::logger()->debug("mensajes : $sql");
		return toba_contexto_info::get_db()->consultar($sql);
	}

	static function get_puntos_control($proyecto = null)
	{
		if (!isset($proyecto)) $proyecto = toba_contexto_info::get_proyecto();
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$sql = "SELECT proyecto, pto_control, descripcion
					FROM apex_ptos_control
					WHERE proyecto = $proyecto
					ORDER BY pto_control;";
		return toba_contexto_info::get_db()->consultar($sql);
	}
	
	
	//-------------------------------------------------
	//---------------- ABM de ei_filtro ---------------------------
	//-------------------------------------------------
	
	static function get_filtro_tipo_columnas()
	{
		$proyecto = toba_contexto_info::get_db()->quote(toba_contexto_info::get_proyecto());
		$sql = "SELECT tipo_col, descripcion
					FROM apex_objeto_ei_filtro_tipo_col
					WHERE 
							proyecto = 'toba'
						OR	proyecto = $proyecto";
	    return toba_contexto_info::get_db()->consultar($sql);
	}

	
	static function get_filtro_efs($tipo_col, $es_multiple)
	{
		if ($tipo_col == 'opciones') {
			$multiple = ($es_multiple == '1')? ' 1' : '0';
			$sql = "
				SELECT elemento_formulario, descripcion
				FROM apex_elemento_formulario
				WHERE 
						obsoleto=0
					AND es_seleccion = 1
					AND es_seleccion_multiple = $multiple
				ORDER BY 2
			";
			toba::logger()->debug($sql);
			toba::logger()->var_dump($es_multiple);
			return toba_contexto_info::get_db()->consultar($sql);
		}
	}
	
	//-------------------------------------------------
	//---------------- LOGS ---------------------------
	//-------------------------------------------------

	/**
		ATENCION, clase 'toba' hardcodeada en el item del editor...
			Hay que arreglarlo antes de que los proyectos agreguen componentes
	*/
	static function get_log_modificacion_componentes()
	{
		$proyecto = toba_contexto_info::get_db()->quote(toba_contexto_info::get_proyecto()) ;
		$schema_logs = toba_contexto_info::get_db()->get_schema() . '_logs';
		$sql = "	SELECT l.momento as momento,
						l.usuario as usuario,	
						'[' || coalesce(CAST(l.objeto as text), '...') || '] ' 
							|| coalesce(o.nombre, i.nombre) as componente_nombre,
						l.objeto_proyecto as componente_proyecto,
						coalesce(CAST(l.objeto as text), l.item) as componente_id,
						coalesce(c.editor_proyecto,'toba_editor') as editor_proyecto, 
						coalesce(c.editor_item,'1000240') as editor_item,
						coalesce(c.icono,'item.gif') as icono_tipo_componente, 
						l.observacion as observacion
					FROM $schema_logs.apex_log_objeto l
					LEFT OUTER JOIN 
						apex_objeto o INNER JOIN apex_clase c ON (o.clase = c.clase AND o.clase_proyecto = c.proyecto)
						ON (o.proyecto = l.objeto_proyecto AND o.objeto = l.objeto)
					LEFT OUTER JOIN
						apex_item i ON (l.item = i.item)
					WHERE objeto_proyecto = $proyecto
					AND ( (o.objeto IS NOT NULL) OR (i.item IS NOT NULL) ) -- no mostrar eliminados
					ORDER BY momento DESC;";
		return toba_contexto_info::get_db()->consultar($sql);
	}

	//------------------------------------------------------------------------------
	//----------  MOLDES
	//------------------------------------------------------------------------------

	static function get_opciones_predefinidas_molde($proyecto = null)
	{
		if (!isset($proyecto)) $proyecto = toba_contexto_info::get_proyecto();
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$sql = "	SELECT *
					FROM apex_molde_opciones_generacion
					WHERE proyecto = $proyecto";
		return toba_contexto_info::get_db()->consultar_fila($sql);			
	}

	static function get_lista_tipo_molde($operacion_tipo = null)
	{
		$where_operacion = '';
		if (isset($operacion_tipo)) {
			$where_operacion = 'WHERE operacion_tipo='.toba_contexto_info::get_db()->quote($operacion_tipo);
		}
		$sql = "	SELECT	operacion_tipo,
							clase,
							descripcion_corta,
							descripcion,
							ci,
							icono,
							vista_previa
					FROM apex_molde_operacion_tipo
					$where_operacion
					ORDER BY orden";	
		if (!isset($operacion_tipo)) {
			return toba_contexto_info::get_db()->consultar($sql);
		} else {
			return toba_contexto_info::get_db()->consultar_fila($sql);			
		}
	}
	

	static function get_lista_moldes_existentes($proyecto=null, $tipo_operacion = null)
	{
		if (!isset($proyecto)) $proyecto = toba_contexto_info::get_proyecto();
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$sql_tipo_operacion = '';
		if (isset($tipo_operacion)) {
			$sql_tipo_operacion = ' AND t.operacion_tipo = '.toba_contexto_info::get_db()->quote($tipo_operacion);
		}
		$sql = "SELECT		t.operacion_tipo 		as tipo,
							t.descripcion_corta		as tipo_desc,
							o.proyecto				as proyecto,
							o.molde					as molde,
							o.nombre				as nombre,
							o.carpeta_archivos		as carpeta_archivos,
							o.item					as item,
							(SELECT COUNT(*) FROM apex_molde_operacion_log WHERE molde = o.molde AND proyecto = o.proyecto) 
													as generaciones
					FROM	apex_molde_operacion o,
							apex_molde_operacion_tipo t
					WHERE	o.operacion_tipo = t.operacion_tipo 
							$sql_tipo_operacion
					AND		o.proyecto = $proyecto
					ORDER BY 1,3;";
		return toba_contexto_info::get_db()->consultar($sql);
	}

	static function get_lista_ejecuciones_molde($proyecto, $molde)
	{
		$molde = toba_contexto_info::get_db()->quote($molde);
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$sql = "SELECT		generacion,
							momento
					FROM	apex_molde_operacion_log
					WHERE	molde = $molde
					AND		proyecto = $proyecto
					ORDER BY 1 DESC;";
		return toba_contexto_info::get_db()->consultar($sql);
	}

	static function get_info_molde($proyecto, $molde)
	{
		if (!isset($proyecto)) $proyecto = toba_contexto_info::get_proyecto();
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$molde = toba_contexto_info::get_db()->quote($molde);
		$sql = "SELECT		t.descripcion_corta		as tipo,
							o.proyecto				as proyecto,
							o.molde					as molde,
							o.nombre				as nombre,
							o.carpeta_archivos		as carpeta_archivos,
							o.item					as item,
							(SELECT COUNT(*) FROM apex_molde_operacion_log WHERE molde = o.molde AND proyecto = o.proyecto) 
													as generaciones
					FROM	apex_molde_operacion o,
							apex_molde_operacion_tipo t
					WHERE	o.operacion_tipo = t.operacion_tipo
					AND		o.proyecto = $proyecto
					AND		o.molde = $molde;";
		return toba_contexto_info::get_db()->consultar_fila($sql);
	}

	//------------------------------------------------------------------------------
	//----------  CONSULTAs PHP  ---------------------------------------------------
	//------------------------------------------------------------------------------

	static function get_consultas_php($proyecto=null)
	{
		if (!isset($proyecto)) $proyecto = toba_contexto_info::get_proyecto();
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$sql = "SELECT		proyecto,
							consulta_php,
							clase,
							archivo,
							clase || ' (' || archivo || ')' as descripcion
					FROM	apex_consulta_php
					WHERE	proyecto = $proyecto
					ORDER BY clase, consulta_php;";
		return toba_contexto_info::get_db()->consultar($sql);
	}
	
	static function get_consulta_php($consulta, $proyecto)
	{
		if (!isset($proyecto)) $proyecto = toba_contexto_info::get_proyecto();
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$sql = "SELECT		punto_montaje,
							clase,
							archivo,
							archivo_clase
					FROM	apex_consulta_php
					WHERE	proyecto = $proyecto AND consulta_php = $consulta
		";
		return toba_contexto_info::get_db()->consultar_fila($sql);		
	}
	
	static function get_metodos_consulta_php($consulta_php, $proyecto=null)
	{
		if (is_null($proyecto)) $proyecto = toba_contexto_info::get_proyecto();
		//Busco nombre de clase y de archivo.
		if (is_array($consulta_php)) {
			$archivo_nombre = $consulta_php['carga_php_include'];
			$clase_nombre = $consulta_php['carga_php_clase'];
		} else {
			$datos = self::get_consulta_php($consulta_php, $proyecto);
			$archivo_nombre = $datos['archivo'];
			$clase_nombre = ( ! is_null($datos['archivo_clase'])) ? $datos['archivo_clase']: $datos['clase'];
		}
		
		if (is_null($clase_nombre)) {
			toba::logger()->error('Se intento cargar una clase de consulta con nombre nulo, pertenece al archivo '. $archivo_nombre);
			throw new toba_error_usuario('La clase de consulta seleccionada para la carga no es válida, verifique su definición');	
		}
		
		//Si hay puntos de montaje, cargo el autoload de c/u
		if (isset($datos['punto_montaje'])) {
			$puntos = self::get_pms($proyecto);
			$pm_obj = array();
			foreach($puntos as $punto) {
				$pm_obj[$punto['id']] = toba_modelo_pms::get_pm($punto['id'], $proyecto);
				$pm_obj[$punto['id']]->registrar_autoload();
			}
			//Busco el path al archivo
			$punto = $pm_obj[$datos['punto_montaje']]; //toba_modelo_pms::get_pm($datos['punto_montaje'], $proyecto);
			$archivo  = $punto->get_path_absoluto().'/'.$archivo_nombre;
		} else {
			$archivo = toba::instancia()->get_path_proyecto($proyecto).'/php/'.$archivo_nombre;
		}
		
		//Si aun no existe el archivo, puede estar buscandose un archivo de toba
		if (! file_exists($archivo)) {			
			$archivo = toba_nucleo::toba_dir().'/php/'.$archivo_nombre;
		}
		$metodos = array();
		if (file_exists($archivo)) {
			include_once($archivo);			//Incluyo el archivo porque no se si esta en un autoload o no.
			
			//Usar Reflexion para obtener los metodos.
			$reflector = new ReflectionClass($clase_nombre);
			$metodos = $reflector->getMethods(ReflectionMethod::IS_STATIC | ReflectionMethod::IS_PUBLIC);
			
			//$metodos = toba_archivo_php::codigo_get_nombre_metodos(file_get_contents($archivo), true);
		} 
		
		//Armo la salida con los nombres de los metodos accesibles
		$salida = array();
		foreach ($metodos as $obj) {
			$salida[] = array('metodo' => $obj->getName());
		}
				
		//Desregistro el autoload de los puntos de montaje existentes para que no interfieran con el editor.
		if (! empty($pm_obj)) {
			foreach ($pm_obj as $klave => $obj) {
				$pm_obj[$klave]->desregistrar_autoload();				
			}
		} 		
		
		return $salida;
	}

	//------------------------------------------------------------------------------
	//----------  DIMENSIONES
	//------------------------------------------------------------------------------

	static function get_dimensiones($proyecto=null)
	{
		if (!isset($proyecto)) $proyecto = toba_contexto_info::get_proyecto();
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$sql = "SELECT		fuente_datos,
							proyecto,
							dimension,
							nombre,
							tabla,
							descripcion
					FROM	apex_dimension
					WHERE	proyecto = $proyecto
					ORDER BY nombre, dimension;";
		return toba_contexto_info::get_db()->consultar($sql);
	}

	static function get_datos_dimension($proyecto, $dimension)
	{
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$dimension = toba_contexto_info::get_db()->quote($dimension);
		$sql = "SELECT		*
					FROM	apex_dimension
					WHERE	proyecto = $proyecto
					AND		dimension = $dimension
					ORDER BY nombre
		";
		return toba_contexto_info::get_db()->consultar_fila($sql);
	}

	static function get_cantidad_dimensiones_fuente($fuente, $proyecto=null)
	{
		if (!isset($proyecto)) $proyecto = toba_contexto_info::get_proyecto();
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$fuente = toba_contexto_info::get_db()->quote($fuente);
		$sql = "SELECT		COUNT(dimension) as cantidad
					FROM	apex_dimension
					WHERE	proyecto = $proyecto
					AND		fuente_datos = $fuente;";
		
		$temp = toba_contexto_info::get_db()->consultar_fila($sql);
		return $temp['cantidad'];
	}

	//------------------------------------------------------------------------------
	//----------  RELACIONES entre TABLAS
	//------------------------------------------------------------------------------
	
	static function get_relaciones_tablas($fuente_datos, $proyecto=null)
	{
		if (!isset($proyecto)) $proyecto = toba_contexto_info::get_proyecto();
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$fuente_datos = toba_contexto_info::get_db()->quote($fuente_datos);
		$sql = "SELECT *
				FROM
					apex_relacion_tablas
				WHERE fuente_datos = $fuente_datos
				AND proyecto = $proyecto
				ORDER BY tabla_1";
		return toba_contexto_info::get_db()->consultar($sql);
	}
	
	//------------------------------------------------------------------------------
	//----------  SERVICIOS WEB
	//------------------------------------------------------------------------------	
	
	static function get_servicios_web_acc($proyecto=null)
	{
		if (!isset($proyecto)) $proyecto = toba_contexto_info::get_proyecto();
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$sql = "SELECT		
					proyecto,
					servicio_web
				FROM	apex_servicio_web
				WHERE	proyecto = $proyecto
				ORDER BY servicio_web ASC; ";
		return toba_contexto_info::get_db()->consultar($sql);
	}
	//------------------------------------------------------------------------------
	//----------  GRAFICOS
	//------------------------------------------------------------------------------

	static function get_lista_tipos_grafico()
	{
		$sql = "SELECT grafico as id, descripcion_corta as desc FROM apex_grafico";
		return toba_contexto_info::get_db()->consultar($sql);
	}

	//------------------------------------------------------------------------------
	//----------  PUNTOS DE MONTAJE
	//------------------------------------------------------------------------------

	static function get_pms($proyecto = null)
	{
		if (!isset($proyecto)) $proyecto = toba_contexto_info::get_proyecto();
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$sql = "SELECT * FROM apex_puntos_montaje WHERE proyecto=$proyecto";
		return toba_contexto_info::get_db()->consultar($sql);
	}
}
?>
