<?php
class toba_migracion_3_4_0 extends toba_migracion
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

		$sql[] = 'ALTER TABLE apex_usuario ADD COLUMN uid TEXT DEFAULT NULL;';
        $sql[] = 'ALTER TABLE apex_usuario ADD COLUMN p_uid TEXT DEFAULT NULL;';

		$this->elemento->get_db()->ejecutar($sql);

		$sql = 'SET CONSTRAINTS ALL DEFERRED;';
		$this->elemento->get_db()->ejecutar($sql);
	}

}
?>