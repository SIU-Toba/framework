<?php

namespace SIUToba\rest\seguridad;

use SIUToba\rest\http\request;
use SIUToba\rest\http\respuesta_rest;

abstract class proveedor_autenticacion
{
    /**
     * Obtiene un usuario si está logueado o si lo puede obtener del request o cualquier otro medio.
     * Si el usuario es nulo, se puede llegar a llamar a requerir_autenticacion (si la operacion lo requiere).
     * En caso de errores, guardarlos y enviarlos en la respuesta.
     *
     * @param request $request
     *
     * @return rest_usuario el usuario logueado o null si es anonimo
     */
    abstract public function get_usuario(request $request = null);

    /**
     * Escribe la respuesta/headers para pedir autenticacion al usuario.
     *
     * @param respuesta_rest $rta
     *
     * @return mixed
     */
    abstract public function requerir_autenticacion(respuesta_rest $rta);
}
