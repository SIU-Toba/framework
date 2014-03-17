<?php

namespace rest\seguridad\autenticacion;

use rest\http\request;
use rest\http\respuesta_rest;
use rest\seguridad\proveedor_autenticacion;
use rest\seguridad\rest_usuario;

class autenticacion_basic_http extends proveedor_autenticacion
{
	/**
	 * @var usuarios_usuario_password
	 */
	protected $validador_usuarios;

	function __construct(usuarios_usuario_password $pu)
	{
		$this->validador_usuarios = $pu;
	}


	public function get_usuario(request $request = null)
	{
		if (isset($_SERVER['PHP_AUTH_USER'])) {
			$password = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : NULL;
			$user = $_SERVER['PHP_AUTH_USER'];
			//validar user password
			if ($this->validador_usuarios->es_valido($user, $password)) {
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