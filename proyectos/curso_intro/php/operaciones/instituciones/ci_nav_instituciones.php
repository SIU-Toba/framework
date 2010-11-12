<?php
class ci_nav_instituciones extends toba_ci
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
	//---- Pantallas --------------------------------------------------------------------
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

	//---- filtro_instituciones ---------------------------------------------------------

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
		if (isset($this->s__filtro_institucion)) {
			$componente->set_datos($this->s__filtro_institucion);
		}
	}

	//---- cuadro_instituciones ---------------------------------------------------------

	function evt__cuadro_instituciones__seleccion($seleccion)
	{
		$this->dep('relacion')->cargar($seleccion);
		$this->set_pantalla('edicion');
	}

	function conf__cuadro_instituciones($componente)
	{
		$where = $this->dep('filtro_instituciones')->get_sql_where();
		$datos = toba::consulta_php('consultas')->get_instituciones($where);
		$componente->set_datos($datos);
	}
}
?>