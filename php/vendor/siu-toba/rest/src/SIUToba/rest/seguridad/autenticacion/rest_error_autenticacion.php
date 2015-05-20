<?php

namespace SIUToba\rest\seguridad\autenticacion;

use SIUToba\rest\http\respuesta_rest;
use SIUToba\rest\lib\rest_error;
use SIUToba\rest\seguridad\proveedor_autenticacion;

class rest_error_autenticacion extends rest_error
{
    protected $proveedor_autenticacion;

    public function __construct(proveedor_autenticacion $autenticador, $mensaje = "Se requiere autenticaci�n")
    {
        parent::__construct(401, $mensaje);
        $this->proveedor_autenticacion = $autenticador;
    }

    public function configurar_respuesta(respuesta_rest $rta)
    {
        parent::configurar_respuesta($rta);
        $this->proveedor_autenticacion->requerir_autenticacion($rta);
    }
}
