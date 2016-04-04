<?php

use SIUToba\rest\rest;
use SIUToba\rest\seguridad\autenticacion\usuarios_usuario_password;

class toba_usuarios_rest_ssl implements usuarios_usuario_password
{
	protected $modelo_proyecto;

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
}
?>
