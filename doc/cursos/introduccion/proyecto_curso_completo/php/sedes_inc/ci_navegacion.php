<?php

class ci_navegacion extends toba_ci
{
	protected $s__filtro_sedes;

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

	//---- cuadro_sedes -----------------------------------------------------------------

	function evt__cuadro_sedes__seleccion($seleccion)
	{
		$this->dep('datos')->cargar($seleccion);
		$this->set_pantalla('edicion');
	}

	function conf__cuadro_sedes($componente)
	{
		if(isset($this->s__filtro_sedes)){
			$componente->set_datos(soe_consultas::get_sedes($this->s__filtro_sedes));
		}
	}

	//---- filtro_sedes -----------------------------------------------------------------

	function evt__filtro_sedes__filtrar($datos)
	{
		$this->s__filtro_sedes = $datos;
	}

	function evt__filtro_sedes__cancelar()
	{
		unset($this->s__filtro_sedes);
	}

	function conf__filtro_sedes($componente)
	{
		if(isset($this->s__filtro_sedes)){
			$componente->set_datos( $this->s__filtro_sedes );
		}
	}
}
?>