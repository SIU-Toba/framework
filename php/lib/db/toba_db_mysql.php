<?
require_once("toba_db.php");

class toba_db_mysql extends toba_db
{
	function __construct($profile, $usuario, $clave, $base)
	{
		$this->motor = "mysql";
		parent::__construct($profile, $usuario, $clave, $base);
	}
}
?>