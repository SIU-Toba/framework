<?
require_once("admin/objetos_toba/dbt_objeto_toba.php");
require_once("admin/objetos_toba/dbr_apex_objeto.php");
require_once("admin/objetos_toba/dbr_apex_objeto_eventos.php");
require_once("dbr_apex_objeto_cuadro.php");
require_once("dbr_apex_objeto_cuadro_columna.php");

class dbt_ei_cuadro extends dbt_objeto_toba
{
	function __construct($fuente)
	{
		//db_registros
		$this->elemento['base'] = new dbr_apex_objeto($fuente, 1,1);
		$this->elemento['prop_basicas'] = new dbr_apex_objeto_cuadro($fuente, 1,1);
		$this->elemento['columnas'] = new dbr_apex_objeto_cuadro_columna($fuente, 1,0);
		//Relaciones
		$this->cabecera = 'base';
		$this->detalles = array(
								'prop_basicas'=>array('objeto_cuadro_proyecto','objeto_cuadro'),
								'columnas'=>array('objeto_cuadro_proyecto','objeto_cuadro')
							);
		parent::__construct($fuente);
	}
}
?>