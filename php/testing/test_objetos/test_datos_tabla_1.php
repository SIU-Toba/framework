<?php
require_once("base_test_datos_tabla.php");

class test_datos_tabla_1 extends base_test_datos_tabla
{

	function get_descripcion()
	{
		return "OBJETO datos_tabla";
	}	

	function get_dt()
	{
		$dt = new objeto_datos_tabla(array('toba_testing','1427'));
		return $dt;
	}

	function get_sql_tablas()
	{
		$sql[] = "CREATE TEMPORARY TABLE test_1 (
					  id 				SMALLINT 		NOT NULL, 
					  nombre			VARCHAR(20) 	NOT NULL, 
					  descripcion 		VARCHAR(80), 
					  CONSTRAINT test_1_pkey PRIMARY KEY(id)
					);";
		return $sql;
	}
	
	function get_sql_eliminar_tablas()
	{
		$sql[] = "DROP TABLE test_1;";
		return $sql;
	}

	function get_sql_juego_datos()
	{
		$sql[] = "INSERT INTO test_1 (id, nombre, descripcion) VALUES ('0','Peras','Las peras son ricas.');";
		$sql[] = "INSERT INTO test_1 (id, nombre, descripcion) VALUES ('1','Naranjas','Las naranjas son ricas.');";
		$sql[] = "INSERT INTO test_1 (id, nombre, descripcion) VALUES ('2','Mandarinas','Las mandarinas son ricas.');";
		$sql[] = "INSERT INTO test_1 (id, nombre, descripcion) VALUES ('3','Manzanas','Las manzanas son ricas.');";
		return $sql;
	}

	function get_sql_eliminar_juego_datos()
	{
		$sql[] = "DELETE FROM test_1;";
		return $sql;
	}

	function get_fila_test($concepto)
	//Registros para insertar en las tablas
	{
		static $datos;
		//- Registros validos
		$datos['valido_1']['id']="10";
		$datos['valido_1']['nombre']="TOMATE";
		$datos['valido_1']['descripcion']="Esta es una cosa";

		$datos['valido_2']['id']="20";
		$datos['valido_2']['nombre']="TOMATE";
		$datos['valido_2']['descripcion']="Este es un Hola";

		//- Registro invalido (nombre NULL)
		$datos['invalido_null']['id']="450";
		$datos['invalido_null']['descripcion']="Este es un Perro";
		//$datos['invalido_null']['nombre']="Hola";

		//- Registro invalido (Estructua incorrecta)
		$datos['invalido_col_inexistente']['id']="220";
		$datos['invalido_col_inexistente']['nombre']="Hola";
		$datos['invalido_col_inexistente']['descripcion']="Este es un Perro";
		$datos['invalido_col_inexistente']['columna_invalida']="Todo mal";

		//- Registro invalido para la DB (El ID es un string)
		$datos['invalido_db']['id']="xxx";
		$datos['invalido_db']['nombre']="Hola";
		$datos['invalido_db']['descripcion']="Este es un Perro";

		if(isset($datos[$concepto])){
			return 	$datos[$concepto];
		}else{
			throw new exception_toba("Se solicito una FILA inexistente");
		}
	}

	function get_clave_test()
	{
		return array("id");
	}

	function get_where_test()
	{
		return	array("id IN (0,1,2)");
	}

	//——————————————————————————————————————————————————————————————————————————————————————————————
	//—————————————————   LIMITE de lo UTILIZADO  ——————————————————————————————————————————————————
	//——————————————————————————————————————————————————————————————————————————————————————————————
	
	function get_id_registro_test()
	{
		return 1;
	}

	function get_clave_valor_test()
	{
		return array("id"=>1);
	}

	function get_condicion_filtro_test()
	{
		return array("id"=>"0");
	}
	
	function get_constraint_no_duplicado()
	{
		return array("id");	
	}
}
?>