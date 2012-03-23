<?php

class ci_navegacion extends toba_ci
{
	protected $seleccion;
	protected $filtro = array();
	
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = 'seleccion';
		$propiedades[] = 'filtro';
		return $propiedades;
	}
	
	/**
	 * @return toba_datos_relacion
	 */
	function get_relacion()
	{
		return $this->dependencia('datos');
	}	
	
	function conf__listado()
	{
		$filtro = (isset($this->filtro)) ? $this->filtro : array();
		return toba_info_permisos::get_lista_permisos($filtro);
	}

	function evt__listado__seleccion($id)
	{
		$this->seleccion = $id;
		$this->get_relacion()->cargar($this->seleccion);
		$this->set_pantalla('edicion');
	}	
	

	function evt__filtro__filtrar($datos)
	{
		$this->filtro = $datos;
	}

	function conf__filtro()
	{
		if (isset($this->filtro)) {
			return $this->filtro;
		}
	}

	function evt__filtro__cancelar()
	{
		unset($this->filtro);	
	}
	
	function evt__agregar()
	{
		$this->set_pantalla('edicion');		
	}
	
	function evt__cancelar()
	{
		$this->get_relacion()->resetear();
		parent::evt__cancelar();		
		$this->set_pantalla('seleccion');
	}
	
	function evt__guardar()
	{
		$this->get_relacion()->tabla('permiso')->set_columna_valor('proyecto', toba_editor::get_proyecto_cargado());
		$this->get_relacion()->sincronizar();
		$this->evt__cancelar();
	}
	
	function evt__eliminar()
	{
		$this->get_relacion()->eliminar();
		$this->set_pantalla('seleccion');
	}	

}

?>