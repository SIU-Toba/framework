<?
require_once("nucleo/persistencia/db_tablas.php");
require_once("db_registros/dbr_apex_item.php");
require_once("db_registros/dbr_apex_item_info.php");
require_once("db_registros/dbr_apex_item_msg.php");
require_once("db_registros/dbr_apex_item_nota.php");
require_once("db_registros/dbr_apex_item_objeto.php");
require_once("db_registros/dbr_apex_usuario_grupo_acc_item.php");

class dbt_item extends db_tablas
{
	function __construct($fuente)
	{
		//db_registros
		$this->elemento['base'] = 			new dbr_apex_item($fuente, 1,1);
		$this->elemento['info'] = 			new dbr_apex_item_info($fuente, 1,1);
		$this->elemento['msgs'] = 			new dbr_apex_item_msg($fuente, 0,0);
		$this->elemento['notas'] = 			new dbr_apex_item_nota($fuente, 0,0);
		$this->elemento['objetos'] = 		new dbr_apex_item_objeto($fuente, 0,0);		
		$this->elemento['permisos'] = 		new dbr_apex_usuario_grupo_acc_item($fuente, 0,0);
		//Relaciones
		$this->cabecera = 'base';
		$this->detalles = array(
								'info'=>array('proyecto','item'),
								'msgs'=>array('proyecto','item'),
								'notas'=>array('proyecto','item'),
								'objetos'=>array('proyecto','item'),
								'permisos'=>array('proyecto','item')
							);
		parent::__construct($fuente);
	}
}


?>	