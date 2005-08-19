<?php
require_once("base_test_db_registros_std_s.php");
/*
	DB_REGISTRO SIMPLE con clave COMPUESTA.
*/
class test_db_registros_std_s_2 extends base_test_db_registros_std_s
{

	function get_descripcion()
	{
		return "DBR Estandard -- db_registros_s -[ 2 ]- (clave multiple)";
	}	

	function get_sql_tablas()
	{
		$sql[] = "CREATE TEMPORARY TABLE test_maestro (
					  id1 				SMALLINT 		NOT NULL, 
					  id2 				SMALLINT 		NOT NULL, 
					  nombre			VARCHAR(20) 	NOT NULL, 
					  descripcion 		VARCHAR(80)		NULL,
					  CONSTRAINT test_maestro_pkey PRIMARY KEY(id1, id2)
					);";
		return $sql;
	}
	
	function get_sql_eliminar_tablas()
	{
		$sql[] = "DROP TABLE test_maestro;";
		return $sql;
	}

	function get_sql_juego_datos()
	{
		$sql[] = "INSERT INTO test_maestro (id1, id2, nombre, descripcion) VALUES ('0','2','Peras','Las peras son ricas.');";
		$sql[] = "INSERT INTO test_maestro (id1, id2, nombre, descripcion) VALUES ('1','2','Naranjas','Las naranjas son ricas.');";
		$sql[] = "INSERT INTO test_maestro (id1, id2, nombre, descripcion) VALUES ('2','2','Mandarinas','Las mandarinas son ricas.');";
		$sql[] = "INSERT INTO test_maestro (id1, id2, nombre, descripcion) VALUES ('3','2','Manzanas','Las manzanas son ricas.');";
		return $sql;
	}

	function get_sql_eliminar_juego_datos()
	{
		$sql[] = "DELETE FROM test_maestro;";
		return $sql;
	}
	
	function get_dbr()
	{
		require_once("mock_db_registros_std_s_2_dbr.php");
		return new mock_db_registros_std_s_2_dbr("instancia");
	}

	function get_where_test()
	{
		return	array("id1 IN (0,1,2)");
	}
	
	function get_clave_test()
	{
		return array("id1","id2");
	}

	function get_id_registro_test()
	{
		return 	array("id1"=>1, "id2"=>2);
	}

	function get_clave_valor_test()
	{
		return 	array("id1"=>1, "id2"=>2);
	}

	function get_condicion_filtro_test()
	{
		return 	array("id1"=>0, "id2"=>2);
	}

	function get_constraint_no_duplicado()
	{
		return array("id1","id2");	
	}

	function get_registro_test($concepto)
	//Registros para insertar en las tablas
	{
		static $datos;
		//- Registros validos
		$datos['valido_1']['id1']="10";
		$datos['valido_1']['id2']="40";
		$datos['valido_1']['nombre']="TOMATE";
		$datos['valido_1']['descripcion']="Esta es una cosa";

		$datos['valido_2']['id1']="11";
		$datos['valido_2']['id2']="42";
		$datos['valido_2']['nombre']="TOMATE";
		$datos['valido_2']['descripcion']="Este es un Hola";

		//- Registro invalido (nombre NULL)
		$datos['invalido_null']['id1']="12";
		$datos['invalido_null']['id2']="42";
		$datos['invalido_null']['descripcion']="Este es un Perro";
		//$datos['invalido_null']['nombre']="Hola";

		//- Registro invalido (Estructua incorrecta)
		$datos['invalido_col_inexistente']['id1']="220";
		$datos['invalido_col_inexistente']['id2']="221";
		$datos['invalido_col_inexistente']['nombre']="Hola";
		$datos['invalido_col_inexistente']['descripcion']="Este es un Perro";
		$datos['invalido_col_inexistente']['columna_invalida']="Todo mal";

		//- Registro invalido para la DB (El ID es un string)
		$datos['invalido_db']['id1']="xxx";
		$datos['invalido_db']['id2']="123";
		$datos['invalido_db']['nombre']="Hola";
		$datos['invalido_db']['descripcion']="Este es un Perro";

		if(isset($datos[$concepto])){
			return 	$datos[$concepto];
		}else{
			throw new exception_toba("Se solicito un registro inexistente");
		}
	}
}
?>