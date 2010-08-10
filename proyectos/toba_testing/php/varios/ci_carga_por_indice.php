<?php 

class ci_carga_por_indice extends toba_testing_pers_ci
{
	function ini()
	{
		$this->agregar_dependencia_por_indice('ci','ci_test_identificador');
		$this->agregar_dependencia_por_indice('form','form_test');
	}
	
	function evt__form__test()
	{
		// componente
		$tabla = toba::componente('t_escalafon');
		$tabla->cargar();
		ei_arbol($tabla->get_filas());
		
		// CN
		toba::cn('prueba_indice_cn')->test();
		
	}
	
	function conf()
	{
		$this->pantalla()->agregar_dep('ci');
		$this->pantalla()->agregar_dep('form');
	}
}

?>