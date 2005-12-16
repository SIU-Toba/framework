<?
require_once("db.php");

class db_informix extends db
{
	function __construct($profile, $usuario, $clave, $base)
	{
		$this->motor = "informix";
		parent::__construct($profile, $usuario, $clave, $base);
	}
}
?>