<?php

namespace rest\chulupi;

use rest\seguridad\autenticacion\usuarios_api_key;
use rest\seguridad\autenticacion\usuarios_password;
use rest\seguridad\autenticacion\usuarios_usuario_password;


class chulupi_usuarios_rest_conf implements
	usuarios_password, //devuelve password planos para usar en digest
	usuarios_api_key,  //devuleve api_keys para usar en api_key
	usuarios_usuario_password //devuelve si user/pass es valido para usar en basic
{



	function __construct()
	{

	}


	/**
	 * Retorna si el usuario password es valido
	 */
	function es_valido($usuario, $password)
	{ //se usa para basic
		return true;
	}

	/**
	 * Dado el username, retorna el password para ser comparado.
	 * @param $usuario
	 * @return mixed string\null. El password o NULL si el usuario no existe
	 */
	function get_password($usuario)
	{ //se usa para digest, ya que se requiere el password plano

		return NULL;
	}

	/**
	 * Dado el username, retorna el api_key.
	 * @param $api_key
	 * @return mixed string\null. El api_key o NULL si el usuario no existe
	 */
	function get_usuario_api_key($api_key)
	{

		return NULL;
	}


}