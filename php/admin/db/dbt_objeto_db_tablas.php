<?
require_once("dbt_objeto.php");
require_once("db_registros/dbr_apex_objeto.php");
require_once("db_registros/dbr_apex_objeto_dependencias.php");
require_once("db_registros/dbr_apex_objeto_datos_rel.php");
require_once("db_registros/dbr_apex_objeto_datos_rel_asoc.php");

class dbt_objeto_db_tablas extends dbt_objeto
{
	function __construct($fuente)
	{
		//db_registros
		$this->elemento['base'] = new 				dbr_apex_objeto($fuente, 1, 1);
		$this->elemento['dependencias'] = new 		dbr_apex_objeto_dependencias($fuente, 0, 0);
		$this->elemento['prop_basicas'] = new 		dbr_apex_objeto_datos_rel($fuente, 0, 0);
		$this->elemento['relaciones'] = new 		dbr_apex_objeto_datos_rel_asoc($fuente, 0, 0);
		//Relaciones
		$this->cabecera = 'base';
		$this->detalles = array(
								'dependencias'=>array('proyecto','objeto_consumidor') ,
								'prop_basicas'=>array('proyecto','objeto') ,
								'relaciones'=>array('proyecto','objeto') 
							);
		parent::__construct($fuente);
	}
}
?>