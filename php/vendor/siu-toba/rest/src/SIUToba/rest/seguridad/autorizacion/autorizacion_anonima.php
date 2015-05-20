<?php

namespace SIUToba\rest\seguridad\autorizacion;

use SIUToba\rest\seguridad\proveedor_autorizacion;

/**
 * Autoriza si esta logueado sin importar quien sea
 * Class autorizacion_anonima.
 */
class autorizacion_anonima extends proveedor_autorizacion
{
    public function tiene_acceso($usuario, $ruta)
    {
        return $usuario != null;
    }
}
