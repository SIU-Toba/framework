<?
class dao_editores
{
	//---------------------------------------------------
	//---------------- CLASES ---------------------------
	//---------------------------------------------------

	static function get_clases_validas()
	{
		return array(	'objeto_ci',
						'objeto_ei_cuadro',
						'objeto_ei_formulario',
						'objeto_ei_formulario_ml',
						'objeto_ei_filtro',
						'objeto_ei_arbol',
						'objeto_ei_calendario',
						'objeto_ei_archivos',
						'objeto_datos_tabla',
						'objeto_datos_relacion' );
	}

	/*
		Las clases usan un ID concatenado para que las cascadas
		las soporten (actualmente pasan un parametro solo)
	*/
	static function get_lista_clases_toba($todas=false)
	{
		if ($todas)
			$sql_todas = "";
		else
			$sql_todas = "clase IN ('". implode("','",self::get_clases_validas() ) ."') AND";
			
		$sql = "SELECT 	proyecto || ',' || clase as clase, 
						clase as descripcion
				FROM apex_clase 
				WHERE 
					$sql_todas
					(proyecto = '". toba::get_hilo()->obtener_proyecto() ."' OR proyecto='toba')
				ORDER BY 2";
		return consultar_fuente($sql, "instancia");
	}	

	static function get_todas_clases_toba()
	{
		return self::get_lista_clases_toba(true);	
	}
	
	static function get_clase_archivo($proyecto, $clase)
	{
		$sql = "SELECT 	archivo
				FROM apex_clase 
				WHERE 	clase = '$clase'
				AND		proyecto = '$proyecto'";
		$temp = consultar_fuente($sql, "instancia");
		if(is_array($temp)){
			return $temp[0]['archivo'];
		}
	}
	
	static function get_clases_editores()
	{
		$sql = "SELECT
					c.proyecto,
					c.clase,
					c.editor_proyecto,
					c.editor_item,
					c.icono,
					ct.clase_tipo
				FROM 
					apex_clase c,
					apex_clase_tipo ct
				WHERE
					c.clase_tipo = ct.clase_tipo AND 
					c.clase IN ('". implode("','",self::get_clases_validas() ) ."')	AND
						--El proyecto es Toba o el actual
					(c.proyecto = '". toba::get_hilo()->obtener_proyecto() ."' OR c.proyecto = 'toba') AND
					c.editor_item IS NOT NULL
				ORDER BY ct.clase_tipo";
		return consultar_fuente($sql, "instancia");	
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
		return consultar_fuente($sql, "instancia");
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
		$res = consultar_fuente($sql, "instancia");
		return $res[0];
	}

	//---------------------------------------------------
	//---------------- PERMISOS --------------------------
	//---------------------------------------------------
	static function get_grupos_acceso($proyecto)
	{
		$sql = "SELECT proyecto, usuario_grupo_acc, nombre
				FROM apex_usuario_grupo_acc
				WHERE 
					proyecto = '$proyecto'
				ORDER BY nombre
		";
		return consultar_fuente($sql, "instancia");
	}


	//---------------------------------------------------
	//---------------- OBJETOS --------------------------
	//---------------------------------------------------

	static function get_lista_objetos_toba($clase)
	{
		$clase = explode(",",$clase);
		$sql = "SELECT 	proyecto, 
						objeto, 
						'[' || objeto || '] -- ' || nombre as descripcion
				FROM apex_objeto 
				WHERE 	clase = '{$clase[1]}'
				AND		clase_proyecto = '{$clase[0]}'
				AND 	proyecto = '". toba::get_hilo()->obtener_proyecto() ."'
				ORDER BY 2";
		return consultar_fuente($sql, "instancia");
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
		return consultar_fuente($sql, "instancia");
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
				AND 	proyecto = '". toba::get_hilo()->obtener_proyecto() ."'
				ORDER BY 2";
		return consultar_fuente($sql, "instancia");
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
				AND 	objeto_proyecto = '". toba::get_hilo()->obtener_proyecto() ."'
				ORDER BY 3";
		return consultar_fuente($sql, "instancia");
	}
}
?>