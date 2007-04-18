<?php

ini_set("max_execution_time",0);

class test_exportador extends test_toba
{
	
	function get_descripcion()
	{
		return "Exportador";
	}	

	function test_objetos()
	{
		exportador::objetos_a_sql("toba");
	}	

/*	function test_items()
	{
		exportador::items_a_sql("toba");		
	}*/
}

?>