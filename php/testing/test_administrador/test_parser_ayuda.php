<?php
require_once('nucleo/lib/parser_ayuda.php');

class test_parser_ayuda extends test_toba
{

	function get_descripcion()
	{
		return "Parser de Ayuda (Wiki, API)";
	}	

	function test_unico_tag()
	{
		$texto = "Esta es mi [wiki:Referencia/PaginaWiki Página Wiki], por favor [wiki:Bla/bla la revisarla] de nuevo";
		$salida = parser_ayuda::parsear($texto);
	}
}

?>