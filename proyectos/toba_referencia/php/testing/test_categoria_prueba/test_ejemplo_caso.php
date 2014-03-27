<?php

class test_ejemplo_caso extends toba_test
{
	static function get_descripcion()
	{
		return "Ejemplo de caso de test";
	}
	
	function test_algo()
	{
		$this->AssertEqual(1+1, 2);
	}

}

?>