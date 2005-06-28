<?php
require_once("test_db_registros.php");

class test_db_registros_mt extends test_db_registros
{
	function __construct()
	{
		parent::__construct();
		$this->dbr_a_utilizar = "01_mt";
	}
	
	function test_carga_estricta()
	{
		$this->dbr->establecer_relacion_estricta();
		$this->dbr->cargar_datos();
		$this->assertEqual( $this->dbr->cantidad_registros(), 2 );
	}

	function test_carga_debil()
	{
		$this->dbr->establecer_relacion_debil();
		$this->dbr->cargar_datos();
		$this->assertEqual( $this->dbr->cantidad_registros(), 4 );
	}

}
?>