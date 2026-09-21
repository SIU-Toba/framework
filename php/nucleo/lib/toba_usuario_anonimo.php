<?php

/**
 * Usuario especial que se usa para el acceso anónimo al sistema
 * @package Seguridad
 * @subpackage TiposUsuario
 */
class toba_usuario_anonimo extends toba_usuario
{
    public function get_id()
    {
        return toba::proyecto()->get_parametro('usuario_anonimo');
    }

    public function get_nombre()
    {
        return toba::proyecto()->get_parametro('usuario_anonimo_desc');
    }

    public function get_perfiles_funcionales()
    {
        return toba::proyecto()->get_perfiles_funcionales_usuario_anonimo();
    }

    public function get_perfiles_datos()
    {
        return array();
    }

    public function verificar_segundo_factor($clave)
    {
        throw \Exception('Metodo no implementado en toba_usuario_anonimo');
    }

    public function requiere_segundo_factor()
    {
        return true;
    }
}
