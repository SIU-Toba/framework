<?php

namespace rest\chulupi;

use kernel\kernel;
use kernel\util\config;
use rest\seguridad\autenticacion\usuarios_password;
use rest\seguridad\autenticacion\usuarios_usuario_password;

class chulupi_rest_usuarios implements	usuarios_usuario_password
{

	protected $fuente;

	function __construct()
	{
		$this->fuente = kernel::localizador()->instanciar(kernel::proyecto()->get_fuente_usuarios());
	}


	/**
	 * Retorna si el usuario password es valido
	 */
	function es_valido($usuario, $password)
	{
		return $this->fuente->autenticar_login_rest($usuario, $password);
	}

}