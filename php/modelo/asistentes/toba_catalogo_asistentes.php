<?php

class toba_catalogo_asistentes
{
	/**
	*	Carga un asistente a partir de un molde de generacion
	* 	@return toba_asistente
	*/
	static function cargar_por_molde($id_molde_proyecto, $id_molde, $dr_molde)
	{
		$tipo_molde = self::get_asistente_molde($id_molde_proyecto, $id_molde);
		$datos = toba_cargador::instancia()->get_metadatos_extendidos( array('proyecto'=>$id_molde_proyecto, 
																'componente' => $id_molde),
																$tipo_molde );
		$clase = $datos['molde']['clase'];
		return new $clase($datos, $dr_molde);
	}
	
	static function cargar_por_tipo_operacion($tipo_operacion, $dr_molde)
	{
		$clase = self::get_clase_asistente($tipo_operacion);
		return new $clase(null, $dr_molde);
	}
	
	static function get_asistente_molde($id_molde_proyecto, $id_molde)
	{
		$id_molde = quote($id_molde);
		$id_molde_proyecto = quote($id_molde_proyecto);
		$sql = "SELECT 	t.clase 			as asistente
				FROM 	apex_molde_operacion o,
						apex_molde_operacion_tipo t
				WHERE 	o.operacion_tipo = t.operacion_tipo
				AND		proyecto = $id_molde_proyecto
				AND		molde = $id_molde;";
		$temp = consultar_fuente($sql);
		if($temp) {
			return $temp[0]['asistente'];
		} else {
			throw new toba_error('El molde solicitado no existe.');	
		}
	}

	static function get_ci_molde($id_molde_proyecto, $id_molde)
	{
		$id_molde = quote($id_molde);
		$id_molde_proyecto = quote($id_molde_proyecto);
		$sql = "SELECT 	t.ci 				as ci
				FROM 	apex_molde_operacion o,
						apex_molde_operacion_tipo t
				WHERE 	o.operacion_tipo = t.operacion_tipo
				AND		proyecto = $id_molde_proyecto
				AND		molde = $id_molde;";
		$temp = consultar_fuente($sql);
		if($temp) {
			return $temp[0]['ci'];
		} else {
			throw new toba_error('El molde solicitado no existe.');	
		}
	}
	
	static function get_clase_asistente($tipo_operacion)
	{
		$tipo_operacion = quote($tipo_operacion);
		$sql = "SELECT 	t.clase
				FROM apex_molde_operacion_tipo t
				WHERE 	t.operacion_tipo = $tipo_operacion";
		$temp = consultar_fuente($sql);
		if($temp) {
			return $temp[0]['clase'];
		} else {
			throw new toba_error('El tipo de operación no existe');	
		}		
	}

	//------------------------------------------------
	//---- Consultas
	//------------------------------------------------

	static function get_lista_tipo_dato($mapeo_obligatorio=false)
	{
		$where = '';
		if ($mapeo_obligatorio) {
			$where = 'WHERE dt_tipo_dato IS NOT NULL';
		}
		$sql = "SELECT 
					tipo_dato,
  					descripcion_corta,
  					descripcion,
  					dt_tipo_dato,
  					elemento_formulario,
					cuadro_estilo,
  					cuadro_formato,
  					filtro_operador
				FROM apex_molde_operacion_tipo_dato
					$where
				ORDER BY descripcion_corta
		";
		return toba_contexto_info::get_db()->consultar($sql);
	}
	
	static function tipo_dato_referencia()
	{
		return '1000008';
	}
	
	static function tipo_dato_caracter()
	{
		return '1000001';
	}	
	
	//------------------------------------------------
	//---- Reflexión de de las tablas
	//------------------------------------------------	
	
	static function get_campo_opciones_para_cuadro($tipo)
	{
		$mapeo_tipos = rs_convertir_asociativo(self::get_lista_tipo_dato(true), array('dt_tipo_dato'), 'tipo_dato');
		$tipo_base = isset($mapeo_tipos[$tipo]) ? $mapeo_tipos[$tipo] : self::tipo_dato_caracter();
		$datos = self::get_fila_opciones_de_tipo($tipo_base);
		$salida = array();
		$salida['estilo'] = $datos['cuadro_estilo'];
		$salida['formato'] = $datos['cuadro_formato'];
		return $salida;
	}
	
	/**
	 * Dado un tipo de dato del asistente retorna las opciones asociadas
	 */
	static function get_fila_opciones_de_tipo($tipo)
	{
		$tipos = self::get_lista_tipo_dato();
		$tipo_datos = rs_convertir_asociativo_matriz(self::get_lista_tipo_dato(true), array('tipo_dato'));
		if ($tipo != self::tipo_dato_referencia()) {
			$fila = $tipo_datos[$tipo];
			$fila['asistente_tipo_dato'] = $tipo;
		} else {
			//-- El tipo referencia es especial
			$fila['elemento_formulario'] = 'ef_combo';
			$fila['dt_tipo_dato'] = 'C';
			$fila['asistente_tipo_dato'] = self::tipo_dato_referencia();
			$fila['cuadro_estilo'] = 4;
			$fila['cuadro_formato'] = 1;
			$fila['filtro_operador'] = '=';
		}
		return $fila;
	}
	
	/**
	 * Dada una tabla retorna los valores por defecto de cada fila para utilizar en un abm
	 */
	static function get_lista_filas_tabla($tabla, $fuente)
	{
		$db = toba::db($fuente, toba_editor::get_proyecto_cargado());
		$nuevas = $db->get_definicion_columnas($tabla);
		//-- Se busca un mapeo entre el tipo en la base y el tipo en el asistente
		$mapeo_tipos = rs_convertir_asociativo(self::get_lista_tipo_dato(true), array('dt_tipo_dato'), 'tipo_dato');
		$salida = array();
		foreach ($nuevas as $nueva) {
			$fila = array();
			if (! isset($nueva['fk_tabla'])) {				
				$tipo = isset($mapeo_tipos[$nueva['tipo']]) ? $mapeo_tipos[$nueva['tipo']] : self::tipo_dato_caracter();
				$fila = self::get_fila_opciones_de_tipo($tipo);
			} else {
				$tipo = self::tipo_dato_referencia();
				$fila = self::get_fila_opciones_de_tipo($tipo);
				$datos_carga_sql = $db->get_opciones_sql_campo_externo($nueva);
				$fila['ef_carga_col_clave'] = $datos_carga_sql['clave'];
				$fila['ef_carga_col_desc'] = $datos_carga_sql['descripcion'];
				$fila['ef_carga_tabla'] = $datos_carga_sql['tabla'];
				$fila['ef_carga_sql'] = $datos_carga_sql['sql'];
				$fila['ef_carga_origen'] = 'datos_tabla';				
			}
			$fila['dt_pk'] = $nueva['pk'];
			$fila['dt_secuencia'] = $nueva['secuencia'];
			$fila['columna'] = $nueva['nombre'];
			$fila['etiqueta'] = ucwords(str_replace(array('_', '_'), ' ', $nueva['nombre']));
			$fila['en_filtro'] = 0;
			$fila['en_form'] = 1;			
			$fila['en_cuadro'] = 1;			
			if ($nueva['secuencia'] != '') {
				$fila['en_form'] = 0;
				$fila['en_cuadro'] = 0;
			}
			$fila['ef_obligatorio'] = $fila['en_form'] && ($nueva['pk'] || $nueva['not_null']);			
			if ($nueva['pk'] && $fila['en_form']) {
				$nueva['ef_desactivar_modificacion'] = 1;
			}
			$salida[] = $fila;
		}
		return $salida;
	}
	
	
}
?>