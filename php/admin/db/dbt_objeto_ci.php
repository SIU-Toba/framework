<?
require_once("dbt_objeto.php");
require_once("db_registros/dbr_apex_objeto.php");
require_once("db_registros/dbr_apex_objeto_dependencias.php");
require_once("db_registros/dbr_apex_objeto_mt_me.php");
require_once("db_registros/dbr_apex_objeto_ci_pantalla.php");
require_once("db_registros/dbr_apex_objeto_ci_pantalla_dep.php");
require_once("db_registros/dbr_apex_objeto_eventos.php");

class dbt_objeto_ci extends dbt_objeto
{
	function __construct($fuente)
	{
		//db_registros
		$this->elemento['base'] = new 				dbr_apex_objeto($fuente, 1, 1);
		$this->elemento['dependencias'] = new 		dbr_apex_objeto_dependencias($fuente, 0, 0);
		$this->elemento['prop_basicas'] = new 		dbr_apex_objeto_mt_me($fuente, 1, 1);
		$this->elemento['pantallas'] = new 			dbr_apex_objeto_ci_pantalla($fuente, 1, 0);
		$this->elemento['pantallas_deps'] = new 	dbr_apex_objeto_ci_pantalla_dep($fuente, 0, 0);
		$this->elemento['eventos'] = new 			dbr_apex_objeto_eventos($fuente, 0,0);
		//Relaciones
		$this->cabecera = 'base';
		$this->detalles = array(
								'dependencias'=>array('proyecto','objeto_consumidor'), 
								'prop_basicas'=>array('objeto_mt_me_proyecto','objeto_mt_me'),
								'pantallas'=>array('objeto_ci_proyecto','objeto_ci'),
								'pantallas_dep'=>array('objeto_ci_proyecto','objeto_ci'),
								'eventos'=>array('proyecto','objeto')
							);
		parent::__construct($fuente);
	}
}
?>