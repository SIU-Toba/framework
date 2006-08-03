<?php
require_once('nucleo/componentes/interface/objeto_ci.php'); 
require_once('modelo/consultas/dao_permisos.php');
//--------------------------------------------------------------------
class ci_navegacion extends objeto_ci
{
	protected $pantalla = "seleccion";
	protected $seleccion;
	protected $filtro = array();
	
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "pantalla";
		$propiedades[] = "seleccion";
		$propiedades[] = "filtro";
		return $propiedades;
	}
	
	/**
	 * @return objeto_datos_relacion
	 */
	function get_relacion()
	{
		return $this->dependencia('datos');
	}	
	
	function evt__listado__carga()
	{
		$filtro = (isset($this->filtro)) ? $this->filtro : array();
		return dao_permisos::get_lista_permisos($filtro);
	}

	function evt__listado__seleccion($id)
	{
		$this->seleccion = $id;
		$this->get_relacion()->cargar($this->seleccion);
		$this->pantalla = 'edicion';
	}	
	

	function evt__filtro__filtrar($datos)
	{
		$this->filtro = $datos;
	}

	function evt__filtro__carga()
	{
		if(isset($this->filtro)){
			return $this->filtro;
		}
	}

	function evt__filtro__cancelar(){
		unset($this->filtro);	
	}
	
	function get_pantalla_actual()
	{
		return $this->pantalla;
	}
	
	function evt__agregar()
	{
		$this->pantalla = 'edicion';	
	}
	
	function evt__cancelar()
	{
		$this->get_relacion()->resetear();
		parent::evt__cancelar();		
		$this->pantalla = 'seleccion';		
	}
	
	function evt__guardar()
	{
		$this->get_relacion()->tabla('permiso')->set_columna_valor('proyecto', editor::get_proyecto_cargado());
		$this->get_relacion()->sincronizar();
		$this->evt__cancelar();
	}
	
	function evt__eliminar()
	{
		$this->get_relacion()->eliminar();
		$this->pantalla = 'seleccion';
	}	

}

?>