<?php
require_once("comunes/ci/ci_FiltroCuadro_ABM.php");
require_once("dao.php");

class ci_tematicas extends ci_FiltroCuadro_ABM {
	
///////////// Cuadro //////////////

	// Esto va en la segunda
	function evt__cuadro__carga() {
		if(isset($this->estado_filtro)) {
			return dao::get_tematicas($this->estado_filtro);
		}
		return dao::get_tematicas();
	}	
}
?>