<?
require_once("nucleo/persistencia/ap_tabla_db_s.php");

class apdb_item_objetos extends ap_tabla_db_s
{
	function inicializar()
	{
		$this->activar_proceso_carga_externa_dao(	"get_info_dependencia",
													"dao_editores",
													"admin/db/dao_editores.php", 
													array('proyecto','objeto'), 
													array('clase','nombre_objeto','descripcion') );
	}
}
?>