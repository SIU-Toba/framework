<?php
class test_db_registros extends test_toba
{
	protected $dbr;

	function pre_run()
	{
		ejecutar_sql( $this->get_sql_tablas() );
	}
	
	function post_run()
	{
		ejecutar_sql( $this->get_sql_eliminar_tablas() );
	}
	//----------------------------------------------

	function SetUp()
	{
		ejecutar_sql( $this->get_sql_juego_datos() );
		$this->dbr = $this->get_dbr();
	}

	function TearDown()
	{
		ejecutar_sql( $this->get_sql_eliminar_juego_datos() );
		$this->dbr->resetear();
		unset($this->dbr);
	}
	//----------------------------------------------

	function dump($mensaje="Info")
	{
		ei_arbol($this->dbr->info(true),$mensaje);	
	}

	function dump_definicion_externa($mensaje="definicion EXTERNA")
	{
		ei_arbol($this->dbr->obtener_definicion(),$mensaje);	
	}

	function dump_definicion($mensaje="Info DEFINICION")
	{
		ei_arbol($this->dbr->info_definicion(),$mensaje);	
	}

	function dump_control($mensaje="Estructura CONTROL")
	{
		ei_arbol($this->dbr->get_estructura_control(),$mensaje);	
	}

	function dump_datos($mensaje="Registros")
	{
		ei_arbol($this->dbr->obtener_registros(null, true),$mensaje);	
	}
}
?>