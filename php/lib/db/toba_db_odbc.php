<?
require_once("toba_db.php");

class toba_db_odbc extends toba_db
{
	function __construct($profile, $usuario, $clave, $base)
	{
		$this->motor = "odbc";
		parent::__construct($profile, $usuario, $clave, $base);
	}
}
?>