<?php 

class ci_editor extends toba_ci
{
	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__agregar()
	{
		$this->set_pantalla('edicion');
		$this->dep('datos')->tabla('clase')->set(array('proyecto'=>'toba'));		
	}

	function evt__eliminar()
	{
		$this->dep('datos')->eliminar_todo();
		$this->set_pantalla('seleccion');
	}

	function evt__guardar()
	{
		$this->dep('datos')->sincronizar();
		$this->dep('datos')->resetear();
		$this->set_pantalla('seleccion');
	}

	function evt__cancelar()
	{
		$this->dep('datos')->resetear();
		$this->set_pantalla('seleccion');
	}

	//-----------------------------------------------------------------------------------
	//---- DEPENDENCIAS -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	//---- cuadro -----------------------------------------------------------------------

	function evt__cuadro__seleccionar($seleccion)
	{
		$this->dep('datos')->cargar($seleccion);
		$this->set_pantalla('edicion');
	}

	function conf__cuadro($componente)
	{
		return toba_info_editores::get_info_tipos_componente(null, false);
	}

	//---- form_clase -------------------------------------------------------------------

	function evt__form_clase__modificacion($datos)
	{
		if (!isset($datos['editor_proyecto'])) {
			$datos['editor_proyecto'] = toba_editor::get_proyecto_cargado();
		}
		$this->dep('datos')->tabla('clase')->set($datos);
	}

	function conf__form_clase($componente)
	{
		if ($this->dep('datos')->esta_cargada()) {
			$componente->ef('clase')->set_solo_lectura();
		}
		$datos = $this->dep('datos')->tabla('clase')->get();
		if (isset($datos['editor_item'])) {
			$datos['editor_item_carpeta'] = toba_info_editores::get_carpeta_de_item($datos['editor_item'], $datos['editor_proyecto']);
		}
		$componente->set_datos($datos);
	}

	//---- form_relaciones --------------------------------------------------------------

	function evt__form_relaciones__modificacion($datos)
	{
		$this->dep('datos')->tabla('relaciones')->procesar_filas($datos);
	}

	function conf__form_relaciones($componente)
	{
		$componente->set_datos($this->dep('datos')->tabla('relaciones')->get_filas());
	}
}

?>