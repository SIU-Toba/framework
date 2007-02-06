<?php
php_referencia::instancia()->agregar(__FILE__);
require_once('operaciones_simples/consultas.php'); 

class ci_abm_juegos extends toba_ci
{
	function resetear()
	{
		$this->dep('datos')->resetear();
	}

	//-------------------------------------------------------------------
	//--- Dependencias
	//-------------------------------------------------------------------

	//-- CUADRO --
	
	function conf__cuadro()
	{
		return consultas::get_juegos();
	}

	function evt__cuadro__seleccion($seleccion)
	{
		$this->dep('datos')->cargar($seleccion);
	}

	//-- FORMULARIO

	function conf__formulario()
	{
		if ($this->dep('datos')->esta_cargada()) {
			return $this->dep('datos')->get();	
		}
	}

	function evt__formulario__alta($datos)
	{
		$this->dep('datos')->nueva_fila($datos);
		$this->dep('datos')->sincronizar();
		$this->resetear();
	}

	function evt__formulario__modificacion($datos)
	{
		$this->dep('datos')->set($datos);
		$this->dep('datos')->sincronizar();
		$this->resetear();
	}

	function evt__formulario__baja()
	{
		$this->dep('datos')->eliminar_filas();
		$this->dep('datos')->sincronizar();
		$this->resetear();
	}

	function evt__formulario__cancelar()
	{
		$this->resetear();		
	}
}
?>