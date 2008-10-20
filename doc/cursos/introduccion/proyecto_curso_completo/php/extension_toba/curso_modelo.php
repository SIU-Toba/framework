<?php

class curso_modelo extends toba_aplicacion_modelo_base 
{
	function __construct()
	{
		$this->permitir_exportar_modelo = false;
		$this->schema_modelo = 'curso';
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
}

?>