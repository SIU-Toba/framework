<?php
use SIUToba\rest\seguridad\autenticacion\usuarios_usuario_password;

/**
 * Created by IntelliJ IDEA.
 * User: andres
 * Date: 24/05/16
 * Time: 18:34
 */
class toba_usuarios_rest_promiscuo implements usuarios_usuario_password
{
    public function get_password($usuario)	{
        if (getenv('API_DOOMSDAY_PASS') !== false) {
            return getenv('API_DOOMSDAY_PASS');						//IF YOU SET THIS UP, EAT YOUR SHIT!
        }
        return null;
    }
    public function es_valido($user, $pass)	{
        $uniqueUser = getenv('API_DOOMSDAY_USR');					//IF YOU SET THIS UP, EAT YOUR SHIT!
        if ($uniqueUser != $user) {
            return false;
        }
        if ($this->get_password($user) == $pass) {
            return true;
        }
        return false;
    }
}