<?
require_once('comando.php');

class comando_conversion extends comando_toba
{
	static function get_info()
	{
		return 'Administracion de cambios entre versiones';
	}
	
	/**
	*	Lista las posibles migraciones a aplicar
	*/
	function opcion__info()
	{
	}

	/**
	*	Muestra el detalle de cambios incluidos en una migraci�n espec�fica
	*/
	function opcion__listar()
	{
	}
	
	/**
	*	Simula la migraci�n de metadatos de un proyecto hacia una versi�n
	*/
	function opcion__probar()
	{
	}

	/**
	*	Ejecuta la migraci�n de metadatos de un proyecto hacia una versi�n
	*/
	function opcion__ejecutar()
	{
	}
}
?>