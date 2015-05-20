<?php

namespace SIUToba\rest\seguridad;

abstract class proveedor_autorizacion
{
    abstract public function tiene_acceso($usuario, $ruta);
}
