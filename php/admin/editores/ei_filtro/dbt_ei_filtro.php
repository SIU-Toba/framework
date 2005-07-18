<?
require_once("admin/editores/dbt_objeto_toba.php");
require_once("admin/editores/dbr_apex_objeto.php");
require_once("admin/editores/dbr_apex_objeto_ut_formulario.php");
require_once("admin/editores/dbr_apex_objeto_ut_formulario_ef.php");

class dbt_ei_filtro extends dbt_objeto_toba
{
	function __construct($fuente)
	{
		//db_registros
		$this->elemento['base'] = new dbr_apex_objeto('a', $fuente, 1);
		$this->elemento['especifico'] = new dbr_apex_objeto_ut_formulario('b', $fuente, 1);
		$this->elemento['efs'] = new dbr_apex_objeto_ut_formulario_ef('c', $fuente, 1);
		//Relaciones
		$this->cabecera = 'base';
		$this->detalles = array(
								'especifico'=>array('objeto_ut_formulario_proyecto','objeto_ut_formulario'),
								'efs'=>array('objeto_ut_formulario_proyecto','objeto_ut_formulario')
							);
		parent::__construct($fuente);
	}
}
?>