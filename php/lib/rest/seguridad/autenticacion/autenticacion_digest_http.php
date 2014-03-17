<?php

namespace rest\seguridad\autenticacion;

use rest\http\request;
use rest\http\respuesta_rest;
use rest\seguridad\proveedor_autenticacion;
use rest\seguridad\rest_usuario;

/**
 * http://www.php.net/manual/en/features.http-auth.php
 */
class autenticacion_digest_http extends proveedor_autenticacion
{

	protected $realm = 'Area Restringida';

	/**
	 * @var usuarios_password
	 */
	protected $passwords;

	function __construct(usuarios_password $pu)
	{
		$this->passwords = $pu;
	}


	public function get_usuario(request $request = null)
	{
		if (isset($_SERVER['PHP_AUTH_DIGEST']) &&
			$data = $this->http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])
		) {
			$user = $data['username'];
			$password_requerido = $this->passwords->get_password($user);

			if (NULL !== $password_requerido) {
				if ($this->validar_digest($data, $this->realm, $password_requerido)) {
					$usuario = new rest_usuario();
					$usuario->set_usuario($user);
					return $usuario;
				}
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
		//importante las comillas dobles
		$header = 'Digest realm="' . $this->realm . '",qop="auth",nonce="' . uniqid() . '",opaque="' . md5($this->realm) . '"';

		$rta->add_headers(array(
			'WWW-Authenticate' => $header,
		));
		$rta->set_data(array('mensaje' => 'autenticación cancelada'));
	}


	protected function http_digest_parse($digest_header)
	{
		// protect against missing data
		$needed_parts = array('nonce' => 1, 'nc' => 1, 'cnonce' => 1, 'qop' => 1, 'username' => 1, 'uri' => 1, 'response' => 1);
		$data = array();
		$keys = implode('|', array_keys($needed_parts));

		preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $digest_header, $matches, PREG_SET_ORDER);

		foreach ($matches as $m) {
			$data[$m[1]] = $m[3] ? $m[3] : $m[4];
			unset($needed_parts[$m[1]]);
		}
		return ($needed_parts) ? false : $data;
	}

	private function validar_digest($data, $realm, $required_passwrod)
	{
		// generate the valid response
		$A1 = md5($data['username'] . ':' . $realm . ':' . $required_passwrod);
		$A2 = md5($_SERVER['REQUEST_METHOD'] . ':' . $data['uri']);
		$digest_plano = $A1 . ':' . $data['nonce'] . ':' . $data['nc'] . ':' . $data['cnonce'] . ':' . $data['qop'] . ':' . $A2;
		$valid_response = md5($digest_plano);

		return $data['response'] == $valid_response;
	}
}