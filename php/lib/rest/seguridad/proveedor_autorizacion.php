<?php

namespace rest\seguridad;

abstract class proveedor_autorizacion
{

	public abstract function tiene_acceso($usuario, $ruta);
}