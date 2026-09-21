<?php

use SIUToba\rest\seguridad\autenticacion\usuarios_usuario_password;

class toba_usuarios_rest_bd implements usuarios_usuario_password
{
    protected $toba_autenticable;

    public function __construct(\toba_autenticable $toba_autenticable)
    {
        $this->toba_autenticable = $toba_autenticable;
    }


    /**
     * Retorna si el usuario password es valido
     */
    public function es_valido($user, $pass)
    {
        return $this->toba_autenticable->autenticar($user, $pass);
    }
}
