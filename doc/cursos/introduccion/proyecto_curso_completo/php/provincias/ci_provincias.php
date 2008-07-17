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
		$this->dep('tabla_provincias')->cargar($seleccion);
	}

	//---- form -------------------------------------------------------------------------

	function conf__form($componente)
	{
		if ($this->dep('tabla_provincias')->esta_cargada()) {
			return $this->dep('tabla_provincias')->get();	
		}
	}

	function evt__form__alta($datos)
	{
		$this->dep('tabla_provincias')->set($datos);
		$this->dep('tabla_provincias')->sincronizar();
		$this->dep('tabla_provincias')->resetear();
	}
	
	function evt__form__modificacion($datos)
	{
		$this->dep('tabla_provincias')->set($datos);
		$this->dep('tabla_provincias')->sincronizar();
		$this->dep('tabla_provincias')->resetear();
	}	

	function evt__form__baja()
	{
		try {
			$this->dep('tabla_provincias')->set(null);
			$this->dep('tabla_provincias')->sincronizar();
			$this->dep('tabla_provincias')->resetear();
		} catch (toba_error $e) {
			toba::notificacion()->agregar('No es posible eliminar el registro.');
			$this->dep('tabla_provincias')->resetear();
		}
	}

	function evt__form__cancelar()
	{
		$this->dep('tabla_provincias')->resetear();
	}
}
?>