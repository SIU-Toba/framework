<?php

class toba_migracion_1_3_0 extends toba_migracion
{
	function instancia__cambios_estructura()
	{
		$sql = "ALTER TABLE apex_objeto ALTER COLUMN subclase_archivo TYPE VARCHAR(255);";
		$sql = "ALTER TABLE apex_proyecto ADD COLUMN tiempo_espera_ms INTEGER";
		$this->elemento->get_db()->ejecutar($sql);
	}	
	
	/*
	* Se asigna el valor 30 al campo que controla el tiempo que permanecera abierta una sesion sin tener interaccion.
	*/	
	function proyecto__tiempo_no_interac_por_defecto()
	{
		$sql = "UPDATE apex_proyecto 
				SET sesion_tiempo_no_interac_min = 30
				WHERE 
						proyecto = '{$this->elemento->get_id()}' 
					AND (sesion_tiempo_no_interac_min IS NULL OR sesion_tiempo_no_interac_min = 0)";
		return $this->elemento->get_db()->ejecutar($sql);
	}	
	
	/*
	* Nueva configuracion para mensaje de espera cuando la operacion no responde 
	*/	
	function proyecto__tiempo_espera()
	{
		$sql = "UPDATE apex_proyecto 
				SET tiempo_espera_ms = 2000
				WHERE
					proyecto = '{$this->elemento->get_id()}'
		";
		return $this->elemento->get_db()->ejecutar($sql);
	}		
}

?>