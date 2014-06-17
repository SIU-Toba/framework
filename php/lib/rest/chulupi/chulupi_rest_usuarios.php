<?php

namespace rest\chulupi;

use kernel\kernel;
use rest\seguridad\autenticacion\usuarios_usuario_password;
use siu\errores\error_guarani_login;

class chulupi_rest_usuarios implements	usuarios_usuario_password //devuelve si user/pass es valido para usar en basic
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
		$lm = kernel::sesion()->get_login_manager();
		try {
			return $lm->autenticar($usuario, $password);
		}catch (error_guarani_login $error){

		}
		return false;
	}

}