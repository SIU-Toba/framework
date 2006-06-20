<?php
require_once("3ros/adodb464/session/crypt.inc.php");

class encriptador
{
	static private $instancia;
	var $motor;
	var $clave;
	
	private function __construct() {
		$this->motor = new MD5Crypt();
		$this->clave = info_instalacion::instancia()->get_claves_encriptacion();
	}
	
	static function instancia() {
		if (!isset(self::$instancia)) {
			self::$instancia = new encriptador();	
		}
		return self::$instancia;	
	}	
		
	function cifrar($no_encriptado, $clave="get")
	{
		$cifrado = $this->motor->Encrypt($no_encriptado,$this->clave[$clave]);
		if ($clave == 'get') {
			return urlencode($cifrado);
		} else {
			return $cifrado;	
		}
	}
	
	function descifrar($encriptado, $clave="get")
	{
		$descifrado = $this->motor->Decrypt($encriptado,$this->clave[$clave]);
		if ($clave == 'get') {
			return urldecode($descifrado);
		} else {
			return $descifrado;	
		}		
	}
}
?>