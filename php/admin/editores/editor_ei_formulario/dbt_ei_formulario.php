<?
require_once("nucleo/persistencia/db_tablas.php");
require_once("admin/editores/dbr_apex_objeto.php");
require_once("dbr_apex_objeto_ut_formulario.php");
require_once("dbr_apex_objeto_ut_formulario_ef.php");

class dbt_ei_formulario extends db_tablas
{
	function __construct($fuente)
	{
		//db_registros
		$this->elemento['basico'] = new dbr_apex_objeto('a', $fuente, 1);
		$this->elemento['especifico'] = new dbr_apex_objeto_ut_formulario('b', $fuente, 1);
		$this->elemento['efs'] = new dbr_apex_objeto_ut_formulario_ef('c', $fuente, 1);
		//Relaciones
		$this->cabecera = 'basico';
		$this->detalles = array(
								'especifico'=>array('objeto_ut_formulario_proyecto','objeto_ut_formulario'),
								'efs'=>array('objeto_ut_formulario_proyecto','objeto_ut_formulario')
							);
		parent::__construct($fuente);
	}
}
?>