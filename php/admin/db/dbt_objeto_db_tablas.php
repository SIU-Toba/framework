<?
require_once("dbt_objeto.php");
require_once("db_registros/dbr_apex_objeto.php");
require_once("db_registros/dbr_apex_objeto_dependencias.php");

class dbt_objeto_ci extends dbt_objeto
{
	function __construct($fuente)
	{
		//db_registros
		$this->elemento['base'] = new 				dbr_apex_objeto($fuente, 1, 1);
		$this->elemento['dependencias'] = new 		dbr_apex_objeto_dependencias($fuente, 0, 0);
		//Relaciones
		$this->cabecera = 'base';
		$this->detalles = array(
								'dependencias'=>array('proyecto','objeto_consumidor') 
							);
		parent::__construct($fuente);
	}

	function evt__pre_sincronizacion()
	{
		parent::evt__pre_sincronizacion();
		$this->elemento['base']->set_registro_valor(0,"clase_proyecto", "toba" );
		$this->elemento['base']->set_registro_valor(0,"clase", "objeto_db_registros" );
	}	
}
?>