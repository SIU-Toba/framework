<?php
class toba_migracion_3_1_0 extends toba_migracion
{
	function instancia__cambios_estructura()
	{		
		/**
		* Se evita el mensaje 'ERROR:  cannot ALTER TABLE "apex_objeto" because
		* it has pending trigger events' de postgres 8.3
		*/
		$sql = 'SET CONSTRAINTS ALL IMMEDIATE;';
		$this->elemento->get_db()->ejecutar($sql);
		$sql = array();		

		$sql[] = 'ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN editor_config_file TEXT NULL;';
                
                                     $sql[] = 'ALTER TABLE apex_proyecto ALTER COLUMN estilo SET DATA TYPE TEXT;';
                                     $sql[] = 'ALTER TABLE apex_objeto_eventos ALTER COLUMN estilo SET DATA TYPE TEXT;';
                                     
		$this->elemento->get_db()->ejecutar($sql);
		
		$sql = 'SET CONSTRAINTS ALL DEFERRED;';
		$this->elemento->get_db()->ejecutar($sql);
	}	
	
}
?>