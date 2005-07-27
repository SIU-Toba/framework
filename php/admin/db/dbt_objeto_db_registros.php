<?
require_once("dbt_objeto.php");
require_once("db_registros/dbr_apex_objeto.php");
require_once("db_registros/dbr_apex_objeto_db_registros.php");
require_once("db_registros/dbr_apex_objeto_db_registros_col.php");

class dbt_objeto_db_registros extends dbt_objeto
{
	function __construct($fuente)
	{
		//db_registros
		$this->elemento['base'] = 			new dbr_apex_objeto($fuente, 1,1);
		$this->elemento['prop_basicas'] = 	new dbr_apex_objeto_db_registros($fuente, 1,1);
		$this->elemento['columnas'] =		new dbr_apex_objeto_db_registros_col($fuente, 1,0);
		//Relaciones
		$this->cabecera = 'base';
		$this->detalles = array(
								'prop_basicas'=>array('proyecto','objeto'),
								'columnas'=>array('proyecto','objeto')
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