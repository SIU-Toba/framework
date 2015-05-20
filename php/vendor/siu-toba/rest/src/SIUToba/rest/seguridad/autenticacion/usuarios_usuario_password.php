<?php

namespace SIUToba\rest\seguridad\autenticacion;

interface usuarios_usuario_password
{
    /**
     * Retorna si el usuario password es valido.
     */
    public function es_valido($user, $pass);
}
