<?php
class toba_migracion_2_5_0 extends toba_migracion
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
		
		$sql[] = 'ALTER TABLE apex_usuario ADD COLUMN forzar_cambio_pwd SMALLINT NOT NULL DEFAULT 0;';
		$sql[] = 'ALTER TABLE apex_usuario_pwd_usados ADD COLUMN fecha_cambio DATE NOT NULL DEFAULT current_date();';
		$this->elemento->get_db()->ejecutar($sql);
		
		$sql = 'SET CONSTRAINTS ALL DEFERRED;';
		$this->elemento->get_db()->ejecutar($sql);
	}	
}
?>
