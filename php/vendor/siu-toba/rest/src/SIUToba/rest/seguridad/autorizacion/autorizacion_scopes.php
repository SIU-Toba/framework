<?php

namespace SIUToba\rest\seguridad\autorizacion;

use SIUToba\rest\seguridad\proveedor_autorizacion;
use SIUToba\rest\seguridad\rest_usuario;

/**
 */
class autorizacion_scopes extends proveedor_autorizacion
{
    protected $scopes = array();

    /**
     * @param array $scopes arreglo de strings con los nombres de los scopes
     */
    public function set_scopes_requeridos($scopes)
    {
        $this->scopes = $scopes;
    }

    /**
     * @param rest_usuario $usuario
     * @param string       $ruta
     *
     * @return bool
     */
    public function tiene_acceso($usuario, $ruta)
    {
        if (null === $usuario) {
            return false;
        }
        // en el contexto de oauth2 los perfiles son los scopes
        $user_scopes = $usuario->get_perfiles();
        if (empty($user_scopes)) {
            $user_scopes = '';
        }

        // se chequea que todos los scopes requeridos estén en el arreglo de scopes del usuario
        $user_scopes_array = explode(' ', $user_scopes);
        foreach ($this->scopes as $required_scope) {
            if (!in_array($required_scope, $user_scopes_array)) {
                return false;
            }
        }

        return true;
    }
}
