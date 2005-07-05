<?php
require_once("test_db_registros_lineal.php");
/*
	PENDIENTE:

		- Modificacion de claves ( $this->dbr->activar_modificacion_clave() )

*/
class test_db_registros_s_1 extends test_db_registros_lineal
{
	function __construct()
	{
		parent::__construct();
		$this->dbr_a_utilizar = "01_s";
	}

	function get_where_test()
	{
		return	array("id IN (0,1,2)");
	}

	function get_tablas()
	{
		$sql[] = "CREATE TEMPORARY TABLE test_db_registros_01 (
					  id 				SMALLINT 		NOT NULL, 
					  nombre			VARCHAR(20) 	NOT NULL, 
					  descripcion 		VARCHAR(80), 
					  CONSTRAINT test_db_registros_01_pkey PRIMARY KEY(id)
					);";
		return $sql;		
	}

	function get_registros()
	{
		$sql[] = "INSERT INTO test_db_registros_01 (id, nombre, descripcion)
				VALUES ('3','Manzanas','Las manzanas son ricas.');";
		$sql[] = "INSERT INTO test_db_registros_01 (id, nombre, descripcion)
				VALUES ('0','Peras','Las peras son ricas.');";
		$sql[] = "INSERT INTO test_db_registros_01 (id, nombre, descripcion)
				VALUES ('2','Mandarinas','Las mandarinas son ricas.');";
		$sql[] = "INSERT INTO test_db_registros_01 (id, nombre, descripcion)
				VALUES ('1','Naranjas','Las naranjas son ricas.');";
		return $sql;
	}


	function get_id()
	{
	
	}
	
	
	function get_registro_test($concepto)
	//Registros para insertar en las tablas
	{
		static $datos;

		$datos['valido_1']['id']="10";
		$datos['valido_1']['nombre']="TOMATE";
		$datos['valido_1']['descripcion']="Esta es una cosa";

		$datos['valido_2']['id']="20";
		$datos['valido_2']['nombre']="TOMATE";
		$datos['valido_2']['descripcion']="Este es un Hola";

		//- Registro invalido (nombre NULL)
		$datos['invalido_null']['id']="20";
		$datos['invalido_null']['descripcion']="Este es un Perro";

		//- Registro invalido (Estructua incorrecta)
		$datos['invalido_col_inexistente']['id']="20";
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
			throw new exception_toba("Se solicito un registro inexistente");
		}
	}
}
?>