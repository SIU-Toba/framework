<?php

class ci_asistente_base extends toba_ci 
{
	function set_molde($proyecto, $id)
	{
		if (! $this->dep('datos')->esta_cargada()) {
			$this->dep('datos')->cargar(array('proyecto' => $proyecto, 'molde' => $id));
		}
	}
	
	function set_molde_nuevo($operacion_tipo)
	{
		$datos_basicos = array();
		$datos_basicos['operacion_tipo'] = $operacion_tipo;
		$datos_basicos['proyecto'] = toba_editor::get_proyecto_cargado();
		$this->dep('datos')->tabla('molde')->set($datos_basicos);	
	}
	
	function sincronizar()
	{
		$this->dep('datos')->sincronizar();	
	}
	
	function get_tablas()
	{
		return toba_editor::get_db_defecto()->get_lista_tablas();
	}

		
}

?>