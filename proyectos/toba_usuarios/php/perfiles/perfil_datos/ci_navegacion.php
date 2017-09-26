<?php 
class ci_navegacion extends toba_ci
{
	protected $s__filtro;

	function ini__operacion()
	{
		if (! is_null(admin_instancia::get_proyecto_defecto())) {
			$this->s__filtro = array('proyecto' => admin_instancia::get_proyecto_defecto());
		}
	}
		
	function conf__seleccion($pantalla)
	{
		if (!isset($this->s__filtro)) {
			$pantalla->eliminar_evento('agregar');
		}
	}
	
	function conf__edicion($pantalla)
	{
		if ( ! $this->dep('datos')->esta_cargada() ) {
			$pantalla->eliminar_evento('eliminar');
		}
		//-- Si es una instalación de producción avisar que los cambios se aplicaran solo a esta instalacion y no al proyecto/personalizacion
		admin_instancia::chequear_usar_perfiles_propios($this->s__filtro['proyecto'], $this->pantalla());		
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
		//-- Si estamos en produccion guardamos un flag indicando que cambio la instancia		
		admin_instancia::set_usar_perfiles_propios($this->s__filtro['proyecto']);		
	}

	function evt__guardar()
	{
		$this->dep('datos')->persistidor()->set_usar_trim(false);
		$this->dep('datos')->sincronizar();
		$this->dep('datos')->resetear();
		$this->set_pantalla('seleccion');
		
		//-- Si estamos en produccion guardamos un flag indicando que cambio la instancia		
		admin_instancia::set_usar_perfiles_propios($this->s__filtro['proyecto']);
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