<?php
class toba_migracion_3_3_0 extends toba_migracion
{
	function instancia__cambios_estructura()
	{
		/**
		* Se evita el mensaje 'ERROR:  cannot ALTER TABLE "apex_objeto" because
		* it has pending trigger events' de postgres 8.3
		*/
		$sql = 'SET CONSTRAINTS ALL IMMEDIATE;';
		$this->elemento->get_db()->ejecutar($sql);

		$sql = 'SET CONSTRAINTS ALL DEFERRED;';
		$this->elemento->get_db()->ejecutar($sql);
	}

        function proyecto__rip_yui_menu()
	{
		$db = $this->elemento->get_db();
		$proyecto = $this->elemento->get_db()->quote($this->elemento->get_id());
		//Actualizo los menues que se puedan haber creado con YUI
		$sql_up[] = 'UPDATE apex_menu '
			. "SET tipo_menu = 'css' "
			. "WHERE tipo_menu = 'yui' "
			. "AND proyecto = $proyecto;";
		//Actualizo el menu por defecto de la aplicacion si tiene YUI
		$sql_up[] = "UPDATE apex_proyecto SET menu='css' "
			. "WHERE menu='yui' "
			. "AND proyecto = $proyecto;";
		$db->ejecutar($sql_up);
	}
}
?>