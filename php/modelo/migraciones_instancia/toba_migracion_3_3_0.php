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

        function instancia__rip_yui_menu()
	{
            $sql = array();
            $sql[] = "UPDATE apex_proyecto SET menu='css' WHERE menu = 'yui';";
            $sql[] = "UPDATE apex_menu SET tipo_menu='css' WHERE tipo_menu='yui';";
            $this->elemento->get_db()->ejecutar($sql);
	}

}
?>