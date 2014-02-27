<?php

namespace rest\seguridad\autenticacion;

/**
 * Retorna los api_keys de los usuarios.
 * @package rest\seguridad\autenticacion
 */
interface api_key_usuarios {

    function get_usuario_api_key($api_key);

} 