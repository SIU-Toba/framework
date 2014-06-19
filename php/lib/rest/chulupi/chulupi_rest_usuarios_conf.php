<?php

namespace rest\chulupi;

use rest\seguridad\autenticacion\usuarios_password;
use rest\seguridad\autenticacion\usuarios_usuario_password;

class chulupi_rest_usuarios_conf implements	usuarios_usuario_password, usuarios_password
{


	function __construct()
	{

	}


	/**
	 * Retorna si el usuario password es valido
	 */
	function es_valido($usuario, $password)
	{
		//se usa para basic
		return false;
	}

	/**
	 * Dado el username, retorna el password para ser comparado.
	 * @param $usuario
	 * @return mixed string\null. El password o NULL si el usuario no existe
	 */
	function get_password($usuario)
	{
		//se usa para digest
	}
}