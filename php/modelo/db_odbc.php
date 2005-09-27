<?
require_once("db.php");

class db_odbc extends db
{
	function __construct($profile, $usuario, $clave, $base)
	{
		$this->motor = "odbc";
		parent::__construct($profile, $usuario, $clave, $base);
	}
}
?>