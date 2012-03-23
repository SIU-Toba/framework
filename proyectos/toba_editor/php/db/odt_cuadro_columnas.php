<?php

class odt_cuadro_columnas extends toba_datos_tabla
{
	//-----------------------------------------------------
	//--- Manejo de la relacion con los EVENTOS
	//-----------------------------------------------------

	function set_cortes_columna($columna, $eventos)
	{	//Setea las eventos asociadas a una pantalla		
		$cortes = implode(',', $eventos);
		$this->set_fila_columna_valor($columna, 'total_cc', $cortes);
	}
	
	function get_cortes_columna($columna)
	{	//Devuelve las eventos asociadas a una pantalla
		$out = null;
		$cortes = $this->get_fila_columna($columna, 'total_cc');
		if (trim($cortes) != '') {
			$out = array_map('trim', explode(',', $cortes));		
		}
		return $out;
	}

	function eliminar_corte($evento)
	{	//Elimino una evento de todas las pantallas donde este
		$ids = $this->get_id_fila_condicion();
		//Recorro las pantallas
		foreach ($ids as $id) {
			$cortes = $this->get_eventos_pantalla($id);
			if (is_array($cortes)) {
				$cortes = array_flip($cortes);
				if (isset($cortes[$evento])) {
					unset($cortes[$evento]);
					$this->set_eventos_pantalla($id, array_flip($cortes));
				}
			}
		}
	}
	//-----------------------------------------------------
}
?>