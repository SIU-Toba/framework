<?php

use SIUToba\rest\rest;
use SIUToba\rest\seguridad\autenticacion\usuarios_api_key;
use SIUToba\rest\seguridad\autenticacion\usuarios_password;
use SIUToba\rest\seguridad\autenticacion\usuarios_usuario_password;

class toba_usuarios_rest_conf implements
	usuarios_password, //devuelve password planos para usar en digest
	usuarios_api_key,  //devuleve api_keys para usar en api_key
	usuarios_usuario_password //devuelve si user/pass es valido para usar en basic
{

	protected $modelo_proyecto;

	function __construct(\toba_modelo_proyecto $proyecto)
	{
		$this->modelo_proyecto = $proyecto;
	}


	/**
	 * Retorna si el usuario password es valido
	 */
	function es_valido($usuario, $password)
	{ //se usa para basic
		$usuarios_ini = toba_modelo_rest::get_ini_usuarios($this->modelo_proyecto);

		foreach ($usuarios_ini->get_entradas() as $key => $u) {
			if ($key === $usuario) {
				if (isset($u['password'])) {
					return $u['password'] == $password;
				} else {
					rest::app()->logger->info('Se encontro al usuario "' . $usuario . '", pero no tiene una entrada password en rest_usuario.ini');
				}
			}
		}
	}

	/**
	 * Dado el username, retorna el password para ser comparado.
	 * @param $usuario
	 * @return mixed string\null. El password o NULL si el usuario no existe
	 */
	function get_password($usuario)
	{ //se usa para digest, ya que se requiere el password plano
		$usuarios_ini = toba_modelo_rest::get_ini_usuarios($this->modelo_proyecto);

		foreach ($usuarios_ini->get_entradas() as $key => $u) {
			if ($key === $usuario) {
				if (isset($u['password'])) {
					return $u['password'];
				} else {
					rest::app()->logger->info('Se encontro al usuario "' . $usuario . '", pero no tiene una entrada password en rest_usuario.ini');
				}
			}
		}
		return NULL;
	}

	/**
	 * Dado el username, retorna el api_key.
	 * @param $api_key
	 * @return mixed string\null. El api_key o NULL si el usuario no existe
	 */
	function get_usuario_api_key($api_key)
	{
		$usuarios_ini = toba_modelo_rest::get_ini_usuarios($this->modelo_proyecto);

		foreach ($usuarios_ini->get_entradas() as $username => $u) {
			if (isset($u['api_key']) && $u['api_key'] === $api_key) {
				return $username;
			}
		}
		rest::app()->logger->info("No se encontro 'api_key = $api_key' para ningún usuario de rest_usuarios.ini");
		return NULL;
	}


}