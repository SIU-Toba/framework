<?
require_once("nucleo/persistencia/db_tablas.php");
require_once("admin/editores/dbr_apex_objeto.php");
require_once("admin/editores/dbr_apex_objeto_dependencias.php");
require_once("dbr_apex_objeto_mt_me.php");
require_once("dbr_apex_objeto_mt_me_etapa.php");
require_once("dbr_apex_objeto_mt_me_etapa_dep.php");

class dbt_ci extends db_tablas
{
	function __construct($fuente)
	{
		//db_registros
		$this->elemento['base'] = new dbr_apex_objeto($fuente, 1, 1);
		$this->elemento['dependencias'] = new dbr_apex_objeto_dependencias($fuente, 0, 0);
		$this->elemento['prop_basicas'] = new dbr_apex_objeto_mt_me($fuente, 1, 1);
		$this->elemento['pantallas'] = new dbr_apex_objeto_mt_me_etapa($fuente, 1, 0);
		$this->elemento['pantallas_deps'] = new dbr_apex_objeto_mt_me_etapa_dep($fuente, 0, 0);
		//Relaciones
		$this->cabecera = 'base';
		$this->detalles = array(
								'dependencias'=>array('proyecto','objeto_consumidor'), 
								'prop_basicas'=>array('objeto_mt_me_proyecto','objeto_mt_me'),
								'pantallas'=>array('objeto_mt_me_proyecto','objeto_mt_me'),
								'pantallas_dep'=>array('objeto_mt_me_proyecto','objeto_mt_me')
							);
		parent::__construct($fuente);
	}
}
?>