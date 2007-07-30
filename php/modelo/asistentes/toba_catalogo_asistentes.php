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
		';		
		return consultar_fuente($sql);
	}
	
	/**
	 * Dada una tabla retorna los valroes por defecto de una fila de un abm
	 */
	static function get_lista_filas_tabla($tabla)
	{
		$nuevas = toba_editor::get_db_defecto()->get_definicion_columnas($tabla);		
		$tipo_datos = rs_convertir_asociativo_matriz(self::get_lista_tipo_dato(), array('dt_tipo_dato'));
		$salida = array();
		foreach ($nuevas as $nueva) {
			$fila = array();
			$fila['dt_pk'] = $nueva['pk'];
			$fila['columna'] = $nueva['nombre'];
			$fila['etiqueta'] = ucwords(str_replace(array('_', '_'), ' ', $nueva['nombre']));
			$tipo = isset($tipo_datos[$nueva['tipo']]) ? $nueva['tipo'] : 'C';
			$fila['asistente_tipo_dato'] = $tipo_datos[$tipo]['tipo_dato'];
			$salida[] = $fila;
		}
		return $salida;
	}

	
}
?>