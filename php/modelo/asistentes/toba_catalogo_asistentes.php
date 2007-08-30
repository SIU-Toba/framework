<?php

class toba_catalogo_asistentes
{
	/**
	*	Carga un asistente a partir de un molde de generacion
	* 	@return toba_asistente
	*/
	static function cargar_por_molde($id_molde_proyecto, $id_molde)
	{
		$tipo_molde = self::get_asistente_molde($id_molde_proyecto, $id_molde);
		$datos = toba_cargador::instancia()->get_metadatos_extendidos( array('proyecto'=>$id_molde_proyecto, 
																'componente' => $id_molde),
																$tipo_molde );
		$clase = $datos['molde']['clase'];
		return new $clase($datos);
	}
	
	static function get_asistente_molde($id_molde_proyecto, $id_molde)
	{	
		$sql = "SELECT 	t.clase 			as asistente 
				FROM 	apex_molde_operacion o,
						apex_molde_operacion_tipo t
				WHERE 	o.operacion_tipo = t.operacion_tipo
				AND		proyecto = '$id_molde_proyecto'
				AND		molde = '$id_molde';";
		$temp = consultar_fuente($sql);
		if($temp) {
			return $temp[0]['asistente'];
		} else {
			throw new toba_error('El molde solicitado no existe.');	
		}
	}

	static function get_ci_molde($id_molde_proyecto, $id_molde)
	{	
		$sql = "SELECT 	t.ci 				as ci
				FROM 	apex_molde_operacion o,
						apex_molde_operacion_tipo t
				WHERE 	o.operacion_tipo = t.operacion_tipo
				AND		proyecto = '$id_molde_proyecto'
				AND		molde = '$id_molde';";
		$temp = consultar_fuente($sql);
		if($temp) {
			return $temp[0]['ci'];
		} else {
			throw new toba_error('El molde solicitado no existe.');	
		}
	}

	//------------------------------------------------
	//---- Consultas
	//------------------------------------------------

	static function get_lista_tipo_dato()
	{
		$sql = 'SELECT 
					*
				FROM apex_molde_operacion_tipo_dato
				ORDER BY descripcion_corta
		';		
		return consultar_fuente($sql);
	}
	
	
	//------------------------------------------------
	//---- Reflexión de de las tablas
	//------------------------------------------------	
	
