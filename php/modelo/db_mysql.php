<?
require_once("db.php");

class db_mysql extends db
{
	function __construct($profile, $usuario, $clave, $base)
	{
		$this->motor = "mysql";
		parent::__construct($profile, $usuario, $clave, $base);
	}
}
?>