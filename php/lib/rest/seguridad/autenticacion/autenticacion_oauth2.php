<?php
/**
 * Created by IntelliJ IDEA.
 * User: andres
 * Date: 1/14/15
 * Time: 5:31 PM
 */

namespace rest\seguridad\autenticacion;

use rest\http\request;
use rest\http\respuesta_rest;
use rest\seguridad\autenticacion\oauth2\oauth_token_decoder;
use rest\seguridad\proveedor_autenticacion;
use rest\seguridad\rest_usuario;

class autenticacion_oauth2 extends proveedor_autenticacion
{
    /**
     * @var oauth_token_decoder
     */
    protected $decoder;

    public function __construct()
    {

    }

    public function set_decoder(oauth_token_decoder $decoder)
    {
        $this->decoder = $decoder;
    }

    /**
     * Obtiene un usuario si está logueado o si lo puede obtener del request o cualquier otro medio.
     * Si el usuario es nulo, se puede llegar a llamar a requerir_autenticacion (si la operacion lo requiere).
     * En caso de errores, guardarlos y enviarlos en la respuesta.
     * @param request $request
     * @return rest_usuario el usuario logueado o null si es anonimo
     */
    public function get_usuario(request $request = null)
    {
        $auth_header = $request->headers('HTTP_AUTHORIZATION', null);
        if ($auth_header === null) {
            return null;
        }
        $token = explode(' ', $auth_header)[1];
        $raw_json = $this->decoder->decode($token);

        if ($raw_json === null) {
            return null;
        }

        $usuario = new rest_usuario();
        $usuario->set_usuario($raw_json['user_id']);
        return $usuario;
    }

    /**
     * Escribe la respuesta/headers para pedir autenticacion al usuario.
     * @param respuesta_rest $rta
     * @return mixed
     */
    public function requerir_autenticacion(respuesta_rest $rta)
    {
        $rta->set_status(401);
        // quizá haya que agregar más detalles al error: http://hdknr.github.io/docs/identity/bearer.html#id5
        $rta->add_headers(array(
            'WWW-Authenticate' => 'Bearer'
        ));
    }
}