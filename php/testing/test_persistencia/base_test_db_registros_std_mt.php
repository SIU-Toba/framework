<?
require_once("base_test_db_registros_std.php");
/*
	Test de la interface BASICA de un db_registros_mt
	-------------------------------------------------

	- Probar la colision de columnas

*/
class base_test_db_registros_std_mt extends base_test_db_registros_std
{

	function get_descripcion()
	{
		return "";
	}	

}
?>