<?php

namespace rest\seguridad\autorizacion;

use rest\seguridad\proveedor_autorizacion;
use rest\seguridad\rest_usuario;

/**
 * @package rest\seguridad
 */
class autorizacion_scopes extends proveedor_autorizacion
{

    /**
     * @param rest_usuario $usuario
     * @param string $ruta
     * @return bool
     */
    public function tiene_acceso($usuario, $ruta)
    {
        return $usuario != null;
    }
}