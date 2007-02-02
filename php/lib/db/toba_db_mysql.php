<?
require_once("toba_db.php");

/**
 * Driver de conexión con mysql.
 * @package Fuentes
 * @subpackage Drivers
 */
class toba_db_mysql extends toba_db
{
	function __construct($profile, $usuario, $clave, $base)
	{
		$this->motor = "mysql";
		parent::__construct($profile, $usuario, $clave, $base);
	}
	
	function get_dsn()
	{
		return "mysql:host=$this->profile;dbname=$this->base";	
	}
	
}
?>