<?php

define('TEST_ID_CAMPO_USUARIO', 'ef_form_33000127_datosusuario');
define('TEST_ID_CAMPO_PWD', 'ef_form_33000127_datosclave');
define('TEST_SUBMIT_LOGIN', 'form_33000127_datos_ingresar');

class test_selenium_autoload 
{
	static function existe_clase($nombre)
	{
		return isset(self::$clases[$nombre]);
	}

	static function cargar($nombre)
	{
		if (self::existe_clase($nombre)) { 
			 require_once(dirname(__FILE__) .'/'. self::$clases[$nombre]); 
		}
	}

	static protected $clases = array(
		'basics_proyecto' => 'basics/basics_proyecto.php',
		'conector_base' => 'basics/conector_base.php',
		'Titiritero' => 'basics/titiritero.php',
		'utilidades_testing' => 'basics/utilidades_testing.php',
		'caso_base' => 'basics/caso_base.php'
		);
}

?>
