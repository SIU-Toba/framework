<?
require_once('migracion_toba.php');

class migracion_0_9_1 extends migracion_toba
{

	//------------------------------------------------------------------------
	//-------------------------- INSTALACION --------------------------
	//------------------------------------------------------------------------
	
	
	/**
	 *	Existe una nueva entrada en la instalacion que define el comando de invocación
	 *  del editor utilizado en el escritorio
	 */
	function instalacion__definir_comando_editor()
	{
		$this->elemento->cambiar_info_basica(array('editor_php' => 'start'));
	}

	function instancia__cambios_estructura()
	{
		$sql[] = "ALTER TABLA apex_objeto_eventos ADD COLUMN accion_vinculo_carpeta varchar(60)";
		$sql[] = "ALTER TABLA apex_objeto_eventos ADD COLUMN accion_vinculo_item varchar(60)";
		$sql[] = "ALTER TABLA apex_objeto_eventos ADD COLUMN accion_vinculo_objeto int4";
		$sql[] = "ALTER TABLA apex_objeto_eventos ADD COLUMN accion_vinculo_popup smallint";
		$sql[] = "ALTER TABLA apex_objeto_eventos ADD COLUMN accion_vinculo_popup_param varchar(100)";
		$sql[] = "ALTER TABLA apex_objeto_eventos ADD COLUMN accion_vinculo_target varchar(60)";
		$this->elemento->get_db()->ejecutar($sql);
	}	
}
?>