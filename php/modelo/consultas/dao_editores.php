<?php
class dao_editores
{
	//---------------------------------------------------
	//---------------- PROYECTOS-------------------------
	//---------------------------------------------------
	
	/**
	 * Retorna la lista de proyectos que el usuario actual puede modificar
	 * @todo Utilizar la clase instancia
	 */
	static function get_proyectos_accesibles()
	{
		$sql = "
			SELECT 	
				p.proyecto, 
				p.descripcion_corta
			FROM
			 	apex_proyecto p,
				apex_usuario_proyecto up
			WHERE 	
				p.proyecto = up.proyecto
			AND	up.usuario = '".toba::usuario()->get_id()."'
			ORDER BY orden;";
		return contexto_info::get_db()->consultar($sql);
	}

	//---------------------------------------------------
	//---------------- CLASES ---------------------------
	//---------------------------------------------------

	static function get_clases_validas()
	{
		return array(	'objeto_ci',
						'objeto_cn',
						'objeto_ei_cuadro',
						'objeto_ei_formulario',
						'objeto_ei_formulario_ml',
						'objeto_ei_filtro',
						'objeto_ei_arbol',
						'objeto_ei_calendario',
						'objeto_ei_archivos',
						'objeto_ei_esquema',						
						'objeto_datos_tabla',
						'objeto_datos_relacion' );
	}

	static function get_clases_validas_contenedor($contenedor=null)
	{
		//item, ci, ci_pantalla, datos_relacion
		if(!isset($contenedor)) return self::get_clases_validas();
		if($contenedor=="item") return array('objeto_ci', 'objeto_cn');
		if($contenedor=="datos_relacion") return array('objeto_datos_tabla');
		if($contenedor=="ci"){
			return array(	'objeto_ci',
							'objeto_ei_cuadro',
							'objeto_ei_formulario',
							'objeto_ei_formulario_ml',
							'objeto_ei_filtro',
							'objeto_ei_arbol',
							'objeto_ei_calendario',
							'objeto_ei_archivos',
							'objeto_ei_esquema',
							'objeto_datos_tabla',
							'objeto_datos_relacion' );
		}
		if($contenedor=="ci_pantalla"){
			return array(	'objeto_ci',
							'objeto_ei_cuadro',
							'objeto_ei_formulario',
							'objeto_ei_formulario_ml',
							'objeto_ei_filtro',
							'objeto_ei_arbol',
							'objeto_ei_calendario',
							'objeto_ei_archivos',
							'objeto_ei_esquema' );
		}
		if($contenedor=="cn"){
			return array(	'objeto_datos_tabla',
							'objeto_datos_relacion' );
		}		
		//Por defecto devulevo todo
		return self::get_clases_validas();
	}
	
	static function get_clases_contenedoras()
	{
		return array(	
						array('proyecto' => 'toba', 'clase' =>'objeto_ci'),
						array('proyecto' => 'toba', 'clase' => 'objeto_datos_relacion')
					);
	}

	static function get_lista_clases_item()
	{
		return self::get_lista_clases_toba(false, self::get_clases_validas_contenedor('item'));
	}
	
	static function get_lista_clases_validas_en_ci()
	{
		return self::get_lista_clases_toba(false, self::get_clases_validas_contenedor('objeto_ci'));
	}	
	
	/*
		Las clases usan un ID concatenado para que las cascadas
		las soporten (actualmente pasan un parametro solo)
	*/
	static function get_lista_clases_toba($todas=false, $especificas=null)
	{
		if ($todas) {
			$sql_todas = "";
		} else {
			$clases = (isset($especificas)) ? $especificas : self::get_clases_validas();
			$sql_todas = "clase IN ('". implode("','", $clases) ."') AND";
		}
			
		$sql = "SELECT 	proyecto || ',' || clase as clase, 
						descripcion_corta as descripcion
				FROM apex_clase 
				WHERE 
					$sql_todas
					(proyecto = '". contexto_info::get_proyecto() ."' OR proyecto='toba')
				ORDER BY 2";
		return contexto_info::get_db()->consultar($sql);
	}	

