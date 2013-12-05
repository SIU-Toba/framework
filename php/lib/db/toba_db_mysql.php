<?php
/**
 * Driver de conexión con mysql.
 * @package Fuentes
 * @subpackage Drivers
 */
class toba_db_mysql extends toba_db
{
	function __construct($profile, $usuario, $clave, $base, $puerto)
	{
		$this->motor = "mysql";
		parent::__construct($profile, $usuario, $clave, $base, $puerto);
	}
	
	function get_dsn()
	{
		$puerto = ($this->puerto != '') ? "port={$this->puerto}": '';
		return "mysql:host=$this->profile;dbname=$this->base;$puerto";	
	}
	
	function set_encoding($encoding, $ejecutar = true)
	{
	
	}
	
}
?>