<?php

class curso_intro_modelo extends toba_aplicacion_modelo_base 
{
	function __construct()
	{
		$this->permitir_exportar_modelo = false;
		$this->schema_modelo = 'curso_intro';
	}
	
	function get_id_base()
	{
		$parametros = $this->get_instancia()->get_parametros_db();
		return $parametros['base'];
	}	
	
	function get_version_nueva()
	{
		return $this->get_instalacion()->get_version_actual();
	}

	function cargar_datos(toba_db $base)
	{
		parent::cargar_datos($base);
		$secuencia = $this->proyecto->get_dir().'/sql/secuencias.sql';
		if (file_exists($secuencia)) {
			$this->manejador_interface->mensaje('Actualizando secuencias', false);
			$this->manejador_interface->progreso_avanzar();	
			$base->ejecutar_archivo($secuencia);
			$this->manejador_interface->progreso_fin();
		}
	}	
	
}


?>