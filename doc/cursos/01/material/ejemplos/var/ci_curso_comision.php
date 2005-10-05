<?php
require_once("comunes/ci/ci_Browse_Form_DatosTabla.php");

class ci_curso_comision extends ci_Browse_Form_DatosTabla {
	
	function get_tabla() {
		if(!isset($this->tabla)) {
			$this->tabla = $this->controlador->get_comision();			
		}
		return $this->tabla;		
	}

	function destruir() {
		parent::destruir();
//		ei_arbol($this->get_estado_sesion());
//		ei_arbol($this->get_tabla()->get_datos());		
	}
}
?>
