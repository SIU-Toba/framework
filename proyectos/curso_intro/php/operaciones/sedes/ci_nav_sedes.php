<?php

class ci_nav_sedes extends toba_ci
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
		$this->dep('relacion')->eliminar_todo();
		$this->set_pantalla('seleccion');
	}

	function evt__guardar()
	{
		$this->dep('relacion')->sincronizar();
		$this->dep('relacion')->resetear();
		$this->set_pantalla('seleccion');
	}

	function evt__volver()
	{
		$this->dep('relacion')->resetear();
		$this->set_pantalla('seleccion');
	}

	//-----------------------------------------------------------------------------------
	//---- Configuracion de pantallas ---------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__edicion($pantalla)
	{
		if (!$this->dep('relacion')->esta_cargada()) {
			$pantalla->eliminar_evento('eliminar');
		}
	}

	//-----------------------------------------------------------------------------------
	//---- DEPENDENCIAS -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	//---- cuadro_sedes -----------------------------------------------------------------

	function evt__cuadro_sedes__seleccion($seleccion)
	{
		$this->dep('relacion')->cargar($seleccion);
		$this->set_pantalla('edicion');
	}

	function conf__cuadro_sedes($componente)
	{
		if (isset($this->s__filtro_sedes)) {
			$where = $this->dep('filtro_sedes')->get_sql_where();	
			$datos = toba::consulta_php('consultas')->get_sedes($where);
			$componente->set_datos($datos);
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
		if (isset($this->s__filtro_sedes)) {
			$componente->set_datos($this->s__filtro_sedes);
		}
	}
}
?>