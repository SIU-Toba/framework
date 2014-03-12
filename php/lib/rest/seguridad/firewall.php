<?php

namespace rest\seguridad;

use rest\seguridad\autenticacion\rest_error_autenticacion;
use rest\seguridad\autorizacion\rest_error_autorizacion;

/**
 * Parte del esquema inspirado en symfony
 * http://symfony.com/doc/current/book/security.html
 *
 * El firewall, si captura una ruta, se encarga de la autenticacion
 * @package rest\seguridad
 */
class firewall
{

	protected $authentication;
	protected $authorization;
	protected $path_pattern;

	function __construct(proveedor_autenticacion $authen, proveedor_autorizacion $author, $pattern)
	{
		$this->authentication = $authen;
		$this->authorization = $author;
		$this->path_pattern = $pattern;
	}

	public function maneja_ruta($ruta)
	{
		return preg_match($this->path_pattern, $ruta) == 1;
	}

	/**
	 * @param $ruta
	 * @param $request
	 * @throws autenticacion\rest_error_autenticacion
	 * @throws autorizacion\rest_error_autorizacion
	 * @return rest_usuario
	 */
	public function manejar($ruta, $request)
	{
		/* RFC:
		  401 Unauthorized:
			  If the request already included Authorization credentials, then the 401 response indicates that authorization has been refused for those credentials.
		  403 Forbidden:
			  The server understood the request, but is refusing to fulfill it.
		 */

		$usuario = $this->authentication->get_usuario($request);

		if (!$this->authorization->tiene_acceso($usuario, $ruta)) {
			if (NULL === $usuario) {
				throw new rest_error_autenticacion($this->authentication);
			} else {
				throw new rest_error_autorizacion();
			}
		}
		return $usuario;
	}
}