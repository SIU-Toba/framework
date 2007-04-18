<?php

class test_editable extends test_toba
/*
	Ejercita los siguientes contratos:
	- El ef se debe poder cargar desde DAOS
*/
{
	protected $parametros;
	protected $id_form = 'nombre_formnombre_padreid';
	
	function get_descripcion()
	{
		return "EF Editable";
	}	

	function get_ef($parametros)
	{
		$padre = array(1=> 'nombre_padre');
		$ef = new ef_editable($padre, 'nombre_form', 'id', 'etiqueta', 'descripcion', 'dato', false, $parametros);
		return $ef;
	}
	
	function test_carga_desde_dao()
	{
		global $retorno_dao;
		$parametros = array(
						'dao' => 'dao_editable',
						'include' => 'testing/test_ef/prueba_daos.php',
						'clase' => 'prueba_daos'
					  );	
		
		//Conjunto vacio de datos de entrada
		$retorno_dao = "Retorno del DAO";
		$ef = $this->get_ef($parametros);
		$this->assertEqual($ef->obtener_estado(), $retorno_dao);
	}
	

}


?>
