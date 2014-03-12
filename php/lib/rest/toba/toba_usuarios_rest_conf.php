<?php

namespace rest\toba;

use rest\rest;
use rest\seguridad\autenticacion\api_key_usuarios;
use rest\seguridad\autenticacion\password_usuarios;
use toba_modelo_rest;

class toba_usuarios_rest_conf implements password_usuarios, api_key_usuarios
{

	protected $modelo_proyecto;

	function __construct(\toba_modelo_proyecto $proyecto)
	{
		$this->modelo_proyecto = $proyecto;
	}


	/**
	 * Dado el username, retorna el password para ser comparado.
	 * @param $usuario
	 * @return mixed string\null. El password o NULL si el usuario no existe
	 */
	function get_password($usuario)
	{
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