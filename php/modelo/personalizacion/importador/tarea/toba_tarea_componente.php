<?php

class toba_tarea_componente extends toba_tarea_pers
{
	protected function armar_datos()
	{
		$this->datos =  new toba_tarea_datos();
		$tag_componente = toba_pers_xml_elementos::componente;
		$this->descripcion_actual = (string) $this->raw_data->{$tag_componente}[toba_pers_xml_atributos::descripcion];
				
		$tag_tabla = toba_pers_xml_elementos::tabla;		
		foreach ($this->raw_data->$tag_tabla as $tabla) {
			$nombre_tabla = (string) $tabla[toba_pers_xml_atributos::nombre];
			foreach ($tabla as $registro) {
				$reg = toba_registro_xml_factory::construir($this->db, $nombre_tabla, $registro);
				$this->datos->add_registro($reg);
			}
		}
	}
}
?>