	/**
	 * Dada una tabla retorna los valores por defecto de cada fila para utilizar en un abm
	 */
	static function get_lista_filas_tabla($tabla)
	{
		$nuevas = toba_editor::get_db_defecto()->get_definicion_columnas($tabla);
		$tipo_datos = rs_convertir_asociativo_matriz(self::get_lista_tipo_dato(), array('dt_tipo_dato'));
		$salida = array();
		foreach ($nuevas as $nueva) {
			$fila = array();			
			if (! isset($nueva['fk_tabla'])) {	
				$tipo = isset($tipo_datos[$nueva['tipo']]) ? $nueva['tipo'] : 'C';
				$fila['asistente_tipo_dato'] = $tipo_datos[$tipo]['tipo_dato'];
				$fila['cuadro_estilo'] = $tipo_datos[$tipo]['cuadro_estilo'];
				$fila['cuadro_formato'] = $tipo_datos[$tipo]['cuadro_formato'];
				$fila['en_cuadro'] = ($tipo_datos[$tipo]['cuadro_estilo'] !== '');
				$fila['elemento_formulario'] = $tipo_datos[$tipo]['elemento_formulario'];					
				$fila['dt_tipo_datos'] = $tipo_datos[$tipo]['dt_tipo_dato'];
			} else {
				//--- Es una referencia
				$fila['elemento_formulario'] = 'ef_combo';
				$fila['dt_tipo_datos'] = 'C';
				$fila['asistente_tipo_dato'] = '1000008';
				$fila['cuadro_estilo'] = 1;
				$fila['cuadro_formato'] = 1;
				$fila['en_cuadro'] = 1;
				
				$datos_carga_sql = self::get_opciones_sql_campo_externo($nueva);
				$fila['ef_carga_col_clave'] = $datos_carga_sql['clave'];
				$fila['ef_carga_col_desc'] = $datos_carga_sql['descripcion'];
				$fila['ef_carga_tabla'] = $datos_carga_sql['tabla'];
				$fila['ef_carga_sql'] = $datos_carga_sql['sql'];
			}
	
			$fila['dt_pk'] = $nueva['pk'];
			
			$fila['dt_secuencia'] = $nueva['secuencia'];
			$fila['columna'] = $nueva['nombre'];
			$fila['etiqueta'] = ucwords(str_replace(array('_', '_'), ' ', $nueva['nombre']));
			$fila['en_filtro'] = 0;
			$fila['en_form'] = 1;			
			if ($nueva['secuencia'] != '') {
				$fila['en_form'] = 0;
				$fila['en_cuadro'] = 0;
			}
		
			if ($nueva['pk']) {
				$fila['orden'] = 1;
			}
			if ($nueva['pk'] && $fila['en_form']) {
				$nueva['ef_desactivar_modificacion'] = 1;
			}

			$salida[] = $fila;

		}
		//ei_arbol($salida);
		return $salida;
	}
	
	
	/**
	 * Dada una tabla retorna la SQL de carga de la tabla y sus campos cosméticos
	 * @param string $tabla
	 * @return array(sql, clave)
	 */
	static function get_sql_carga_tabla($tabla)
	{
		$columnas = toba_editor::get_db_defecto()->get_definicion_columnas($tabla);
		$claves = array();
		$select = array();
		$alias = sql_get_alias($tabla);		
		$from = array();
		$aliases = array($alias);
		$where = array();
		$left = array();
		foreach ($columnas as $columna) {
			if ($columna['pk']) {
				$claves[] = $columna['nombre'];	
			}
			//-- Si es clave o no es una referencia se trae el dato puro
			if ($columna['pk']  || !$columna['fk_tabla']) {
				$select[] = $alias.'.'.$columna['nombre'];
			} else {
				//--- Es una referencia, hay que hacer joins
				$externo = self::get_opciones_sql_campo_externo($columna);
				$alias_externo = sql_get_alias( $externo['tabla']);
				if (in_array($alias_externo, $aliases)) {
					$alias_externo = $externo['tabla']; //En caso de existir el alias, usa el nombre de la tabla
				}
				$aliases[] = $alias_externo;				
				$select[] = $alias_externo.'.'.$externo['descripcion'].' as '.$columna['nombre'];				
				$ext_where = $alias.'.'.$columna['nombre'].' = '.$alias_externo.'.'.$externo['clave'];
				$ext_from = $externo['tabla'].' as '.$alias_externo;
				if ($columna['not_null']) {
					//-- Si es NOT NULL, se hace un INNER join
					$from[] = $ext_from;
					$where[] = $ext_where;
				} else {
					//-- Si es NULL, se hace un LEFT OUTER join
					$left[] = "$ext_from ON ($ext_where)";
				}
			}
		}
		$from = array_unique($from);
		$sql = "SELECT\n\t".implode(",\n\t", $select)."\n";
		$sql .= "FROM\n\t$tabla as $alias";
		if (!empty($left)) {
			$texto_left = "\tLEFT OUTER JOIN ";
			$sql .= $texto_left.implode("\n$texto_left",$left)."\n";
		}
		if (!empty($from)) {
			$sql .= ",\n\t".implode(",\n\t",$from)."\n";			
		}
		if (!empty($where)) {
			$sql .= "WHERE\n\t".implode(",\n\t",$where)."\n";
		}
		return array($sql, implode(',',$claves));
	}
	
	/**
	 * Determina la sql,clave y desc de un campo externo de una tabla
	 * Remonta N-niveles de indireccion de FKs
	 */
	static protected function get_opciones_sql_campo_externo($campo)
	{
		//--- Busca cual es el campo descripcion de la tabla destino
		while (isset($campo['fk_tabla'])) {
			$tabla = $campo['fk_tabla'];
			$clave = $campo['fk_campo'];
			$descripcion = $campo['fk_campo'];
			//-- Busca cual es el campo descripción más 'acorde' en la tabla actual
			$campos_tabla_externa = toba_editor::get_db_defecto()->get_definicion_columnas($tabla);
			$encontrado = false;			
			foreach ($campos_tabla_externa as $campo_tabla_ext) {
				//---Detecta cual es la clave para seguir ejecutando el script
				if ($campo_tabla_ext['nombre'] == $clave) {
					$campo = $campo_tabla_ext;
				}
				if (! $encontrado && !$campo_tabla_ext['pk'] && $campo_tabla_ext['tipo'] == 'C') {
					$descripcion = $campo_tabla_ext['nombre'];
					$encontrado = true;
				}
			}
			$sql = "SELECT $clave, $descripcion FROM $tabla";
		}
		return array('sql'=>$sql, 'tabla'=>$tabla, 'clave'=>$clave, 'descripcion'=>$descripcion);
	}
	
}
?>