<?php

abstract class toba_asistente_1dt extends toba_asistente
{
	//----------------------------------------------------------------------
	//-- Primitivas para la construccion de elementos
	//----------------------------------------------------------------------

	function autocompletar_informacion($refrescar_todo=false)
	{
		$nombre_tabla = $this->dr_molde->tabla('base')->get_columna('tabla');
		$nombre_fuente = $this->dr_molde->tabla('base')->get_columna('fuente');		
		$tabla = $this->dr_molde->tabla('filas');
		if($refrescar_todo) {
			$tabla->eliminar_filas();	
		}
		//--- Recorre las columnas y las rellenas con los nuevos datos
		$actuales =  $tabla->get_filas(null, true);
		$nuevas = toba_catalogo_asistentes::get_lista_filas_tabla($nombre_tabla, $nombre_fuente);
		//-- Borra las filas viejas que ya no estan en la tabla
		foreach ($actuales as $id => $actual) {
			$existe = false;
			foreach ($nuevas as $nueva) {
				if ($nueva['columna'] == $actual['columna']) {
					$existe = true;
					break;	
				}
			}
			if (!$existe) {
				$tabla->eliminar_fila($id);
			}
		}
		//-- Agrega las filas nuevas
		foreach ($nuevas as $nueva) {
			$existe = false;
			foreach ($actuales as $id => $actual) {
				if ($nueva['columna'] == $actual['columna']) {
					$existe = true;
					break;	
				}
			}
			if (!$existe) {
				$tabla->nueva_fila($nueva);
			}
		}
	}
	
	/**
	 * Asume que el dt 'filas' tiene un cursor seteado en la fila actual
	 */
	function autocompletar_carga_combo($columna)
	{
		$nombre_tabla = $this->dr_molde->tabla('base')->get_columna('tabla');
		$nombre_fuente = $this->dr_molde->tabla('base')->get_columna('fuente');			
		$nuevas = toba_catalogo_asistentes::get_lista_filas_tabla($nombre_tabla, $nombre_fuente);
		$datos = array();
		//-- Busca la fila a actualizar
		foreach ($nuevas as $nueva) {
			if ($nueva['columna'] == $columna) {
				$datos['ef_carga_col_clave'] = $nueva['ef_carga_col_clave'];
				$datos['ef_carga_col_desc'] = $nueva['ef_carga_col_desc'];
				$datos['ef_carga_tabla'] = $nueva['ef_carga_tabla'];
				$datos['ef_carga_sql'] = $nueva['ef_carga_sql'];
				break;
			}
		}
		$this->dr_molde->tabla('filas')->set($datos);		
	}
}

?>