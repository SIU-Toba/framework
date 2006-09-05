<?php
require_once('nucleo/browser/hilo.php');

class hilo_version_test extends hilo
{
	protected $sincronizada;
	
	function __construct()
	{
	}
	
	function set_dato_sincronizado($indice, $datos)
	{
		$this->sincronizada[$indice] = $datos;
	}
	
	function recuperar_dato_sincronizado($indice)
	{
		return $this->sincronizada[$indice];			
	}
	

}


?>