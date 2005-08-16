<?php
require_once("test_db_registros_mt.php");
/*
	TEST de la relacion debil
*/
class test_db_registros_std_mt_3 extends test_db_registros_std_mt
{
	function __construct()
	{
		parent::__construct();
	}
	
	function test_carga()
	{
		$this->dbr->establecer_relacion_debil();
		$this->dbr->cargar_datos();
		$this->assertEqual( $this->dbr->get_cantidad_registros(), 4 );
	}

	//----------------------------------------------------------
	// Modificacion de registros en la relacion DEBIL
	//----------------------------------------------------------

	function test_agregar_registro_dbr_vacio()
	{
		$this->dbr->establecer_relacion_debil();
		$datos = $this->get_tabla_registro(1,0);
		$this->dbr->agregar_registro( $datos );
		$this->assertEqual( $this->dbr->get_cantidad_registros(), 1 );
		$control = $this->dbr->get_estructura_control();
		$this->AssertEqual($control[0]['estado'], "i");
		$this->AssertEqual($control[0]['tablas']['test_db_registros_02'], "i");
	}
	
	function test_agregar_registro()
	{
		$this->dbr->establecer_relacion_debil();
		$this->dbr->cargar_datos();
		$datos = $this->get_tabla_registro(1,0);
		$this->dbr->agregar_registro( $datos );
		$this->assertEqual( $this->dbr->get_cantidad_registros(), 5 );
		$control = $this->dbr->get_estructura_control();
		$this->AssertEqual($control[4]['estado'], "i");
		$this->AssertEqual($control[4]['tablas']['test_db_registros_02'], "i");
	}

	function test_modificar_registro_db_inner()
	{
		$this->dbr->establecer_relacion_debil();
		$this->dbr->cargar_datos_clave(2);
		//$this->dump_control();
		$datos = $this->get_tabla_registro(1,0);
		$this->dbr->modificar_registro( $datos , 0 );
		//$this->dump_control();
		$control = $this->dbr->get_estructura_control();
		$this->AssertEqual($control[0]['estado'], "u");
		$this->AssertEqual($control[0]['tablas']['test_db_registros_02'], "u");
	}

	function test_modificar_registro_db_outer()
	{
		$this->dbr->establecer_relacion_debil();
		$this->dbr->cargar_datos_clave(0);
		//$this->dump_datos();
		//$this->dump_control();
		$this->dbr->modificar_registro( $this->get_tabla_registro(1,0) , 0 );
		$control = $this->dbr->get_estructura_control();
		$this->AssertEqual($control[0]['estado'], "u");
		$this->AssertEqual($control[0]['tablas']['test_db_registros_02'], "i");
	}
}
?>