<?php

class ci_instituciones extends toba_ci
{
	protected $s__filtro_institucion;

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__agregar()
	{
		$this->set_pantalla('edicion');
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

	function evt__volver()
	{
		$this->dep('datos')->resetear();
		$this->set_pantalla('seleccion');
	}

	//-----------------------------------------------------------------------------------
	//---- DEPENDENCIAS -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	//---- filtro_instituciones -----------------------------------------------------------------

	function evt__filtro_instituciones__filtrar($datos)
	{
		$this->s__filtro_institucion = $datos;
	}

	function evt__filtro_instituciones__cancelar()
	{
		unset($this->s__filtro_institucion);
	}

	function conf__filtro_instituciones($componente)
	{
		if(isset($this->s__filtro_institucion)){
			return $this->s__filtro_institucion;
		}
	}
	
	//---- cuadro_instituciones ---------------------------------------------------------

	function evt__cuadro_instituciones__seleccion($seleccion)
	{
		$this->dep('datos')->cargar($seleccion);
		$this->set_pantalla('edicion');
	}

	function conf__cuadro_instituciones($componente)
	{
		if(isset($this->s__filtro_institucion)){
			$componente->set_datos(soe_consultas::get_instituciones($this->s__filtro_institucion));
		}else{
			$componente->set_datos(soe_consultas::get_instituciones());
		}
	}

	//---- form_institucion -------------------------------------------------------------

	function evt__form_institucion__modificacion($datos)
	{
		$this->dep('datos')->tabla('institucion')->set($datos);
	}

	function conf__form_institucion($componente)
	{
		$componente->set_datos($this->dep('datos')->tabla('institucion')->get());
	}

	//---- form_uas ---------------------------------------------------------------------

	function evt__form_uas__modificacion($datos)
	{
		$this->dep('datos')->tabla('uas')->procesar_filas($datos);
	}

	function conf__form_uas($componente)
	{
		$componente->set_datos($this->dep('datos')->tabla('uas')->get_filas());
	}
}
?>