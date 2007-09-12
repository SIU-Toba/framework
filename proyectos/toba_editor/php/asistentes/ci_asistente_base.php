<?php

class ci_asistente_base extends toba_ci 
{
	//-----------------------------------------------------------------------------------
	//-- API para el ADMINISTRADOR de asistentes
	//-----------------------------------------------------------------------------------

	function sincronizar()
	{
		//Si el molde es nuevo, recupero la informacion basica de molde del controlador
		if (! $this->dep('datos')->esta_cargada()) {
			$datos = $this->dep('datos')->tabla('molde')->get();
			$datos = array_merge($datos, $this->controlador()->get_datos_basicos() );
			$this->dep('datos')->tabla('molde')->set($datos);
		}
		$this->dep('datos')->sincronizar();	
	}

	function get_clave_molde()
	{
		return $this->dep('datos')->tabla('molde')->get_clave_valor(0);	
	}

	function set_molde($proyecto, $id)
	{
		$this->dep('datos')->cargar(array('proyecto' => $proyecto, 'molde' => $id));
	}

	/**
	*	Indica si ya existe la informacion necesaria para disparar la generacion
	*	Hay que sobreescribirlo en cada asistente.
	*/
	function posee_informacion_completa()
	{
		$datos_molde = $this->dep('datos')->tabla('molde')->get();	
		if( isset($datos_molde['carpeta_archivos']) && isset($datos_molde['prefijo_clases']) ) {
			return true;	
		}
		return false;
	}
	
	/*
		Por ahora no se utilizaria, cambiar de tipo de asistente asociado a un item
		tiene varias implicancias que hay que analizar, por ahora habria que eliminar el item
	*/
	function unset_molde()
	{
		if ($this->dep('datos')->esta_cargada()) {
			$this->dep('datos')->eliminar_todo();
			$this->dep('datos')->resetear();
		} else {
			throw new toba_error('ERROR: Se solicito borrar un molde inexistente');
		}
	}
	
	//-----------------------------------------------------------------------------------
	//-- Manejo del formulario basico, transversal a todos los moldes
	//-----------------------------------------------------------------------------------
	
	function evt__form_molde__modificacion($datos)
	{
		$this->dep('datos')->tabla('molde')->set($datos);
	}

	function conf__form_molde()
	{
		return $this->dep('datos')->tabla('molde')->get();
	}

	//-----------------------------------------------------------------------------------
	//-- DAOS
	//-----------------------------------------------------------------------------------

	function get_tablas()
	{
		return toba_editor::get_db_defecto()->get_lista_tablas();
	}
		
}

?>