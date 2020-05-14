<?php

use SIUToba\rest\rest;
use SIUToba\rest\seguridad\autenticacion\usuarios_usuario_password;

class toba_usuarios_rest_ssl implements usuarios_usuario_password
{
	protected $modelo_proyecto;

	static private $env_config = 'API_SSL_CLIENTES';
	
	function __construct(\toba_modelo_proyecto $proyecto)
	{
		$this->modelo_proyecto = $proyecto;
	}
	
	/**
	 * Retorna si el usuario certificado es valido
	 */
	function es_valido($usuario, $certificado)
	{
	}	
		
	function get_passwords()
	{ 
		$usuarios_ini = toba_modelo_rest::get_ini_usuarios($this->modelo_proyecto);
		$passwords = array();
		foreach ($usuarios_ini->get_entradas() as $key => $u) {
			if (isset($u['fingerprint'])) {
				$passwords[$key]['fingerprint'] = $u['fingerprint'];
			} 
		}		
		return $passwords;
	}
	
	
	private function get_config_usuarios($modelo_proyecto)
	{
		$env_value = \getenv(self::$env_config);
		if (false === $env_value) {
			$usuarios = toba_modelo_rest::get_ini_usuarios($modelo_proyecto);
		} else {
			$datos = parse_rest_config_str($env_value);
			$usuarios = new toba_ini();
			foreach($datos as $dato) {
				$usuarios->agregar_entrada($dato[0], ['fingerprint' => $dato[1]]);
			}
		}
		return $usuarios;
	}
}
?>
