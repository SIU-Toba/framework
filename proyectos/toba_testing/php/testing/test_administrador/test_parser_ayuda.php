<?php

class test_parser_ayuda extends test_toba
{

	function get_descripcion()
	{
		return "Parser de Ayuda (Wiki, API)";
	}	

	function test_sin_tags()
	{
		$texto = "Esta es mi Página Wiki, por favor hay que revisarla de nuevo";
		$this->assertTrue( toba_parser_ayuda::es_texto_plano($texto));
		
		$salida = toba_parser_ayuda::parsear($texto);
		$esperado = $texto;
		$this->assertEqual($salida, $esperado);
	}

	
	function test_unico_tag_multiples_ocurrencias()
	{
		$texto = "Esta es mi [test:Referencia/PaginaWiki Página Wiki], por favor [test:Bla/bla hay que revisarla] de nuevo";
		$this->assertFalse( toba_parser_ayuda::es_texto_plano($texto));
				
		$salida = toba_parser_ayuda::parsear($texto);
		$esperado = "Esta es mi <test id='Referencia/PaginaWiki'>Página Wiki</test>, por favor <test id='Bla/bla'>hay que revisarla</test> de nuevo";
		$this->assertEqual($salida, $esperado);
	}
	
	function test_solo_el_tag()
	{
		$texto = "[test:Referencia/PaginaWiki Página Wiki]";
		$this->assertFalse( toba_parser_ayuda::es_texto_plano($texto));
				
		$salida = toba_parser_ayuda::parsear($texto);
		$esperado = "<test id='Referencia/PaginaWiki'>Página Wiki</test>";
		$this->assertEqual($salida, $esperado);
	}
	
	function test_tag_sin_texto_posterior()
	{
		$texto = "Hola [test:Referencia/PaginaWiki Página Wiki]";
		$this->assertFalse( toba_parser_ayuda::es_texto_plano($texto));		
		
		$salida = toba_parser_ayuda::parsear($texto);
		$esperado = "Hola <test id='Referencia/PaginaWiki'>Página Wiki</test>";
		$this->assertEqual($salida, $esperado);
	
	}
	
	function test_tag_sin_texto_anterior()
	{
		$texto = "[test:Referencia/PaginaWiki Página Wiki] Hola";
		$this->assertFalse( toba_parser_ayuda::es_texto_plano($texto));
				
		$salida = toba_parser_ayuda::parsear($texto);
		$esperado = "<test id='Referencia/PaginaWiki'>Página Wiki</test> Hola";
		$this->assertEqual($salida, $esperado);
	}
	
	function test_tag_erroneo()
	{
		$texto = "Esta es la [testa:Referencia/PaginaWiki Página Wiki] ";
		$this->assertTrue( toba_parser_ayuda::es_texto_plano($texto));
				
		$salida = toba_parser_ayuda::parsear($texto);
		$esperado = $texto;
		$this->assertEqual($salida, $esperado);
	}

	function test_tag_sin_desc()
	{
		$texto = "[test:Referencia/PaginaWiki]";		
		$this->assertFalse(toba_parser_ayuda::es_texto_plano($texto));
		$salida = toba_parser_ayuda::parsear($texto, true);
		$esperado = "<test>Referencia/PaginaWiki</test>";
		$this->assertEqual($salida, $esperado);		
	}	
	

}

?>