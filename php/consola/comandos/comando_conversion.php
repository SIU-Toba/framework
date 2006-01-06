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
	*	Muestra el detalle de cambios incluidos en una migración específica
	*/
	function opcion__listar()
	{
	}
	
	/**
	*	Simula la migración de metadatos de un proyecto hacia una versión
	*/
	function opcion__probar()
	{
	}

	/**
	*	Ejecuta la migración de metadatos de un proyecto hacia una versión
	*/
	function opcion__ejecutar()
	{
	}
}
?>