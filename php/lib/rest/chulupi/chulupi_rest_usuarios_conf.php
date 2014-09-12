<?php

namespace rest\chulupi;

use kernel\error_kernel;
use kernel\util\config;
use rest\seguridad\autenticacion\usuarios_password;
use rest\seguridad\autenticacion\usuarios_usuario_password;

class chulupi_rest_usuarios_conf implements	usuarios_usuario_password, usuarios_password
{

	protected $usuarios;

	function __construct($conf)
	{
		if(!isset($conf['archivo_usuarios'])){
			throw new error_kernel("Debe especificar el campo 'archivo_usuarios' para el tipo de autenticacion basic. La ruta de un archivo de configuracion con los usuarios");
		}
		$this->usuarios = config::load($conf['archivo_usuarios']);
	}


	/**
	 * Retorna si el usuario password es valido
	 */
	function es_valido($usuario, $password)
	{
		return isset($this->usuarios[$usuario]) && $this->usuarios[$usuario]['password'] === $password;
	}

	/**
	 * Dado el username, retorna el password para ser comparado.
	 * @param $usuario
	 * @return mixed string\null. El password o NULL si el usuario no existe
	 */
	function get_password($usuario)
	{
		return isset($this->usuarios[$usuario])? $this->usuarios[$usuario]['password']: null;
	}
}