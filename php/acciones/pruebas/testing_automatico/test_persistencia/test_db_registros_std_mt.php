<?
require_once("test_db_registros_std.php");
/*
	Test de la interface BASICA de un db_registros_mt
	-------------------------------------------------

	- Probar la colision de columnas

*/
class test_db_registros_std_mt extends test_db_registros_std
{

	function get_descripcion()
	{
		return "";
	}	

}
?>