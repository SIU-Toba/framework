<?
require_once('nucleo/lib/toba_editor.php'); //Se necesita para saber el ID del editor
/**
*	Brinda un contexto a las consultas informativas sobre el modelo
*/
class contexto_info
{
	static private $proyecto = null;
	static private $db = null;

	static function set_db($db)
	{
		self::$db = $db;	
	}
	
	static function set_proyecto($proyecto)
	{
		self::$proyecto = $proyecto;
	}
	
	static function get_db()
	{
		if (!isset(self::$db)) {
			throw new toba_error("El contexto no se encuentra inicializado: base indefinida");
		}
		return self::$db;
	}

	static function get_proyecto()
	{
		if (!isset(self::$proyecto)) {
			throw new toba_error("El contexto no se encuentra inicializado: proyecto indefinido");
		}
		return self::$proyecto;
	}
}
?>