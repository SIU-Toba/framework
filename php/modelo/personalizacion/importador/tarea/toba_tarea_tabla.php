<?php

class toba_tarea_tabla extends toba_tarea_pers {
	
	protected function armar_datos()
	{
		$this->datos =  new toba_tarea_datos();
		$tag_tabla = toba_pers_xml_elementos::tabla;

		$nombre_tabla = (string) $this->raw_data[toba_pers_xml_atributos::id];
		foreach ($this->raw_data as $registro) {
			$reg = toba_registro_xml_factory::construir($this->db, $nombre_tabla, $registro);
			$this->datos->add_registro($reg);
		}
	}
}
?>
