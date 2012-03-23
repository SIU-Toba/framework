<?php

class ci_asistente_base extends toba_ci 
{
	protected $asistente;
	//-----------------------------------------------------------------------------------
	//-- API para el ADMINISTRADOR de asistentes
	//-----------------------------------------------------------------------------------

	function sincronizar()
	{
		//Si el molde es nuevo, recupero la informacion basica de molde del controlador
		if (! $this->dep('datos')->esta_cargada()) {
			$datos = $this->dep('datos')->tabla('molde')->get();
			$datos = array_merge($datos, $this->controlador()->get_datos_basicos());
			$this->dep('datos')->tabla('molde')->set($datos);
		} 
		//-- Actualiza el nombre del molde en base al nombre del item
		$info_item = toba::zona()->get_info();		
		$this->dep('datos')->tabla('molde')->set(array('nombre' => $info_item['nombre']));		
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
	
	function asistente()
	{
		return $this->controlador->asistente();
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
		//-- Asume los nombres a partir de la operación 
		$datos = $this->dep('datos')->tabla('molde')->get();
		$nombre = strtolower(toba::zona()->get_info('nombre'));
		if (!isset($datos['prefijo_clases'])) {
			$datos['prefijo_clases'] = '_'.toba_texto::nombre_valido_clase($nombre);
		}
		if (!isset($datos['carpeta_archivos'])) {
			$datos['carpeta_archivos'] = toba_texto::nombre_valido_clase($nombre);
		}
		return $datos;
	}

	//-----------------------------------------------------------------------------------
	//-- DAOS
	//-----------------------------------------------------------------------------------

	function get_tablas($fuente)
	{
		return toba::db($fuente, toba_editor::get_proyecto_cargado())->get_lista_tablas();
	}

	function validar_datos_ingresados()
	{
		//Ventana de extension para validaciones de los Cis que manejan asistentes
	}
}

?>