<?php
/**
 * Driver de conexión via ODBC
 * @package Fuentes
 * @subpackage Drivers
 */
class toba_db_odbc extends toba_db
{
	function __construct($profile, $usuario, $clave, $base, $puerto)
	{
		$this->motor = "odbc";
		parent::__construct($profile, $usuario, $clave, $base, $puerto);
	}
}
?>