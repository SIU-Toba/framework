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
		//Calculo el fingerprint del certificado enviado por el usuario
		$fingerprint_cert = toba_firma_digital::certificado_get_fingerprint($certificado);		
		//Recupero el fingerprint configurado anteriormente y comparo
		$fingerprint_local = $this->get_usuario_huella($usuario);		
		
		return hash_equals($fingerprint_local, $fingerprint_cert);
	}		
	
	
	function get_usuario_huella($usuario)
	{
		$usuarios_ini = toba_modelo_rest::get_ini_usuarios($this->modelo_proyecto);
		foreach ($usuarios_ini->get_entradas() as $key => $u) {
			if ($key === $usuario) {
				if (isset($u['fingerprint'])) {
					return $u['fingerprint'];
				} else {
					rest::app()->logger->info('Se encontro al usuario "' . $usuario . '", pero no tiene una entrada fingerprint en rest_usuario.ini');
				}
			}
		}
		return NULL;
	}
}
?>
