<?php
include_once('lib/toba_varios.php');
class test_parseo_etiquetas extends test_toba
{

	function get_descripcion()
	{
		return "Parseo de etiquetas";
	}	

	function test_sin_acceso()
	{
		$etiqueta = "Etiqueta";
		$res = tecla_acceso($etiqueta);
		$this->AssertEqual($res[0], "Etiqueta");
		$this->AssertEqual($res[1], null);	
	}
	
	function test_acceso_mal_cargado()
	{
		$etiqueta = "Etiqueta&";
		$res = tecla_acceso($etiqueta);
		$this->AssertEqual($res[0], "Etiqueta&amp;");
		$this->AssertEqual($res[1], null);	
	}
	
	function test_acceso_con_tags()
	{
		$etiqueta = "< Etiqu&eta";
		$res = tecla_acceso($etiqueta);
		$this->AssertEqual($res[0], "&lt; Etiqu<u>e</u>ta");
		$this->AssertEqual($res[1], 'e');	
	}

	function test_acceso_inicio()
	{
		$etiqueta = "&Etiqueta";
		$res = tecla_acceso($etiqueta);
		$this->AssertEqual($res[0], "<u>E</u>tiqueta");
		$this->AssertEqual($res[1], "E");		
	}

	function test_acceso_medio()
	{
		$etiqueta = "Eti&queta";
		$res = tecla_acceso($etiqueta);
		$this->AssertEqual($res[0], "Eti<u>q</u>ueta");
		$this->AssertEqual($res[1], "q");		
	}	
	
	function test_acceso_final()
	{
		$etiqueta = "Etiquet&a";
		$res = tecla_acceso($etiqueta);
		$this->AssertEqual($res[0], "Etiquet<u>a</u>");
		$this->AssertEqual($res[1], "a");		
	}		
}




?>