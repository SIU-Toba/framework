<?
require_once("nucleo/persistencia/db_tablas_cd.php");
require_once("admin/editores/dbr_apex_objeto.php");
require_once("admin/editores/dbr_apex_objeto_dependencias.php");
require_once("dbr_apex_objeto_mt_me.php");
require_once("dbr_apex_objeto_mt_me_etapa.php");
require_once("dbr_apex_objeto_mt_me_etapa_dep.php");

class dbt_edt extends db_tablas_cd
{
	function __construct($fuente)
	{
		//db_registros
		$this->elemento['basico'] = new dbr_apex_objeto('basico', $fuente, 1);
		$this->elemento['basico_dep'] = new dbr_apex_objeto_dependencias('basico_dep', $fuente, 1);
		$this->elemento['especifico'] = new dbr_apex_objeto_mt_me('mt_me', $fuente, 1);
		$this->elemento['etapas'] = new dbr_apex_objeto_mt_me_etapa('mt_me_etapas', $fuente, 1);
		$this->elemento['etapas_dep'] = new dbr_apex_objeto_mt_me_etapa_dep('mt_me_etapas_dep', $fuente, 1);
		//Relaciones
		$this->cabecera = 'basico';
		$this->detalles = array(
								'basico_dep'=>array('proyecto','objeto_consumidor'), 
								'especifico'=>array('objeto_mt_me_proyecto','objeto_mt_me'),
								'etapas'=>array('objeto_mt_me_proyecto','objeto_mt_me'),
								'etapas_dep'=>array('objeto_mt_me_proyecto','objeto_mt_me')
							);
		parent::__construct($fuente);
	}
}
?>
