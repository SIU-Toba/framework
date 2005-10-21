<?php
require_once("3ros/adodb464/session/crypt.inc.php");
//------------------------------------------

class encriptador
{
	static private $instancia;
	var $motor;
	var $clave;
	
	private function __construct() {
		$this->motor = new MD5Crypt();
		//ATENCION: se pide la instancia para que parsee el archivo instancias.php
		//Esto hay que solucionarlo de otra forma
		toba::get_db('instancia');
		$this->clave["db"] = apex_clave_db;
		$this->clave["get"] = apex_clave_get;
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