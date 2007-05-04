<?php

class toba_migracion_0_9_1 extends toba_migracion
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
		$sql[] = "ALTER TABLE apex_objeto_eventos ADD COLUMN accion_vinculo_carpeta varchar(60)";
		$sql[] = "ALTER TABLE apex_objeto_eventos ADD COLUMN accion_vinculo_item varchar(60)";
		$sql[] = "ALTER TABLE apex_objeto_eventos ADD COLUMN accion_vinculo_objeto int4";
		$sql[] = "ALTER TABLE apex_objeto_eventos ADD COLUMN accion_vinculo_popup smallint";
		$sql[] = "ALTER TABLE apex_objeto_eventos ADD COLUMN accion_vinculo_popup_param varchar(100)";
		$sql[] = "ALTER TABLE apex_objeto_eventos ADD COLUMN accion_vinculo_target varchar(40)";
		$sql[] = "ALTER TABLE apex_objeto_eventos ADD COLUMN accion_vinculo_celda varchar(40)";
		$sql[] = "ALTER TABLE apex_log_objeto ADD COLUMN item varchar(60)";
		$this->elemento->get_db()->ejecutar($sql);
	}	
}
?>