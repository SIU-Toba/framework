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
				$this->fail("La version $version debería ser válida ");	
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
	}
	
	function test_comparacion_versiones()
	{
		$v = new toba_version("0.1.20");
		$this->assertTrue($v->es_menor( new toba_version('0.10.0')));
		$this->assertTrue($v->es_mayor( new toba_version('0.1.19')));
	}
	
	function test_camino_migraciones()
	{
		$desde = new toba_version("0.0.3");
		$hasta = new toba_version("1.0.0");
		$versiones = $desde->get_secuencia_migraciones($hasta, $this->path_migraciones());
		$this->assertEqual(count($versiones), 5);
		$this->assertEqual($versiones[0]->__toString(), "0.1.0");
		$this->assertEqual($versiones[1]->__toString(), "0.1.9");
		$this->assertEqual($versiones[2]->__toString(), "0.1.10");
		$this->assertEqual($versiones[3]->__toString(), "0.10.2");
		$this->assertEqual($versiones[4]->__toString(), "1.0.0");
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
}

?>