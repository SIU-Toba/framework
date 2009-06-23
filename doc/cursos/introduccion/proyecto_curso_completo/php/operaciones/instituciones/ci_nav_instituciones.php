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
	//---- Pantallas ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__edicion()
	{
		if (!$this->dep('datos')->esta_cargada() ) {
			$this->pantalla()->eliminar_evento('eliminar');	
		}
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
			$componente->set_datos($this->s__filtro_institucion);
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
			$where = $this->dep('filtro_instituciones')->get_sql_where();	
			$datos = toba::consulta_php('soe_consultas')->get_instituciones($where);
		}else{
			$datos = toba::consulta_php('soe_consultas')->get_instituciones();
		}
		$componente->set_datos($datos);
	}
}
?>