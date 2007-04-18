<?php

class test_fijos extends test_toba
/*
* Contratos:
*  - El ef_fijo debe mantener su estado en sesión
*/
{
	protected $parametros;
	protected $id_form = 'nombre_formnombre_padreid';
	protected $clase;
	
	function get_descripcion()
	{
		return "EF Fijos";
	}	

	function SetUp()
	{
		$this->clase = 'ef_fijo';
		$this->mentir_hilo();
	}
	
	function TearDown()
	{
		$this->restaurar_hilo();
	}
	
	function get_ef($parametros)
	{
		$padre = array(1=> 'nombre_padre');
		$ef = new $this->clase($padre, 'nombre_form', 'id', 'etiqueta', 'descripcion', 'dato', false, $parametros);
		return $ef;	
	}

	
	function test_fijo_mantiene_estado_sesion()
	{
		$parametros = array();
		$ef = $this->get_ef($parametros);
		$html = "<h1>TITULO</h1>";
		$ef->cargar_estado($html);
		$ef = $this->get_ef($parametros);
		$ef->cargar_estado();
		$this->AssertEqual($ef->obtener_estado(), $html);
	}
	
}


?>