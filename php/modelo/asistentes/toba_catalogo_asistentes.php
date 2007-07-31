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
	
	/**
	 * Dada una tabla retorna los valroes por defecto de una fila de un abm
	 */
	static function get_lista_filas_tabla($tabla)
	{
		$nuevas = toba_editor::get_db_defecto()->get_definicion_columnas($tabla);		
		$tipo_datos = rs_convertir_asociativo_matriz(self::get_lista_tipo_dato(), array('dt_tipo_dato'));
		$salida = array();
		foreach ($nuevas as $nueva) {
			$tipo = isset($tipo_datos[$nueva['tipo']]) ? $nueva['tipo'] : 'C';
			if (isset($nueva['pk_tabla'])) {	
				$tipo = '';	
			}
			$fila = array();
			$fila['dt_pk'] = $nueva['pk'];
			$fila['dt_largo'] = $nueva['longitud'];			
			$fila['dt_secuencia'] = $nueva['secuencia'];
			$fila['columna'] = $nueva['nombre'];
			$fila['etiqueta'] = ucwords(str_replace(array('_', '_'), ' ', $nueva['nombre']));
			$fila['asistente_tipo_dato'] = $tipo_datos[$tipo]['tipo_dato'];
			$fila['en_filtro'] = 0;
			$fila['en_cuadro'] = ($tipo_datos[$tipo]['cuadro_estilo'] !== '');
			$fila['en_form'] = 1;			
			if ($nueva['secuencia'] != '') {
				$fila['en_form'] = 0;
				$fila['en_cuadro'] = 0;
			}
			$fila['cuadro_estilo'] = $tipo_datos[$tipo]['cuadro_estilo'];
			$fila['cuadro_formato'] = $tipo_datos[$tipo]['cuadro_formato'];
			//$fila['orden'] = $tipo_datos[$tipo]['orden'];			
			if ($nueva['pk']) {
				$fila['orden'] = 1;
			}
			if ($nueva['pk'] && $fila['en_form']) {
				$nueva['ef_desactivar_modificacion'] = 1;
			}
			$salida[] = $fila;
		}
		return $salida;
	}

	
}
?>