<?php
require_once("test_db_registros.php");
/*
	Multitabla ESTRICTO con clave SIMPLE IDENTICA.
*/
class test_db_registros_s_alias extends test_db_registros
{
	function get_sql_tablas()
	{
		$sql[] = "CREATE TEMPORARY TABLE test (
					  id 				int4			NOT NULL, 
					  nombre			VARCHAR(20) 	NOT NULL, 
					  descripcion 		VARCHAR(80), 
					  CONSTRAINT test_pkey PRIMARY KEY(id)
					);";
		$sql[] = "CREATE TEMPORARY TABLE test_asoc (
					  id 				SMALLINT		NOT NULL, 
					  extra 			VARCHAR(20)		NOT NULL, 
					  CONSTRAINT test_asoc_pkey PRIMARY KEY(id), 
					  FOREIGN KEY (id) REFERENCES test(id) ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE
					);";	
		return $sql;
	}
	
	function get_sql_eliminar_tablas()
	{
		$sql[] = "DROP TABLE test_asoc;";
		$sql[] = "DROP TABLE test;";
		return $sql;
	}

	function get_sql_juego_datos()
	{
		$sql[] = "INSERT INTO test (id, nombre, descripcion) VALUES ('0','Peras','Las peras son ricas.');";
		$sql[] = "INSERT INTO test (id, nombre, descripcion) VALUES ('1','Naranjas','Las naranjas son ricas.');";
		$sql[] = "INSERT INTO test (id, nombre, descripcion) VALUES ('2','Mandarinas','Las mandarinas son ricas.');";
		$sql[] = "INSERT INTO test (id, nombre, descripcion) VALUES ('3','Manzanas','Las manzanas son ricas.');";
		$sql[] = "INSERT INTO test_asoc (id, extra) VALUES ('0','pocho');";
		$sql[] = "INSERT INTO test_asoc (id, extra) VALUES ('1','pocho');";
		$sql[] = "INSERT INTO test_asoc (id, extra) VALUES ('2','Aparecen en el otoño');";
		$sql[] = "INSERT INTO test_asoc (id, extra) VALUES ('3','Vienen de Chipoletti');";
		return $sql;
	}

	function get_sql_eliminar_juego_datos()
	{
		$sql[] = "DELETE FROM test_asoc;";
		$sql[] = "DELETE FROM test;";
		return $sql;
	}
	
	function get_dbr()
	{
		require_once("test_db_registros_s_alias_dbr.php");
		return new test_db_registros_s_alias_dbr("instancia");
	}

	function get_registro_test($concepto)
	//Registros para insertar en las tablas
	{
		static $datos;
		//- Registros validos
		$datos['valido_1']['nombre']="TOMATE";
		$datos['valido_1']['descripcion']="Esta es una cosa";
		$datos['valido_1']['extra']="Cossaaaaa!";

		$datos['valido_2']['nombre']="TOMATE";
		$datos['valido_2']['descripcion']="Este es un Hola";
		$datos['valido_2']['extra']="Hollaaaa!";
		if(isset($datos[$concepto])){
			return 	$datos[$concepto];
		}else{
			throw new exception_toba("Se solicito un registro inexistente");
		}
	}
	//------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------

	function test_info()
	{
		//$this->dump_definicion_externa();
		//$this->dump_definicion();		
		//$this->dump_tabla("test_maestro");
	}
	
	function test_carga_registros_where()
	/*
		Carga completa de registros con WHERE
	*/
	{
		$this->dbr->cargar_datos_especificos("pocho");
		$this->AssertEqual($this->dbr->get_cantidad_registros(), 2);
		$control = $this->dbr->get_estructura_control();
		$this->AssertEqual($control[0]['estado'], "db");
		$this->AssertEqual($control[1]['estado'], "db");
	}
}
?>