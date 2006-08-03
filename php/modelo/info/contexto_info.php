<?
require_once('nucleo/lib/editor.php'); //Se necesita para saber el ID del editor
/**
*	Brinda un contexto a las consultas informativas sobre el modelo
*/
class contexto_info
{
	static private $proyecto = null;
	static private $db = null;

	function set_db($db)
	{
		self::$db = $db;	
	}
	
	function set_proyecto($proyecto)
	{
		self::$proyecto = $proyecto;
	}
	
	function get_db()
	{
		if (!isset(self::$db)) {
			throw new excepcion_toba("El contexto no se encuentra inicializado: base indefinida");
		}
		return self::$db;
	}

	function get_proyecto()
	{
		if (!isset(self::$proyecto)) {
			throw new excepcion_toba("El contexto no se encuentra inicializado: proyecto indefinido");
		}
		return self::$proyecto;
	}
}
?>