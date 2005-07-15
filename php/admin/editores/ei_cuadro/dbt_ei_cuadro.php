<?
require_once("nucleo/persistencia/db_tablas.php");
require_once("admin/editores/dbr_apex_objeto.php");
require_once("dbr_apex_objeto_cuadro.php");
require_once("dbr_apex_objeto_cuadro_columna.php");

class dbt_ei_cuadro extends db_tablas
{
	function __construct($fuente)
	{
		//db_registros
		$this->elemento['basico'] = new dbr_apex_objeto('a', $fuente, 1);
		$this->elemento['especifico'] = new dbr_apex_objeto_cuadro('b', $fuente, 1);
		$this->elemento['columnas'] = new dbr_apex_objeto_cuadro_columna('c', $fuente, 1);
		//Relaciones
		$this->cabecera = 'basico';
		$this->detalles = array(
								'especifico'=>array('objeto_cuadro_proyecto','objeto_cuadro'),
								'columnas'=>array('objeto_cuadro_proyecto','objeto_cuadro')
							);
		parent::__construct($fuente);
	}
}
?>