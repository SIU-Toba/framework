<?php

class toba_usuarios_modelo extends toba_aplicacion_modelo_base 
{
	function __construct()
	{
		$this->permitir_exportar_modelo = false;
		$this->permitir_instalar = false;
	}

	function get_version_nueva()
	{
		return $this->get_instalacion()->get_version_actual();
	}	
}

?>