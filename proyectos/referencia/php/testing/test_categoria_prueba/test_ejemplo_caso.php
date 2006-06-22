<?php

class test_ejemplo_caso extends test_toba
{
	function get_descripcion()
	{
		return "Ejemplo de caso de test";
	}
	
	function test_algo()
	{
		$this->AssertEqual(1+1, 2);
	}

}

?>