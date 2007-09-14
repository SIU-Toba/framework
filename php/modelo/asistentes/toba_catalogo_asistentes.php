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
	static function get_lista_filas_tabla($tabla, $fuente)
	{
		$db = toba::db($fuente, toba_editor::get_proyecto_cargado());
		$nuevas = $db->get_definicion_columnas($tabla);
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
				
				$datos_carga_sql = $db->get_opciones_sql_campo_externo($nueva);
				$fila['ef_carga_col_clave'] = $datos_carga_sql['clave'];
				$fila['ef_carga_col_desc'] = $datos_carga_sql['descripcion'];
				$fila['ef_carga_tabla'] = $datos_carga_sql['tabla'];
				$fila['ef_carga_sql'] = $datos_carga_sql['sql'];
				
				//Seteo la clase de consultas predefinida
				$consultas_php = toba_info_editores::get_consultas_php();
				if(count($consultas_php)>0) {
					$fila['carga_origen'] = 'consulta_php';
					$fila['carga_php_include'] = $consultas_php[0]['archivo'];
					$fila['carga_php_clase'] = $consultas_php[0]['clase'];
					$fila['carga_php_metodo_nuevo'] = 'get_' . $datos_carga_sql['clave'];
					$fila['carga_php_metodo'] = 'get_' . $datos_carga_sql['clave'];
					//Parches temporales!
					$fila['ef_carga_origen'] = 'consulta_php';
					$fila['ef_carga_php_include'] = $consultas_php[0]['archivo'];
					$fila['ef_carga_php_clase'] = $consultas_php[0]['clase'];
					$fila['ef_carga_php_metodo_nuevo'] = 'get_' . $datos_carga_sql['clave'];
					$fila['ef_carga_php_metodo'] = 'get_' . $datos_carga_sql['clave'];
				}
			}
			//Parches temporales!
			if(isset($tipo)) {
				if($tipo == 'N') {
					$fila['dt_largo'] = 10;
				} else {
					if($tipo!='F') {
						$fila['dt_largo'] = $nueva['longitud'];			
					}
				}
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
	
	

	
}
?>