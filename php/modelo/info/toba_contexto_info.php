<?php

/**
*	Brinda un contexto a las consultas informativas sobre el modelo
*/
class toba_contexto_info
{
    private static $proyecto = null;
    private static $db = null;

    public static function set_db($db)
    {
        self::$db = $db;
    }

    public static function set_proyecto($proyecto)
    {
        self::$proyecto = $proyecto;
    }

    /**
     * @return toba_db
     */
    public static function get_db()
    {
        if (!isset(self::$db)) {
            throw new toba_error("El contexto no se encuentra inicializado: base indefinida");
        }
        return self::$db;
    }

    public static function get_proyecto()
    {
        if (!isset(self::$proyecto)) {
            throw new toba_error("El contexto no se encuentra inicializado: proyecto indefinido");
        }
        return self::$proyecto;
    }
}
