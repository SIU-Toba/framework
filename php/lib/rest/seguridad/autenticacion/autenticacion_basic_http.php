<?php

namespace rest\seguridad\autenticacion;

use rest\http\request;
use rest\http\respuesta_rest;
use rest\seguridad\proveedor_autenticacion;
use rest\seguridad\rest_usuario;

class autenticacion_basic_http extends proveedor_autenticacion
{
	/**
	 * @var password_usuarios
	 */
	protected $passwords;

	function __construct(password_usuarios $pu)
	{
		$this->passwords = $pu;
	}


	public function get_usuario(request $request = null)
	{
		if (isset($_SERVER['PHP_AUTH_USER'])) {
			$password = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : NULL;
			$user = $_SERVER['PHP_AUTH_USER'];

			$password_requerido = $this->passwords->get_password($user);

			//validar user password
			if (NULL !== $password_requerido &&
				$password === $password_requerido
			) {
				$usuario = new rest_usuario();
				$usuario->set_usuario($user);
				return $usuario;
			}
		}
		return null; //anonimo
	}

	/**
	 * Escribe la respuesta/headers para pedir autenticacion al usuario.
	 * @param respuesta_rest $rta
	 * @return mixed
	 */
	public function requerir_autenticacion(respuesta_rest $rta)
	{
		$rta->add_headers(array(
			'WWW-Authenticate' => 'Basic realm="Usuario de la API"',
		));
		$rta->set_data(array('mensaje' => 'autenticación cancelada'));
	}
}