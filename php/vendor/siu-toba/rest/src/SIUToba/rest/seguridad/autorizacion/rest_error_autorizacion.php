<?php

namespace SIUToba\rest\seguridad\autorizacion;

use SIUToba\rest\lib\rest_error;

class rest_error_autorizacion extends rest_error
{
    public function __construct($mensaje = "Acceso denegado", $detalle = array())
    {
        parent::__construct(403, $mensaje, $detalle);
    }
}
