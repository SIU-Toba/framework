<?php
require_once("nucleo/browser/clases/objeto_ci.php");

class ci_abm extends objeto_ci 
{
	protected $seleccion;
	protected $estado_filtro;
	private $tabla;
	
	private function get_tabla() 
	{
		if(!isset($this->tabla)) {
			$this->cargar_dependencia("datos");
			$this->tabla = $this->dependencias["datos"];			
		}
		return $this->tabla;		
	}
/*
	function destruir()
	{
		parent::destruir();
		ei_arbol($this->get_estado_sesion());
		ei_arbol($this->get_tabla()->get_datos());		
	}
*/
	function mantener_estado_sesion() 
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "seleccion";
		return $propiedades;
	}

	/*
		---------  CUADRO  -------------
	*/

	function evt__cuadro__seleccion($id)
	{
		$this->seleccion = $id;
	}

	function evt__cuadro__carga()
	{
		return dao_general::get_instituciones();
	}	

	/*
		--------  FORMULARIO  ------------
	*/
	
	function evt__formulario__alta($datos) 
	{
		$this->get_tabla()->set($datos); // shortcut para nueva_fila
		$this->get_tabla()->sincronizar();		
		$this->get_tabla()->resetear();		
	}
	
	function evt__formulario__modificacion($datos)
	{
		$this->get_tabla()->set($datos);
		$this->get_tabla()->sincronizar();
		$this->get_tabla()->resetear();		
	}
	
	function evt__formulario__baja()
	{
		$this->get_tabla()->eliminar_filas();		
		$this->get_tabla()->sincronizar();
		$this->get_tabla()->resetear();		
	}	

	function evt__formulario__carga()
	{
		if(isset($this->seleccion)) {
			$this->get_tabla()->cargar($this->seleccion);
			return $this->get_tabla()->get(); 		
		}
	}	
}
?>
