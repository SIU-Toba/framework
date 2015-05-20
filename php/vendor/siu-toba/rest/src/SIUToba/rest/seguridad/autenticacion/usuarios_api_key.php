<?php

namespace SIUToba\rest\seguridad\autenticacion;

/**
 * Retorna los api_keys de los usuarios.
 */
interface usuarios_api_key
{
    public function get_usuario_api_key($api_key);
}
