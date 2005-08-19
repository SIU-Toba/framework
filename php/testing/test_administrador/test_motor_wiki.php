<?php
require_once('nucleo/lib/motor_wiki.php');

class test_motor_wiki extends test_toba
{

	function get_descripcion()
	{
		return "Motor Wiki";
	}	

	function test_separacion_pagina_descripcion()
	{
		$texto = "Esta es mi [wiki:Referencia/PaginaWiki Página Wiki], por favor revisarla";
		list($link, $descripcion) = motor_wiki::link_wiki($texto);
		$this->AssertEqual($link, "http://toba.siu.edu.ar/trac/wiki/Referencia/PaginaWiki");
		$this->AssertEqual($descripcion, "Página Wiki");
	}
}

?>