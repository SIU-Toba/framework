<?php
require_once("3ros/adodb464/session/crypt.inc.php");
//------------------------------------------

class encriptador
{
	var $motor;
	var $clave;
	
	function encriptador()
	{
		$this->motor = new MD5Crypt();
		$this->clave["db"] = apex_clave_db;
		$this->clave["get"] = apex_clave_get;
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