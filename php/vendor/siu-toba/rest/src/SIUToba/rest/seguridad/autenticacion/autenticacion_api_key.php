<?php

namespace SIUToba\rest\seguridad\autenticacion;

use SIUToba\rest\http\request;
use SIUToba\rest\http\respuesta_rest;
use SIUToba\rest\seguridad\proveedor_autenticacion;
use SIUToba\rest\seguridad\rest_usuario;

class autenticacion_api_key extends proveedor_autenticacion
{
    /**
     * @var usuarios_password
     */
    protected $api_keys;

    protected $mensaje;

    public function __construct(usuarios_api_key $pu)
    {
        $this->api_keys = $pu;
    }

    public function get_usuario(request $request = null)
    {
        $api_key = $request->get('api_key', '');
        $username = $this->api_keys->get_usuario_api_key($api_key);
        if ($username !== null) {
            $usuario = new rest_usuario();
            $usuario->set_usuario($username);

            return $usuario;
        }
        if (isset($api_key)) {
            $this->mensaje = "No se encontro usuario para '?api_key=$api_key'";
        } else {
            $this->mensaje = 'Debe proveer una api_key en la URL ?api_key=';
        }

        return; //anonimo
    }

    /**
     * Escribe la respuesta/headers para pedir autenticacion al usuario.
     *
     * @param respuesta_rest $rta
     *
     * @return mixed
     */
    public function requerir_autenticacion(respuesta_rest $rta)
    {
        $rta->set_data(array('mensaje' => $this->mensaje));
    }
}
