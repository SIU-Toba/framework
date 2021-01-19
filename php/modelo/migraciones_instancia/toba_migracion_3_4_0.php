<?php
class toba_migracion_3_4_0 extends toba_migracion
{
	function instancia__cambios_estructura()
	{
		/**
		 * Cambia el schema a los logs
		 */
		$schema_logs = $this->elemento->get_db()->get_schema() . '_logs';
		
		/**
		* Se evita el mensaje 'ERROR:  cannot ALTER TABLE "apex_objeto" because
		* it has pending trigger events' de postgres 8.3
		*/		
		$sql = 'SET CONSTRAINTS ALL IMMEDIATE;';
		$this->elemento->get_db()->ejecutar($sql);
		$sql = array();

		$sql[] = "ALTER TABLE $schema_logs.apex_sesion_browser ALTER COLUMN ip SET DATA TYPE TEXT;";
        $sql[] = "ALTER TABLE $schema_logs.apex_solicitud_browser ALTER COLUMN ip SET DATA TYPE TEXT;";
		$sql[] = "ALTER TABLE $schema_logs.apex_solicitud_consola ALTER COLUMN ip SET DATA TYPE TEXT;";
		$sql[] = "ALTER TABLE $schema_logs.apex_log_ip_rechazada ALTER COLUMN ip SET DATA TYPE TEXT;";
		$sql[] = "ALTER TABLE $schema_logs.apex_solicitud_web_service ALTER COLUMN ip SET DATA TYPE TEXT;";
		$sql[] = 'ALTER TABLE apex_usuario ADD COLUMN uid VARCHAR(36) DEFAULT NULL;';
        $sql[] = 'ALTER TABLE apex_usuario ADD COLUMN p_uid TEXT DEFAULT NULL;';

		$this->elemento->get_db()->ejecutar($sql);

		$sql = 'SET CONSTRAINTS ALL DEFERRED;';
		$this->elemento->get_db()->ejecutar($sql);
	}
}
?>
