<?php

/**
 * Driver de conexión via ODBC
 * @package Fuentes
 * @subpackage Drivers
 */
class toba_db_odbc extends toba_db
{
    public function __construct($profile, $usuario, $clave, $base, $puerto)
    {
        $this->motor = "odbc";
        parent::__construct($profile, $usuario, $clave, $base, $puerto);
    }

    /**
     * Retorna una ER para quitar comentarios de la SQL
     */
    public function get_separador_comentarios()
    {
        return "/\/\*([^'|\"])*?\*\/|(?:-{2,}[^'|\"]*?\R)/im";
    }
}
