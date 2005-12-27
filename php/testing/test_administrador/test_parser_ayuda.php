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
		$texto = "Esta es mi [test:Referencia/PaginaWiki Página Wiki], por favor [test:Bla/bla hay que revisarla] de nuevo";
		$salida = parser_ayuda::parsear($texto);
		$esperado = "Esta es mi <test id='Referencia/PaginaWiki'>Página Wiki</test>, por favor <test id='Bla/bla'>hay que revisarla</test> de nuevo";
		$this->assertEqual($salida, $esperado);
	}
	
}

?>