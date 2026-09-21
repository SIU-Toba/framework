<?php

define('TEST_ID_CAMPO_USUARIO', 'ef_form_46000003_datosusuario');
define('TEST_ID_CAMPO_PWD', 'ef_form_46000003_datosclave');
define('TEST_SUBMIT_LOGIN', 'form_46000003_datos_ingresar');

class test_selenium_autoload
{
    public static function existe_clase($nombre)
    {
        return isset(self::$clases[$nombre]);
    }

    public static function cargar($nombre)
    {
        if (self::existe_clase($nombre)) {
            require_once(dirname(__FILE__) .'/'. self::$clases[$nombre]);
        }
    }

    protected static $clases = array(
        'caso_base' => 'basics/caso_base.php'
    );
}
