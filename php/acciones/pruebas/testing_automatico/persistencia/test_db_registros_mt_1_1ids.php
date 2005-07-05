<?php
require_once("test_db_registros.php");
/*
	Multitabla estricto con clave de 2 registros.
*/
class test_db_registros_mt_1 extends test_db_registros
{
	function get_sql_tablas()
	{
		$sql[] = "CREATE TEMPORARY TABLE test_db_registros_01 (
					  id 				SMALLINT 		NOT NULL, 
					  nombre			VARCHAR(20) 	NOT NULL, 
					  descripcion 		VARCHAR(80), 
					  CONSTRAINT test_db_registros_01_pkey PRIMARY KEY(id)
					);";
		$sql[] = "CREATE TEMPORARY TABLE test_db_registros_02 (
					  id 				SMALLINT		NOT NULL, 
					  extra 			VARCHAR(20)		NOT NULL, 
					  CONSTRAINT test_db_registros_02_pkey PRIMARY KEY(id), 
					  FOREIGN KEY (id) REFERENCES test_db_registros_01(id) ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE
					);";	
		return $sql;
	}
	
	function get_sql_eliminar_tablas()
	{
		$sql[] = "DROP TABLE test_db_registros_02;";
		$sql[] = "DROP TABLE test_db_registros_01;";
		return $sql;
	}

	function get_sql_juego_datos()
	{
		$sql[] = "INSERT INTO test_db_registros_01 (id, nombre, descripcion) VALUES ('0','Peras','Las peras son ricas.');";
		$sql[] = "INSERT INTO test_db_registros_01 (id, nombre, descripcion) VALUES ('1','Naranjas','Las naranjas son ricas.');";
		$sql[] = "INSERT INTO test_db_registros_01 (id, nombre, descripcion) VALUES ('2','Mandarinas','Las mandarinas son ricas.');";
		$sql[] = "INSERT INTO test_db_registros_01 (id, nombre, descripcion) VALUES ('3','Manzanas','Las manzanas son ricas.');";
		$sql[] = "INSERT INTO test_db_registros_02 (id, extra) VALUES ('0','Peras!!');";
		$sql[] = "INSERT INTO test_db_registros_02 (id, extra) VALUES ('1','Increibles');";
		$sql[] = "INSERT INTO test_db_registros_02 (id, extra) VALUES ('2','Aparecen en el otoño');";
		$sql[] = "INSERT INTO test_db_registros_02 (id, extra) VALUES ('3','Vienen de Chipoletti');";
		return $sql;
	}

	function get_sql_eliminar_juego_datos()
	{
		$sql[] = "DELETE FROM test_db_registros_01;";
		$sql[] = "DELETE FROM test_db_registros_02;";
		return $sql;
	}
	
	function instanciar_dbr()
	{
		require_once("dbr_test_db_registros_01_mt_1.php");
		$this->dbr = new dbr_test_db_registros_01_mt_1("a","instancia",0);
		require_once("dbr_test_db_registros_01.php");
		$this->dbr = new dbr_test_db_registros_01("a","instancia",0);
	}

	function get_where_test()
	{
		return	array("t01.id IN (0,1,2)");
	}

	function get_registro_test($concepto)
	//Registros para insertar en las tablas
	{
		static $datos;
		$datos['valido_1']['id']="10";
		$datos['valido_1']['nombre']="TOMATE";
		$datos['valido_1']['descripcion']="Esta es una cosa";
		$datos['valido_1']['extra']="Cossaaaaa!";

		$datos['valido_2']['id']="20";
		$datos['valido_2']['nombre']="TOMATE";
		$datos['valido_2']['descripcion']="Este es un Hola";
		$datos['valido_2']['extra']="Hollaaaa!";

		//- Registro invalido (nombre y extra NULL)
		$datos['invalido_null']['id']="450";
		//$datos['invalido_null']['nombre']="Hola";
		$datos['invalido_null']['descripcion']="Este es un Perro";

		//- Registro invalido (Estructua incorrecta)
		$datos['invalido_col_inexistente']['id']="220";
		$datos['invalido_col_inexistente']['nombre']="Hola";
		$datos['invalido_col_inexistente']['descripcion']="Este es un Perro";
		$datos['invalido_col_inexistente']['extra']="Hollaaaa!";
		$datos['invalido_col_inexistente']['columna_invalida']="Todo mal";

		//- Registro invalido para la DB (El ID es un string)
		$datos['invalido_db']['id']="xxx";
		$datos['invalido_db']['nombre']="Hola";
		$datos['invalido_db']['descripcion']="Este es un Perro";
		$datos['invalido_db']['extra']="Hollaaaa!";

		if(isset($datos[$concepto])){
			return 	$datos[$concepto];
		}else{
			throw new exception_toba("Se solicito un registro inexistente");
		}
	}
}
?>