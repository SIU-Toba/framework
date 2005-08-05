<?
require_once("dbt_objeto.php");
require_once("db_registros/dbr_apex_objeto.php");

class dbt_objeto_ei_calendario extends dbt_objeto
{
	function __construct($fuente)
	{
		//db_registros
		$this->elemento['base'] = new 				dbr_apex_objeto($fuente, 1, 1);
		$this->cabecera = 'base';
		parent::__construct($fuente);
	}
}
?>