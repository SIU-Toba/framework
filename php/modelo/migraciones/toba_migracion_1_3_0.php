<?php

class toba_migracion_1_3_0 extends toba_migracion
{
	function instancia__cambios_estructura()
	{
		$sql = "ALTER TABLE apex_objeto ALTER COLUMN subclase_archivo TYPE VARCHAR(255);";
		$this->elemento->get_db()->ejecutar($sql);
	}	
	
	/*
	* Se asigna el valor 30 al campo que controla el tiempo que permanecera abierta una sesion sin tener interaccion.
	*/	
	function proyecto__tiempo_no_interac_por_defecto()
	{
		$sql = "UPDATE apex_proyecto SET sesion_tiempo_no_interac_min = 30
				WHERE (sesion_tiempo_no_interac_min IS NULL) OR (sesion_tiempo_no_interac_min = 0);";
		
		return $this->elemento->get_db()->ejecutar($sql);
	}	
}

?>
