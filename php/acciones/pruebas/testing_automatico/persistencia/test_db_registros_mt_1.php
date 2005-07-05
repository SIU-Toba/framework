<?php
require_once("test_db_registros_lineal.php");

class test_db_registros_mt_1 extends test_db_registros_lineal
{
	function __construct()
	{
		parent::__construct();
		$this->dbr_a_utilizar = "01_mt_1";
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