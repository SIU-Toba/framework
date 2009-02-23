<?php

class toba_migracion_1_3_0 extends toba_migracion
{
	function instancia__cambios_estructura()
	{
		$sql = array();
		$sql[] = "ALTER TABLE apex_objeto ALTER COLUMN subclase_archivo TYPE VARCHAR(255);";
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN tiempo_espera_ms INTEGER;";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN deshabilitar_rest_func SMALLINT;";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN permitir_html SMALLINT;";
		$sql[] = "ALTER TABLE apex_objeto_ei_cuadro_columna ADD COLUMN permitir_html SMALLINT;";
		$sql[] = "ALTER TABLE apex_objeto_eventos ADD COLUMN accion_vinculo_servicio VARCHAR(100);";
		$sql[] = "ALTER TABLE apex_objeto_ei_cuadro_columna ADD COLUMN vinculo_servicio VARCHAR(100);";
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
	
	/*
	* Para mantener compatibilidad hacia atrás, se permite que los ef_fijos tengan estado html 
	*/	
	function proyecto__ef_fijo_permite_html()
	{
		$sql = "UPDATE apex_objeto_ei_formulario_ef 
				SET permitir_html = 1
				WHERE
						objeto_ei_formulario_proyecto = '{$this->elemento->get_id()}'
					AND elemento_formulario = 'ef_fijo'
		";
		return $this->elemento->get_db()->ejecutar($sql);
	}

	/*
	 * Se asigna la celda de memoria 'popup' por defecto para aquellos vinculos que aun no lo tienen especificado.
	 */
	function proyecto__celda_memoria_popup()
	{
		$sql = array();
		//Vinculos en Cuadros
		$sql[] = "UPDATE 	apex_objeto_ei_cuadro_columna SET vinculo_celda = 'popup' 
				WHERE 	objeto_ei_formulario_proyecto = '{$this->elemento->get_id()}'
				AND 	vinculo_celda IS NULL
				AND		vinculo_popup = '1';";
		
		//Vinculos en Formularios/CI
		$sql[] = "UPDATE	apex_objeto_eventos SET accion_vinculo_celda = 'popup'
				WHERE  	objeto_ei_formulario_proyecto = '{$this->elemento->get_id()}'
				AND 	accion_vinculo_celda IS NULL
				AND 	accion_vinculo_popup = '1';";
		$this->elemento->get_db()->ejecutar($sql);
	}
}

?>