	static function get_todas_clases_toba()
	{
		return self::get_lista_clases_toba(true);	
	}
	
	static function get_archivo_de_clase($proyecto, $clase)
	{
		$sql = "SELECT 	archivo
				FROM apex_clase 
				WHERE 	clase = '$clase'
				AND		proyecto = '$proyecto'";
		$temp = contexto_info::get_db()->consultar($sql);
		if(is_array($temp)){
			return $temp[0]['archivo'];
		}
	}
	
	static function get_clases_editores($contenedor=null)
	{
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
					c.clase_tipo = ct.clase_tipo AND 
					c.clase IN ('". implode("','", self::get_clases_validas_contenedor($contenedor) ) ."')	AND
						--El proyecto es Toba o el actual
					(c.proyecto = '". contexto_info::get_proyecto() ."' OR c.proyecto = 'toba') AND
					c.editor_item IS NOT NULL
				ORDER BY ct.orden DESC";
		return contexto_info::get_db()->consultar($sql);
	}
	
	static function get_clases_tipos()
	{
		$sql = "SELECT DISTINCT
					ct.clase_tipo,
					ct.descripcion_corta
				FROM 
					apex_clase c,
					apex_clase_tipo ct
				WHERE
					c.clase_tipo = ct.clase_tipo AND 
					c.clase IN ('". implode("','",self::get_clases_validas() ) ."')
				ORDER BY ct.clase_tipo";
		return contexto_info::get_db()->consultar($sql);
	}

	static function get_ci_editor_clase($proyecto, $clase)
	{
		$sql = "SELECT 
				 	o.proyecto,
					o.objeto
				FROM
					apex_clase c,
					apex_item_objeto io,
					apex_objeto o
				WHERE
					c.clase = '$clase' AND
					c.proyecto = '$proyecto' AND
					c.editor_item = io.item AND				-- Se busca el item editor
					c.editor_proyecto = io.proyecto AND
					io.objeto = o.objeto AND				-- Se busca el CI del item
					io.proyecto = o.proyecto AND
					o.clase = 'objeto_ci'";
		$res = contexto_info::get_db()->consultar($sql);
		return $res[0];
	}
	
	static function get_pantallas_de_ci($objeto)
	{
		if (is_numeric($objeto)) {
			$sql = "SELECT
						pantalla,
						identificador || ' - ' || COALESCE(etiqueta, '') as descripcion
					FROM
						apex_objeto_ci_pantalla
					WHERE
						objeto_ci_proyecto = '". contexto_info::get_proyecto() ."' AND
						objeto_ci = '$objeto'
			";
			return contexto_info::get_db()->consultar($sql);
		} else {
			return array();	
		}
	}

	static function get_clases_con_fuente_datos()
	{
		$clases = array();
		$clases[] = 'objeto_ei_formulario';
		$clases[] = 'objeto_ei_formulario_ml';
		$clases[] = 'objeto_ei_filtro';
		$clases[] = 'objeto_datos_tabla';
		$clases[] = 'objeto_datos_relacion';
		$clases[] = 'objeto_cn';
		return $clases;	
	}
	
	//-------------------------------------------------
	//---------------- ITEMS --------------------------
	//-------------------------------------------------

