<?php

namespace rest\chulupi;

use kernel\kernel;
use kernel\util\config;
use rest\seguridad\autenticacion\usuarios_password;
use rest\seguridad\autenticacion\usuarios_usuario_password;

class chulupi_rest_usuarios implements	usuarios_usuario_password
{

	protected $usuarios;

	function __construct($path_archivo_usuarios)
	{
		$this->usuarios = config::load($path_archivo_usuarios);
		klog($this->usuarios);
	}


	/**
	 * Retorna si el usuario password es valido
	 */
	function es_valido($usuario, $password)
	{
		$fuente = kernel::localizador()->instanciar(kernel::proyecto()->get_fuente_usuarios());
		return $fuente->autenticar_login_rest($usuario, $password);
	}

}