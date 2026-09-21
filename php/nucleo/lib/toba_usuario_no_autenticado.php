<?php

/**
 * Usuario especial que se usa para el acceso público al sistema
 * @package Seguridad
 * @subpackage TiposUsuario
 */
class toba_usuario_no_autenticado extends toba_usuario
{
    public const NO_AUTENTICADO = 'no_autentificado';

    public function __construct()
    {
        parent::__construct(self::NO_AUTENTICADO);
    }

    /**
    *	Retorna el identificador del usuario
    */
    public function get_id()
    {
        return self::NO_AUTENTICADO;
    }

    /**
    *	Retorna el nombre del usuario
    */
    public function get_nombre()
    {
        return 'Usuario no autentificado';
    }

    public function get_perfiles_datos()
    {
        return array();
    }

    public function requiere_segundo_factor()
    {
        return false;
    }
}
