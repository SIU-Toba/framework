<?php
require_once("test_db_registros.php");
/*
	Multitabla ESTRICTO con clave SIMPLE IDENTICA.
*/
class test_db_registros_s_seq extends test_db_registros
{
	function get_sql_tablas()
	{
		//$sql[] = "DROP SEQUENCE seq_maestro;";
		$sql[] = "CREATE SEQUENCE seq_maestro INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1;";
		$sql[] = "CREATE TEMPORARY TABLE test_maestro (
					  id 				int4			DEFAULT nextval('\"seq_maestro\"'::text)	NOT NULL, 
					  nombre			VARCHAR(20) 	NOT NULL, 
					  descripcion 		VARCHAR(80), 
					  CONSTRAINT test_maestro_pkey PRIMARY KEY(id)
					);";
		return $sql;
	}
	
	function get_sql_eliminar_tablas()
	{
		$sql[] = "DROP SEQUENCE seq_maestro;";
		$sql[] = "DROP TABLE test_maestro;";
		return $sql;
	}

	function get_sql_juego_datos()
	{
		$sql[] = "INSERT INTO test_maestro (nombre, descripcion) VALUES ('Peras','Las peras son ricas.');";
		$sql[] = "INSERT INTO test_maestro (nombre, descripcion) VALUES ('Naranjas','Las naranjas son ricas.');";
		$sql[] = "INSERT INTO test_maestro (nombre, descripcion) VALUES ('Mandarinas','Las mandarinas son ricas.');";
		$sql[] = "INSERT INTO test_maestro (nombre, descripcion) VALUES ('Manzanas','Las manzanas son ricas.');";
		return $sql;
	}

	function get_sql_eliminar_juego_datos()
	{
		$sql[] = "DELETE FROM test_maestro;";
		return $sql;
	}
	
	function get_dbr()
	{
		require_once("test_db_registros_s_seq_dbr.php");
		return new test_db_registros_s_seq_dbr("instancia");
	}

	function get_registro_test($concepto)
	//Registros para insertar en las tablas
	{
		static $datos;
		//- Registros validos
		$datos['valido_1']['nombre']="TOMATE";
		$datos['valido_1']['descripcion']="Esta es una cosa";

		$datos['valido_2']['nombre']="TOMATE";
		$datos['valido_2']['descripcion']="Este es un Hola";

		if(isset($datos[$concepto])){
			return 	$datos[$concepto];
		}else{
			throw new exception_toba("Se solicito un registro inexistente");
		}
	}
	//------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------

	function test_insert()
	{
		$this->dump_definicion();		
	}

}
?>