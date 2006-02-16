<?php
require_once("3ros/adodb464/session/crypt.inc.php");
require_once("modelo/instalacion.php");
//------------------------------------------

class encriptador
{
	static private $instancia;
	var $motor;
	var $clave;
	
	private function __construct() {
		$this->motor = new MD5Crypt();
		$this->clave = instalacion::get_claves_encriptacion();
	}
	
	static function instancia() {
		if (!isset(self::$instancia)) {
			self::$instancia = new encriptador();	
		}
		return self::$instancia;	
	}	
		
	function cifrar($no_encriptado, $clave="get")
	{
		return $this->motor->Encrypt($no_encriptado,$this->clave[$clave]);
	}
	
	function descifrar($encriptado, $clave="get")
	{
		return  $this->motor->Decrypt($encriptado,$this->clave[$clave]);
	}
}
?>