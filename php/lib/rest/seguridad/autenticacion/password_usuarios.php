<?php

namespace rest\seguridad\autenticacion;

/**
 * Retorna los passswords de los usuarios. Esta clase está pensada para http basic y digest.
 * @package rest\seguridad\autenticacion
 */
interface password_usuarios {

    /**
     * Dado el username, retorna el password para ser comparado.
     * @param $usuario
     * @return mixed string\null. El password o NULL si el usuario no existe
     */
    function get_password($usuario);

} 