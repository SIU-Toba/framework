<?
require_once("dbt_objeto.php");
require_once("db_registros/dbr_apex_objeto.php");

class dbt_objeto_ei_archivos extends dbt_objeto
{
	function __construct($fuente)
	{
		//db_registros
		$this->elemento['base'] = new 				dbr_apex_objeto($fuente, 1, 1);
		$this->cabecera = 'base';
		$this->detalles = array();
		parent::__construct($fuente);
	}
}
?>