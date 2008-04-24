<?php 
class ci_navegacion extends toba_ci
{
	protected $s__filtro;
	
	function ini__operacion()
	{
		$this->s__filtro['proyecto'] = toba::sesion()->get_id_proyecto();
	}
		
	function conf__seleccion($pantalla)
	{
		if( toba::sesion()->proyecto_esta_predefinido() ) {
			$this->pantalla()->eliminar_dep('filtro');
		}
		if (!isset($this->s__filtro)) {
			$pantalla->eliminar_evento('agregar');
		}
	}
	
	function conf__edicion($pantalla)
	{
		if ( ! $this->dep('datos')->esta_cargada() ) {
			$pantalla->eliminar_evento('eliminar');
		}
	}
	
	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__agregar()
	{
		$this->dep('editor')->set_proyecto($this->s__filtro['proyecto']);
		$this->set_pantalla('edicion');
	}

	function evt__cancelar()
	{
		$this->dep('datos')->resetear();
		$this->set_pantalla('seleccion');
	}

	function evt__eliminar()
	{
		$this->dep('datos')->eliminar_todo();
		$this->dep('datos')->resetear();
		$this->set_pantalla('seleccion');
	}

	function evt__guardar()
	{
		$this->dep('datos')->sincronizar();
		$this->dep('datos')->resetear();
		$this->set_pantalla('seleccion');
	}


	//-----------------------------------------------------------------------------------
	//---- DEPENDENCIAS -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	//---- cuadro -----------------------------------------------------------------------

	function evt__cuadro__seleccion($seleccion)
	{
		$this->dep('editor')->set_proyecto($this->s__filtro['proyecto']);
		$this->dep('datos')->cargar($seleccion);
		$this->set_pantalla('edicion');
	}

	function conf__cuadro($componente)
	{
		if (isset($this->s__filtro)) {
			$datos = consultas_instancia::get_lista_perfil_datos($this->s__filtro['proyecto']);
			$componente->set_datos($datos);
		}
	}


	//---- filtro -----------------------------------------------------------------------

	function evt__filtro__filtrar($datos)
	{
		$this->s__filtro = $datos;
	}
	
	function evt__filtro__cancelar()
	{
		unset($this->s__filtro);
	}
	
	function conf__filtro($componente)
	{
		if (isset($this->s__filtro)) {
			$componente->set_datos($this->s__filtro);
		}		
	}
}

?>