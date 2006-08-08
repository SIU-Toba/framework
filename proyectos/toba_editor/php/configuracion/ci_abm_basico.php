<?php 

class ci_abm_basico extends objeto_ci
{
	//---- cuadro -------------------------------------------------------

	function evt__cuadro__seleccion($seleccion)
	{
		$this->dependencia('datos')->cargar($seleccion);
	}

	function conf__cuadro()
	{
		return $this->get_datos_listado();
	}

	//---- form -------------------------------------------------------

	function evt__formulario__alta($datos)
	{
		$datos['proyecto'] = editor::get_proyecto_cargado();
		$this->dependencia('datos')->set($datos);
		$this->dependencia('datos')->sincronizar();
		$this->dependencia('datos')->resetear();
	}

	function evt__formulario__baja()
	{
		$this->dependencia('datos')->eliminar_filas();
		$this->dependencia('datos')->sincronizar();
		$this->dependencia('datos')->resetear();
	}

	function evt__formulario__modificacion($datos)
	{
		$datos['proyecto'] = editor::get_proyecto_cargado();
		$this->dependencia('datos')->set($datos);
		$this->dependencia('datos')->sincronizar();
		$this->dependencia('datos')->resetear();
	}

	function evt__formulario__cancelar()
	{
		$this->dependencia('datos')->resetear();
	}

	function conf__formulario()
	{
		if( $this->dependencia('datos')->hay_cursor() ) {
			return $this->dependencia('datos')->get();
		}
	}
}
?>