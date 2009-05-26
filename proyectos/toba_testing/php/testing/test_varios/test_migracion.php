<?php

class test_migracion extends test_toba 
{

	function get_descripcion()
	{
		return "Esquema de versiones y migración";
	}	
	
	function chequear_validez($version, $es_valida)
	{
		try {
			$v = new toba_version($version);
			if (! $es_valida) {
				$this->fail("La version $version no debería ser válida");
			} else {
				$this->pass();				
			}
		} catch (toba_error $e) {
			if ($es_valida) {
				$this->fail("La version $version debería ser válida (".$e->getMessage().")");	
			} else {
				$this->pass();
			}
		}

	}
	
	function path_migraciones()
	{
		return dirname(__FILE__)."/migraciones";
	}
	
	function test_version_valida()
	{
		$this->chequear_validez("0.1.2", true);
		$this->chequear_validez("12.14.1222", true);
		$this->chequear_validez("2.0.0", true);
		$this->chequear_validez("2.0.0rc", true);
		$this->chequear_validez("2.0.0rc-1", true);
		$this->chequear_validez("2", false);
		$this->chequear_validez("2.0", false);
		$this->chequear_validez("2.0,12", false);
		$this->chequear_validez("2.0.0rca", false);
		$this->chequear_validez("2.0.0rca-1", false);
		$this->chequear_validez("2.0.0rc-pepa", false);
		$this->chequear_validez("2.0.0 (2550)", true);
		$this->chequear_validez("2.0.0 (alpha-2550)", true);
	}
	
	function test_comparacion_versiones()
	{
		$v = new toba_version("0.1.20");
		$this->assertTrue($v->es_menor( new toba_version('0.10.0')));
		$this->assertTrue($v->es_mayor( new toba_version('0.1.19')));
		$this->assertTrue($v->es_igual( new toba_version('0.1.20')));
		$this->assertTrue($v->es_menor( new toba_version('0.1.20 (2099)')));
		$this->assertTrue($v->es_mayor( new toba_version('0.1.20 (rc)')));
		
		$v = new toba_version("0.1.20 (200)");
		$this->assertTrue($v->es_igual( new toba_version('0.1.20 (200)')));		
		$this->assertTrue($v->es_mayor( new toba_version('0.1.20')));		
		$this->assertTrue($v->es_mayor( new toba_version('0.1.20 (94)')));
		$this->assertTrue($v->es_menor( new toba_version('0.1.20 (1114)')));
		$this->assertTrue($v->es_menor( new toba_version('0.1.21')));
		
		
		$v = new toba_version("0.1.20 (beta-200)");
		$this->assertTrue($v->es_igual( new toba_version('0.1.20 (beta-200)')));		
		$this->assertTrue($v->es_mayor( new toba_version('0.1.20 (alpha-199)')));
		$this->assertTrue($v->es_mayor( new toba_version('0.1.20 (beta-199)')));
		$this->assertTrue($v->es_menor( new toba_version('0.1.20 (rc-210)')));
		$this->assertTrue($v->es_menor( new toba_version('0.1.20 (210)')));
		$this->assertTrue($v->es_menor( new toba_version('0.1.21')));		
	}

	function test_camino_migraciones()
	{
		$desde = new toba_version("0.0.3");
		$hasta = new toba_version("1.0.0");
		$versiones = $desde->get_secuencia_migraciones($hasta, $this->path_migraciones());
		$this->assertEqual(count($versiones), 8);
		$this->assertEqual($versiones[0]->__toString(), "0.1.0");
		$this->assertEqual($versiones[1]->__toString(), "0.1.9");		
		$this->assertEqual($versiones[2]->__toString(), "0.1.9 (2000)");		
		$this->assertEqual($versiones[3]->__toString(), "0.1.9 (2100)");		
		$this->assertEqual($versiones[4]->__toString(), "0.1.9 (2200)");		
		$this->assertEqual($versiones[5]->__toString(), "0.1.10");
		$this->assertEqual($versiones[6]->__toString(), "0.10.2");
		$this->assertEqual($versiones[7]->__toString(), "1.0.0");
	}

	function test_migracion_misma_version()
	{
		$desde = new toba_version("1.0.0");
		$hasta = new toba_version("1.0.0");
		$versiones = $desde->get_secuencia_migraciones($hasta, $this->path_migraciones());		
		$this->assertEqual(count($versiones), 0);
	}
	
	function test_sin_migracion()
	{
		$desde = new toba_version("0.1.10");
		$hasta = new toba_version("0.10.0");
		$versiones = $desde->get_secuencia_migraciones($hasta, $this->path_migraciones());		
		$this->assertEqual(count($versiones), 0);
	}	
	
	function test_rango_builds()
	{
		$desde = new toba_version("0.1.4 (2005)");
		$hasta = new toba_version("0.2.5 (2012)");
		$intermedios = array('2006', '2007', '2008', '2009', '2010', '2011', '2012');		
		$this->assertEqualArray($desde->get_builds_intermedios($hasta), $intermedios);		
	}
	
	function test_rango_builds_inverso()
	{
		$desde = new toba_version("0.1.4 (2005)");
		$hasta = new toba_version("0.2.5 (2012)");
		$intermedios = array('2012', '2011', '2010', '2009', '2008', '2007', '2006');
	
		$this->assertEqualArray($hasta->get_builds_intermedios($desde), $intermedios);		
	}	

}

?>