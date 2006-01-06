<?
require_once('comando.php');
require_once('modelo/instancia.php');

class comando_instancia extends comando_toba
{
	static function get_info()
	{
		return 'Administracion de INSTANCIAS. (Contextos de ejecucion independientes dentro de la instalacion)';
	}
	
	/**
	*	Inicializa una instancia
	*/
	function opcion__iniciar()
	{
	}

	/**
	*	Agrega un proyecto en la instancia
	*/
	function opcion__agregar_proyecto()
	{
	}
	
	/**
	*	Elimina un proyecto de la instancia
	*/
	function opcion__eliminar_proyecto()
	{
	}

}
?>