	/**
	*	Retorna la lista de todos los items del proyecto actual (no carpetas)
	*/
	static function get_lista_items()
	{
		$sql = "
			SELECT 
				proyecto, 
				item 						as id, 
				nombre						as descripcion
			FROM apex_item 
			WHERE 
				(carpeta <> '1' OR carpeta IS NULL) AND
				proyecto = '". contexto_info::get_proyecto() ."'
			ORDER BY nombre;
		";
		return contexto_info::get_db()->consultar($sql);
	}
	
	
	/**
	*	Retorna la lista de items en un formato adecuado para un combo
	*/
	static function get_items_para_combo()
	{
		require_once("modelo/lib/catalogo_items.php");
		$catalogador = new catalogo_items(contexto_info::get_proyecto());
		$catalogador->cargar_todo();	
		foreach($catalogador->items() as $item) {
			if (! $item->es_carpeta()) {
				$nivel = $item->get_nivel_prof() - 1;
				if($nivel >= 1){
					$inden = "&nbsp;" . str_repeat("&nbsp" . str_repeat("&nbsp;",8), $nivel -1) . "|__&nbsp;";
				}else{
					$inden = "";
				}
				$datos[] =  array('proyecto' => contexto_info::get_proyecto(),
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
			$proyecto = contexto_info::get_proyecto();
		}
		require_once("modelo/lib/catalogo_items.php");
		$catalogador = new catalogo_items($proyecto);
		$catalogador->cargar_todo(array('solo_carpetas' => 1));
		foreach($catalogador->items() as $carpeta) {
			$nivel = $carpeta->get_nivel_prof() - 1;
			if($nivel >= 0) {
				$inden = "&nbsp;" . str_repeat("|" . str_repeat("&nbsp;",8), $nivel) . "|__&nbsp;";
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
			$proyecto = contexto_info::get_proyecto();
		}
		$sql = "
			SELECT 
				item 									as id, 
				nombre || ' - (' || item || ')'			as descripcion
			FROM apex_item 
			WHERE 
				(carpeta <> '1' OR carpeta IS NULL) AND
				( (padre = '$carpeta') AND (padre_proyecto='$proyecto') )
					AND	proyecto = '$proyecto'
			ORDER BY nombre;
		";
		return contexto_info::get_db()->consultar($sql);
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
		$rs = contexto_info::get_db()->consultar($sql);
		if (!empty($rs)) {
			return $rs[0]['padre'];	
		}
	}

	//---------------------------------------------------
	//------------ PUNTOS DE CONTROL --------------------
	//---------------------------------------------------

  static function get_puntos_de_control($filtro, $id_contenedor = null, $id_objeto = null, $campos = null)
  {
    $sql = "SELECT pto_control,
                     descripcion
                FROM apex_ptos_control 
               WHERE proyecto = '". contexto_info::get_proyecto() ."'

     ";
    
    // Elimina los puntos de control que ya se utilizaron en 
    // pantallas anteriores del mismo item. 
    if ($filtro == 'P' || $filtro == 'C') 
    {
      if (isset($id_objeto))
        $sql .= "    AND pto_control NOT IN (
                   
                    SELECT pce.pto_control
                     FROM apex_ptos_control_x_evento pce,
                          apex_objeto_dependencias ode,
                          apex_objeto_dependencias ode2,
                          apex_objeto_eventos oe
                          
                     WHERE ode.proyecto          =  '". contexto_info::get_proyecto() ."'
                       AND ode.objeto_proveedor  =  '". $id_objeto ."'
                       
                       AND ode.proyecto          =  ode2.proyecto
                       AND ode.objeto_consumidor =  ode2.objeto_consumidor
   
                       AND ode2.proyecto         =  oe.proyecto
                       AND ode2.objeto_proveedor =  oe.objeto
                       AND ode2.objeto_proveedor <> '". $id_objeto ."'
   
                       AND oe.proyecto           =  pce.proyecto
                       AND oe.evento_id          =  pce.evento_id
                 ) 
        ";    

      if (isset($id_contenedor))
        $sql .= "    AND pto_control NOT IN (

                    SELECT pce.pto_control
                     FROM apex_ptos_control_x_evento pce,
                          apex_objeto_dependencias ode,
                          apex_objeto_eventos oe
                          
                     WHERE ode.proyecto          =  '". contexto_info::get_proyecto() ."'
                       AND ode.objeto_consumidor =  '". $id_contenedor ."'
                       
                       AND ode.proyecto          =  oe.proyecto
                       AND ode.objeto_proveedor  =  oe.objeto
   
                       AND oe.proyecto           =  pce.proyecto
                       AND oe.evento_id          =  pce.evento_id
                 ) 
        ";    
    }

    // Solo muestra los puntos de control cuyos parametros tengan el mismo 
    // nombre que los definidos en el cuadro en edición
    if ($filtro == 'C')
    {
       $sql .= "  AND pto_control IN (

                     SELECT DISTINCT pcp.pto_control
                       FROM apex_ptos_control_param pcp
                      WHERE pcp.proyecto = '". contexto_info::get_proyecto() ."'
                        AND pcp.parametro IN ('" 
                        . implode("','",$campos) 
                      . "')
                 ) 
     ";    
    }

    $sql .= "  ORDER BY descripcion   ";

		return contexto_info::get_db()->consultar($sql);  
  }

	//---------------------------------------------------
	//---------------- OBJETOS --------------------------
	//---------------------------------------------------

	static function get_lista_objetos_toba($clase)
	{
		$clase = explode(",",$clase);
		$sql = "SELECT 	proyecto, 
						objeto, 
						objeto							   as id,
						'[' || objeto || '] -- ' || nombre as descripcion
				FROM apex_objeto 
				WHERE 	clase = '{$clase[1]}'
				AND 	proyecto = '". contexto_info::get_proyecto() ."'
				ORDER BY nombre";
		return contexto_info::get_db()->consultar($sql);
	}
	
	static function get_info_dependencia($objeto_proyecto, $objeto)
	//Carga externa para un db_registros de dependencias
	{
		$sql = "SELECT 	o.clase || ' - ' || '[' || o.objeto || '] - ' || o.nombre as nombre_objeto,
						'[' || o.objeto || '] - ' || o.nombre as descripcion,
						o.clase_proyecto || ',' || o.clase as clase
				FROM 	apex_clase c, apex_objeto o
				WHERE 	o.clase = c.clase
				AND 	o.clase_proyecto = c.proyecto
				AND 	o.proyecto = '$objeto_proyecto'
				AND 	o.objeto = '$objeto'";
		return contexto_info::get_db()->consultar($sql);
	}

	/**
	*	Retorna el nombre de la clase del objeto
	*/
	static function get_clase_de_objeto($id)
	{
		$sql = "
			SELECT 
				o.clase
			FROM 
				apex_objeto o
			WHERE 
				(o.objeto = '{$id[1]}') AND 
				(o.proyecto = '{$id[0]}')
		";		
		$datos = contexto_info::get_db()->consultar($sql);
		return $datos[0]['clase'];
	}
	
	//---------------------------------------------------
	//-- DATOS RELACION----------------------------------
	//---------------------------------------------------
	
	/**
	*	Retorna el id del objeto datos_relacion asociado a la clase
	*/
	static function get_dr_de_clase($clase)
	{
		$drs = array(
			'objeto_datos_relacion' 	=> array( toba_editor::get_id(), '1532'),
			'objeto_datos_tabla' 		=> array( toba_editor::get_id(), '1533'),
			'objeto_ei_arbol'			=> array( toba_editor::get_id(), '1537'),
			'objeto_ei_archivos'		=> array( toba_editor::get_id(), '1538'),
			'objeto_ei_calendario'		=> array( toba_editor::get_id(), '1539'),
			'objeto_ci' 				=> array( toba_editor::get_id(), '1507'),
			'objeto_ei_cuadro' 			=> array( toba_editor::get_id(), '1531'),
			'objeto_ei_filtro' 			=> array( toba_editor::get_id(), '1535'),
			'objeto_ei_formulario' 		=> array( toba_editor::get_id(), '1534'),
			'objeto_ei_formulario_ml' 	=> array( toba_editor::get_id(), '1536'),			
			'objeto_ei_arbol' 			=> array( toba_editor::get_id(), '1610'),	
			'objeto_cn'					=> array( toba_editor::get_id(), '1610'),
			'item'						=> array( toba_editor::get_id(), '1554')
		);
		if (isset($drs[$clase])) {
			return $drs[$clase];			
		} else {
			throw new toba_error("No hay definido un datos_relacion para la clase $clase");
		}
	}	
	
	//---------------------------------------------------
	//-- DATOS TABLA ------------------------------------
	//---------------------------------------------------

	static function get_lista_objetos_dt()
	//Listar objetos que son datos_tabla
	{
		$sql = "SELECT 	proyecto, 
						objeto, 
						'[' || objeto || '] -- ' || nombre as descripcion
				FROM apex_objeto 
				WHERE 	clase = 'objeto_datos_tabla'
				AND		clase_proyecto = 'toba'
				AND 	proyecto = '". contexto_info::get_proyecto() ."'
				ORDER BY 2";
		return contexto_info::get_db()->consultar($sql);
	}
	//---------------------------------------------------

	static function get_lista_dt_columnas($objeto)
	/*
		Lista las columnas de los DATOS_TABLA (supone que el objeto pasado como parametro lo es)
		Esta pregunta hay que hacercela a una clase de dominio (un datos_tabla)
	*/
	{
		$sql = "SELECT 		columna as 	clave,
							columna as 	descripcion,
							col_id  as	col_id
				FROM apex_objeto_db_registros_col 
				WHERE 	objeto = $objeto
				AND 	objeto_proyecto = '". contexto_info::get_proyecto() ."'
				ORDER BY 3";
		return contexto_info::get_db()->consultar($sql);
	}

	//-------------------------------------------------
	//---------------- VARIOS -------------------------
	//-------------------------------------------------

	/**
	* Tipos de pagina
	*/
	function get_tipos_pagina()
	{
		$sql = "SELECT proyecto, pagina_tipo, descripcion 
				FROM apex_pagina_tipo 
				WHERE ( proyecto = 'toba' OR proyecto = '". contexto_info::get_proyecto() ."' )
				ORDER BY 3";
		return contexto_info::get_db()->consultar($sql);
	}

	/**
	* BUFFERs
	*/
	function get_buffers()
	{
		$sql = "SELECT proyecto, buffer, descripcion_corta 
				FROM apex_buffer 
				WHERE ( proyecto = 'toba' OR proyecto = '". contexto_info::get_proyecto() ."' )
				ORDER BY 2";
		return contexto_info::get_db()->consultar($sql);		
	}

	/**
	* Patrones
	*/
	function get_comportamientos()
	{
		$sql = "SELECT proyecto, patron, descripcion_corta FROM apex_patron 
				WHERE patron != 'especifico'
				AND ( proyecto = 'toba' OR proyecto = '". contexto_info::get_proyecto() ."' )
				ORDER BY 3";
		return contexto_info::get_db()->consultar($sql);
	}

	/**
	* Zonas
	*/
	function get_zonas()
	{
		$sql = "SELECT proyecto, zona, nombre
				FROM apex_item_zona
				WHERE ( proyecto = 'toba' OR proyecto = '". contexto_info::get_proyecto() ."' )
				ORDER BY nombre";
		return contexto_info::get_db()->consultar($sql);		
	}			

	/**
	* Tipos de solicitud
	*/
	function get_tipo_observaciones_solicitud()
	{
		$sql = "SELECT proyecto, solicitud_obs_tipo, 
						descripcion 
				FROM apex_solicitud_obs_tipo 
				WHERE ( proyecto = 'toba' OR proyecto = '". contexto_info::get_proyecto() ."' ) ";
		return contexto_info::get_db()->consultar($sql);
	}

	/**
	* Lista de FUENTEs de DATOS
	*/
	function get_fuentes_datos($proyecto=null)
	{
		if (!isset($proyecto)) {
			$proyecto = contexto_info::get_proyecto();
		}
		$sql = "SELECT proyecto, fuente_datos, descripcion_corta  
				FROM apex_fuente_datos
				WHERE ( proyecto = '$proyecto' )
				ORDER BY 2";
		return contexto_info::get_db()->consultar($sql);	
	}

	/**
	* Propiedadesd de una FUENTEs de DATOS
	*/
	function get_info_fuente_datos($id_fuente, $proyecto=null)
	{
		if (!isset($proyecto)) {
			$proyecto = contexto_info::get_proyecto();
		}
		$sql = "SELECT 	*,
						link_instancia 		as link_base_archivo,
						fuente_datos_motor 	as motor,
						host 				as profile
				FROM 	apex_fuente_datos
				WHERE	fuente_datos = '$id_fuente'
				AND 	proyecto = '$proyecto'";
		$rs = contexto_info::get_db()->consultar($sql);
		if (empty($rs)) {
			throw new toba_error("No se puede encontrar la fuente '$id_fuente' en el proyecto '$proyecto'");	
		}
		return $rs[0];
	}
	
	/**
	 * Determina si el proyecto cuenta con una fuente de datos propia
	 */
	static function hay_fuente_definida($proyecto)
	{
		$sql = "SELECT count(*) as cantidad
				FROM 	apex_fuente_datos
				WHERE	proyecto = '$proyecto'";
		$rs = contexto_info::get_db()->consultar($sql);
		return $rs[0]['cantidad'] > 0;
	}

	/**
	*	Consultas PHP declaradas
	*/
	function get_consultas_php()
	{
		$sql = "SELECT proyecto, nucleo, nucleo 
				FROM apex_nucleo
				WHERE ( proyecto = '". contexto_info::get_proyecto() ."' )
				ORDER BY 2 ASC";
		return contexto_info::get_db()->consultar($sql);	
	}

	/**
	*	Lista de Skins
	*/
	function get_lista_skins()
	{
		$sql = "SELECT
					estilo, descripcion
				FROM apex_estilo
				WHERE
						proyecto = '".contexto_info::get_proyecto()."' 
					OR	proyecto = 'toba'
				ORDER BY descripcion";
		return contexto_info::get_db()->consultar($sql);
	}

	function get_lista_estilos_columnas()
	{
		$sql = "SELECT columna_estilo, css FROM apex_columna_estilo";
		return contexto_info::get_db()->consultar($sql);
	}
	
	function get_mensajes($proyecto=null)
	{
		if (!isset($proyecto)) $proyecto = contexto_info::get_proyecto();
		$sql = "SELECT proyecto, msg, indice, msg_tipo as tipo, descripcion_corta
				FROM 	apex_msg
				WHERE proyecto = '$proyecto';";
		return contexto_info::get_db()->consultar($sql);
	}
	
	function get_mensajes_objeto($objeto, $proyecto=null)
	{
		if (!isset($proyecto)) $proyecto = contexto_info::get_proyecto();
		$sql = "SELECT objeto_proyecto, objeto_msg, indice, msg_tipo as tipo, descripcion_corta
				FROM 	apex_objeto_msg
				WHERE 
						objeto_proyecto = '$proyecto'
					AND	objeto = '$objeto'
		";
		return contexto_info::get_db()->consultar($sql);
	}

	function get_puntos_control($proyecto = null)
	{
		if (!isset($proyecto)) $proyecto = contexto_info::get_proyecto();
	      $sql = "SELECT proyecto, pto_control, descripcion
					FROM apex_ptos_control
					WHERE proyecto = '$proyecto'
	      ";
		return contexto_info::get_db()->consultar($sql);
	}

	//-------------------------------------------------
	//---------------- LOGS ---------------------------
	//-------------------------------------------------

	/**
		ATENCION, clase 'toba' hardcodeada en el item del editor...
			Hay que arreglarlo antes de que los proyectos agreguen componentes
	*/
	function get_log_modificacion_componentes()
	{
		$sql = "	SELECT l.momento as momento,
						l.usuario as usuario,	
						'[' || coalesce(CAST(l.objeto as text), '...') || '] ' 
							|| coalesce(o.nombre, i.nombre) as componente_nombre,
						l.objeto_proyecto as componente_proyecto,
						coalesce(CAST(l.objeto as text), l.item) as componente_id,
						coalesce(c.editor_proyecto,'toba_editor') as editor_proyecto, 
						coalesce(c.editor_item,'/admin/items/editor_items') as editor_item,
						coalesce(c.icono,'item.gif') as icono_tipo_componente, 
						l.observacion as observacion
					FROM apex_log_objeto l
					LEFT OUTER JOIN 
						apex_objeto o INNER JOIN apex_clase c ON (o.clase = c.clase AND o.clase_proyecto = c.proyecto)
						ON (o.proyecto = l.objeto_proyecto AND o.objeto = l.objeto)
					LEFT OUTER JOIN
						apex_item i ON (l.item = i.item)
					WHERE objeto_proyecto = '". contexto_info::get_proyecto() ."'
					AND ( (o.objeto IS NOT NULL) OR (i.item IS NOT NULL) ) -- no mostrar eliminados
					ORDER BY momento DESC;";	
		return contexto_info::get_db()->consultar($sql);
	}
}
?>
