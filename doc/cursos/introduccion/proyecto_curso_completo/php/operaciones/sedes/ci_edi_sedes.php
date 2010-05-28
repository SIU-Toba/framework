<?php

class ci_edi_sedes extends toba_ci
{
	function relacion()
	{
		return $this->controlador->dep('relacion');
	}

	function get_lista_uas()
	{
		$datos = $this->relacion()->tabla('sede')->get();
		return toba::consulta_php('consultas')->get_ua($datos['id_institucion']);
	}
	
	//-----------------------------------------------------------------------------------
	//---- DEPENDENCIAS -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	//---- form_sedes -------------------------------------------------------------------

	function evt__form_sedes__modificacion($datos)
	{
		$this->relacion()->tabla('sede')->set($datos);
	}

	function conf__form_sedes($componente)
	{
		$datos = $this->relacion()->tabla('sede')->get();
		
		//Como el dato de pais y provincia no forma parte de la tabla, lo cargo manualmente
		if (isset($datos['cp']) && $datos['cp'] != null) {
			$datos['id_pais'] = toba::consulta_php('consultas')->get_pais_localidad($datos['cp']);
			$datos['id_provincia'] = toba::consulta_php('consultas')->get_provincia_localidad($datos['cp']);
		}
		$componente->set_datos($datos);
		if ($this->relacion()->esta_cargada()) {
			$componente->ef('id_institucion')->set_solo_lectura();
		}
	}

	//---- cuadro_edificios ---------------------------------------------------------------

	function evt__cuadro_edificios__seleccion($seleccion)
	{
		$this->relacion()->tabla('sede_edificio')->set_cursor($seleccion);
	}

	function conf__cuadro_edificios($componente)
	{
		$componente->set_datos($this->relacion()->tabla('sede_edificio')->get_filas());
	}

	//---- form_edificios ---------------------------------------------------------------

	function evt__form_edificios__alta($datos)
	{
		$this->relacion()->tabla('sede_edificio')->nueva_fila($datos);
	}

	function evt__form_edificios__baja()
	{
		$this->relacion()->tabla('sede_edificio')->set(null);
	}

	function evt__form_edificios__modificacion($datos)
	{
		$this->relacion()->tabla('sede_edificio')->set($datos);
		$this->evt__form_edificios__cancelar();
	}

	function evt__form_edificios__cancelar()
	{
		$this->relacion()->tabla('sede_edificio')->resetear_cursor();
	}

	function conf__form_edificios($componente)
	{
		if ($this->relacion()->tabla('sede_edificio')->hay_cursor()) {
			$componente->set_datos($this->relacion()->tabla('sede_edificio')->get());
		}
	}

	//---- form_uas ---------------------------------------------------------------------

	function evt__form_uas__modificacion($datos)
	{
		$this->relacion()->tabla('sede_ua')->procesar_filas($datos);
	}

	function conf__form_uas($componente)
	{
		$componente->set_datos($this->relacion()->tabla('sede_ua')->get_filas());
	}
}
?>