<?php
/**
 * Esta clase fue y será generada automáticamente. NO EDITAR A MANO.
 * @ignore
 */
class curso_intro_autoload 
{
	static function existe_clase($nombre)
	{
		return isset(self::$clases[$nombre]);
	}

	static function cargar($nombre)
	{
		if (self::existe_clase($nombre)) { require_once(dirname(__FILE__) .'/'. self::$clases[$nombre]); }
	}

	static $clases = array(
	);
}
?>