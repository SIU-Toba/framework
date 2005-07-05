<?php
require_once("test_db_registros.php");
/*
	Multitabla ESTRICTO con clave SIMPLE COMPUESTA.
*/
class test_db_registros_mt_2 extends test_db_registros
{
	function get_sql_tablas()
	{
		$sql[] = "CREATE TEMPORARY TABLE test_maestro (
					  id1 				SMALLINT 		NOT NULL, 
					  id2 				SMALLINT 		NOT NULL,
					  nombre			VARCHAR(20) 	NOT NULL, 
					  descripcion 		VARCHAR(80), 
					  CONSTRAINT test_maestro_pkey PRIMARY KEY(id1, id2)
					);";
		$sql[] = "CREATE TEMPORARY TABLE test_detalle (
					  id1 				SMALLINT 		NOT NULL, 
					  id2 				SMALLINT 		NOT NULL,
					  extra 			VARCHAR(20)		NOT NULL, 
					  CONSTRAINT test_detalle_pkey PRIMARY KEY(id1, id2), 
					  FOREIGN KEY (id1, id2) REFERENCES test_maestro(id1, id2) ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE
					);";	
		return $sql;
	}
	
	function get_sql_eliminar_tablas()
	{
		$sql[] = "DROP TABLE test_detalle;";
		$sql[] = "DROP TABLE test_maestro;";
		return $sql;
	}

	function get_sql_juego_datos()
	{
		$sql[] = "INSERT INTO test_maestro (id1, id2, nombre, descripcion) VALUES ('0','2','Peras','Las peras son ricas.');";
		$sql[] = "INSERT INTO test_maestro (id1, id2, nombre, descripcion) VALUES ('1','2','Naranjas','Las naranjas son ricas.');";
		$sql[] = "INSERT INTO test_maestro (id1, id2, nombre, descripcion) VALUES ('2','2','Mandarinas','Las mandarinas son ricas.');";
		$sql[] = "INSERT INTO test_maestro (id1, id2, nombre, descripcion) VALUES ('3','2','Manzanas','Las manzanas son ricas.');";
		$sql[] = "INSERT INTO test_detalle (id1, id2, extra) VALUES ('0','2','Peras!!');";
		$sql[] = "INSERT INTO test_detalle (id1, id2, extra) VALUES ('1','2','Increibles');";
		$sql[] = "INSERT INTO test_detalle (id1, id2, extra) VALUES ('2','2','Aparecen en el otoño');";
		$sql[] = "INSERT INTO test_detalle (id1, id2, extra) VALUES ('3','2','Vienen de Chipoletti');";
		return $sql;
	}

	function get_sql_eliminar_juego_datos()
	{
		$sql[] = "DELETE FROM test_detalle;";
		$sql[] = "DELETE FROM test_maestro;";
		return $sql;
	}
	
	function get_dbr()
	{
		require_once("test_db_registros_mt_2_dbr.php");
		return new test_db_registros_mt_2_dbr("multi","instancia",0);
	}

	function get_where_test()
	{
		return	array("maestro.id1 IN (0,1,2)");
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
	
	function get_registro_test($concepto)
	//Registros para insertar en las tablas
	{
		static $datos;
		//- Registros validos
		$datos['valido_1']['id1']="10";
		$datos['valido_1']['id2']="40";
		$datos['valido_1']['nombre']="TOMATE";
		$datos['valido_1']['descripcion']="Esta es una cosa";
		$datos['valido_1']['extra']="Cossaaaaa!";

		$datos['valido_2']['id1']="11";
		$datos['valido_2']['id2']="42";
		$datos['valido_2']['nombre']="TOMATE";
		$datos['valido_2']['descripcion']="Este es un Hola";
		$datos['valido_2']['extra']="Hollaaaa!";
		//- Registro invalido (nombre y extra NULL)
		$datos['invalido_null']['id1']="12";
		$datos['invalido_null']['id2']="42";
		$datos['invalido_null']['descripcion']="Este es un Perro";
		//$datos['invalido_null']['nombre']="Hola";

		//- Registro invalido (Estructua incorrecta)
		$datos['invalido_col_inexistente']['id1']="220";
		$datos['invalido_col_inexistente']['id2']="221";
		$datos['invalido_col_inexistente']['descripcion']="Este es un Perro";
		$datos['invalido_col_inexistente']['extra']="Hollaaaa!";
		$datos['invalido_col_inexistente']['columna_invalida']="Todo mal";

		//- Registro invalido para la DB (El ID es un string)
		$datos['invalido_db']['id1']="xxx";
		$datos['invalido_db']['id2']="123";
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