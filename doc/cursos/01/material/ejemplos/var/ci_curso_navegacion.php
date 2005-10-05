<?php
require_once("comunes/ci/ci_navegacionMD.php");
require_once("dao.php");

class ci_curso_navegacion extends ci_navegacionMD {
	
	///////////// Cuadro //////////////

	// Esto va en la segunda
	function evt__cuadro__carga() {
		if(isset($this->estado_filtro)) {
			return dao::get_cursos($this->estado_filtro);
		}
		return dao::get_cursos();
	}
}
?>
