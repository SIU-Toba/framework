<?php

class toba_migracion_1_3_0 extends toba_migracion
{
	function instancia__cambios_estructura()
	{
		$sql = "ALTER TABLE apex_objeto ALTER COLUMN subclase_archivo TYPE VARCHAR(255);";
		$this->elemento->get_db()->ejecutar($sql);
	}	
}

?>
