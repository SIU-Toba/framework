<?php

class toba_usuarios_modelo extends toba_aplicacion_modelo_base 
{
	function __construct()
	{
		$this->permitir_exportar_modelo = false;
	}

	function get_version_nueva()
	{
		return $this->get_instalacion()->get_version_actual();
	}	
	
	function instalar($datos_servidor)
	{
		if (! $this->permitir_instalar) {
			return;
		}		
		$id_def_base = $this->proyecto->construir_id_def_base($this->get_fuente_defecto());		
		if (! $this->instalacion->existe_base_datos_definida($id_def_base)) {
			$datos_servidor = $this->instancia->get_parametros_db();
			//-- Agrega la definición de la base
			$this->instalacion->agregar_db($id_def_base, $datos_servidor);
		}
	}	
}
?>