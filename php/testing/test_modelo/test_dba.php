<?php
require_once('modelo/dba.php');

class test_dba extends test_toba 
{
	function get_descripcion()
	{
		return "Test de administración de bases (DBA)";	
	}
	
	function SetUp()
	{
		abrir_transaccion();	
	}
	
	function test_conexion_fuente_inexistente()
	{
		try {
			dba::get_db("Otra");
			$this->fail();
		} catch(excepcion_toba $e) {
			$this->pass();
		}
		
	}

	function test_conexion_base_inexistente()
	{
		global $instancia;		
		try {
			$instancia[apex_pa_instancia];
			$instancia[apex_pa_instancia]['base_anterior'] = $instancia[apex_pa_instancia][apex_db_base];
			$instancia[apex_pa_instancia][apex_db_base] = '_Otra_';
			dba::refrescar("instancia");
			$this->fail();
		} catch(excepcion_toba $e) {
			$this->pass();
		}
		$instancia[apex_pa_instancia][apex_db_base] = $instancia[apex_pa_instancia]['base_anterior'];
		dba::refrescar("instancia");
	}	
	
	function test_existencia_conexion()
	{
		//La instancia esta conectada
		$this->assertTrue(dba::existe_conexion("instancia"));
		//La base "otra" no lo esta
		$this->assertFalse(dba::existe_conexion("otra"));		
	}
	
	function test_existencia_base()
	{
		//La instancia esta conectada
		$this->assertTrue(dba::existe_base_datos("instancia"));
		//La base "otra" no lo esta
		//$this->assertFalse(dba::existe_base_datos("otra"));	
		
	}
}

?>