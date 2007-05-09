<?php 
class ci_provincias extends toba_ci
{
	//---- cuadro -----------------------------------------------------------------------

	function conf__cuadro($componente)
	{
		$componente->set_datos(soe_consultas::get_provincias());
	}
	
	function evt__cuadro__seleccion($seleccion)
	{
		$this->dep('datos')->cargar($seleccion);
	}

	//---- form -------------------------------------------------------------------------

	function conf__form($componente)
	{
		if ($this->dep('datos')->esta_cargada()) {
			return $this->dep('datos')->get();	
		}
	}

	function evt__form__alta($datos)
	{
		$this->dep('datos')->nueva_fila($datos);
		$this->dep('datos')->sincronizar();
		$this->dep('datos')->resetear();
	}

	function evt__form__baja()
	{
		try {
			$this->dep('datos')->eliminar_filas();
			$this->dep('datos')->sincronizar();
			$this->dep('datos')->resetear();
		} catch (toba_error $e) {
			toba::notificacion()->agregar('No es posible eliminar el registro.');
			$this->dep('datos')->resetear();
		}
	}

	function evt__form__modificacion($datos)
	{
		$this->dep('datos')->set($datos);
		$this->dep('datos')->sincronizar();
		$this->dep('datos')->resetear();
	}

	function evt__form__cancelar()
	{
		$this->dep('datos')->resetear();
	}
}
?>