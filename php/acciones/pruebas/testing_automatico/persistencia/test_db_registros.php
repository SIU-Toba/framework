<?php
class test_db_registros extends test_toba
{
	protected $dbr;

	function SetUp()
	{
		//abrir_transaccion();
		$this->crear_tablas();
		$this->cargar_tablas();
	}

	function TearDown()
	{
		$this->eliminar_tablas();	
		//abortar_transaccion();
	}

	//----------------------------------------------
	//--  db_registros  ----------------------------
	//----------------------------------------------

	function cargar_dbr_01_s()
	{
		require_once("dbr_test_db_registros_01.php");
		$this->dbr = new dbr_test_db_registros_01("a","instancia",0);
	}
	
	function cargar_dbr_02_s()
	{
		require_once("dbr_test_db_registros_02.php");
		$this->dbr = new dbr_test_db_registros_02("a","instancia",0);
	}

	function cargar_dbr_01_mt()
	{
		require_once("dbr_test_db_registros_01_mt.php");
		$this->dbr = new dbr_test_db_registros_01_mt("a","instancia",0);
	}

	function descargar_dbr()
	{
		$this->dbr->resetear();
		unset($this->dbr);	
	}
	
	//----------------------------------------------
	//--  Base de Datos  ---------------------------
	//----------------------------------------------

	function crear_tablas()
	{
		//Tabla principal
		$sql[] = "CREATE TABLE test_db_registros_01 (
					  id 				SMALLINT 		NOT NULL, 
					  nombre			VARCHAR(20) 	NOT NULL, 
					  descripcion 		VARCHAR(80), 
					  CONSTRAINT test_db_registros_01_pkey PRIMARY KEY(id)
					);";
		//Extension
		$sql[] = "CREATE TABLE test_db_registros_02 (
					  id SMALLINT NOT NULL, 
					  extra VARCHAR(20) NOT NULL, 
					  CONSTRAINT test_db_registros_02_pkey PRIMARY KEY(id), 
					  FOREIGN KEY (id)
					    REFERENCES test_db_registros_01(id)
					    ON DELETE NO ACTION
					    ON UPDATE NO ACTION
					    NOT DEFERRABLE
					);";	
		ejecutar_sql($sql);
	}

	function cargar_tablas()
	{
		//-- Tabla 01
		$sql[] = "INSERT INTO test_db_registros_01 (id, nombre, descripcion)
				VALUES ('3','Manzanas','Las manzanas son ricas.');";
		$sql[] = "INSERT INTO test_db_registros_01 (id, nombre, descripcion)
				VALUES ('0','Peras','Las peras son ricas.');";
		$sql[] = "INSERT INTO test_db_registros_01 (id, nombre, descripcion)
				VALUES ('2','Mandarinas','Las mandarinas son ricas.');";
		$sql[] = "INSERT INTO test_db_registros_01 (id, nombre, descripcion)
				VALUES ('1','Naranjas','Las naranjas son ricas.');";
		//-- Tabla 02
		$sql[] = "INSERT INTO test_db_registros_02 (id, extra)
					VALUES ('3','Vienen de Chipoletti');";
		$sql[] = "INSERT INTO test_db_registros_02 (id, extra)
					VALUES ('2','Aparecen en el otoño');";
		ejecutar_sql($sql);
	}

	function eliminar_tablas()
	{
		$sql[] = "DROP TABLE test_db_registros_02;";
		$sql[] = "DROP TABLE test_db_registros_01;";
		ejecutar_sql($sql);
	}
}
/*
	PLAN
	-----

	- carga
	- obtenecion de registros
	- obtencion de registros filtrados

	- deteccion de errores de campos no nulos
	- deteccion de errores de campos incorrectos

	- modificadores del estado (actualizacion y control)
		- alta 
		- baja
		- modificacion

	- columnas cosmeticas

	- sincronizacion con la base
		- alta
		- baja
		- modificacion

	dbr_mt
	
		- relacion estricta
		- relacion debil

*/
?>