<?php

class ci_edi_sedes extends toba_ci
{
	function datos()
	{
		return $this->controlador->dep('datos');
	}	
	
	//-----------------------------------------------------------------------------------
	//---- DEPENDENCIAS -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	//---- form_sedes -------------------------------------------------------------------

	function evt__form_sedes__modificacion($datos)
	{
		$this->datos()->tabla('sede')->set($datos);
	}

	function conf__form_sedes($componente)
	{
		$componente->set_datos($this->datos()->tabla('sede')->get());
		if ($this->datos()->esta_cargada()) {
			$componente->ef('institucion')->set_solo_lectura();
		}
	}

	//---- cuadro_edificios ---------------------------------------------------------------

	function evt__cuadro_edificios__seleccion($seleccion)
	{
		$this->datos()->tabla('edificios')->set_cursor($seleccion);
	}

	function conf__cuadro_edificios($componente)
	{
		$componente->set_datos($this->datos()->tabla('edificios')->get_filas());
	}

	//---- form_edificios ---------------------------------------------------------------

	function evt__form_edificios__alta($datos)
	{
		$this->datos()->tabla('edificios')->nueva_fila($datos);
	}

	function evt__form_edificios__baja()
	{
		$this->datos()->tabla('edificios')->set(null);
	}

	function evt__form_edificios__modificacion($datos)
	{
		$this->datos()->tabla('edificios')->set($datos);
		$this->evt__form_edificios__cancelar();
	}

	function evt__form_edificios__cancelar()
	{
		$this->datos()->tabla('edificios')->resetear_cursor();
	}

	function conf__form_edificios($componente)
	{
		if ($this->datos()->tabla('edificios')->hay_cursor()) {
			$componente->set_datos($this->datos()->tabla('edificios')->get());
		}
	}

	//---- form_uas ---------------------------------------------------------------------

	function evt__form_uas__modificacion($datos)
	{
		$this->datos()->tabla('uas')->procesar_filas($datos);
	}

	function conf__form_uas($componente)
	{
		$componente->set_datos($this->datos()->tabla('uas')->get_filas());
	}

	function get_lista_uas()
	{
		$datos = $this->datos()->tabla('sede')->get();
		return toba::consulta_php('soe_consultas')->get_unidadacad($datos['institucion']);
	}
}
?>