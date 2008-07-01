<?php
class ci_prueba extends toba_ci
{
	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$cuadro->set_datos($this->dep('datos')->get_listado());
	}

	function evt__cuadro__seleccion($datos)
	{
		$datos = array('clave' => 'valor');
		$this->dep('datos')->cargar($datos);
	}

	//---- Formulario -------------------------------------------------------------------

	function conf__formulario(toba_ei_formulario $form)
	{
		if($this->dep('datos')->esta_cargada()){
			$form->set_datos($this->dep('datos')->get());
		}
	}

	function evt__formulario__alta($datos)
	{
		$this->dep('datos')->set($datos);
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

	function resetear()
	{
		$this->dep('datos')->resetear();
	}
}

?>