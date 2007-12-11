<?php
class skins_ci extends toba_ci
{
	protected $proyecto;
	
	function ini()
	{
		if (toba_editor::acceso_recursivo()) {
			$this->proyecto = 'toba';
		} else {
			$this->proyecto = toba_editor::get_proyecto_cargado();
		}
	}
	
	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$cuadro->set_datos($this->dep('datos')->get_listado($this->proyecto));
	}

	function evt__cuadro__seleccion($datos)
	{
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
		$datos['proyecto'] = $this->proyecto;
		$this->dep('datos')->nueva_fila($datos);
		$this->dep('datos')->sincronizar();
		$this->resetear();
	}

	function evt__formulario__baja()
	{
		$this->dep('datos')->eliminar_filas();
		$this->dep('datos')->sincronizar();
		$this->resetear();
	}

	function evt__formulario__modificacion($datos)
	{
		$datos['proyecto'] = $this->proyecto;		
		$this->dep('datos')->set($datos);
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