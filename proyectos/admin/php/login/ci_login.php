<?php 
require_once('modelo/catalogo_modelo.php');
require_once('modelo/lib/gui.php');

class ci_login extends objeto_ci
{
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = 'datos';
		return $propiedades;
	}

	function get_lista_ei()
	{
		$ei = parent::get_lista_ei();
		if ( ! info_proyecto::instancia()->get_parametro('validacion_debug') ) {
			unset($ei);
			$ei[] = 'datos';
		}
		return $ei;
	}

	function evt__post_recuperar_interaccion()
	{
		//ei_arbol( $this->datos );
		if ( false || ( isset($this->datos['instancia']) && isset($this->datos['proyecto']) && isset($this->datos['usuario']) ) ) {
			try {
				editor::iniciar($this->datos['instancia'], $this->datos['proyecto']);
				toba::get_sesion()->iniciar($this->datos['usuario'], $this->datos['clave']);
			} catch ( excepcion_toba_login $e ) {
				toba::get_cola_mensajes()->agregar( $e->getMessage() );
			}
		}
	}

	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	//---- datos -------------------------------------------------------

	function evt__datos__modificacion($datos)
	{
		$datos['instancia'] = apex_pa_instancia; //temp
		$this->datos = $datos;
	}

	function evt__datos__carga()
	{
		$this->datos['instancia'] = apex_pa_instancia;
		if (isset($this->datos['clave'])) {
			unset($this->datos['clave']);
		}
		return $this->datos;	
	}

	//---- seleccion_usuario -------------------------------------------------------

	function evt__seleccion_usuario__seleccion($seleccion)
	{
		$this->datos['usuario'] = $seleccion['usuario'];
		$this->datos['clave'] = null;
	}

	function evt__seleccion_usuario__carga()
	{
		return datos_acceso::get_lista_usuarios();
	}

	//--- COMBOS ----------------------------------------------------------------

	function get_lista_instancias()
	{
		$instancias = instancia::get_lista();
		$datos = array();
		$a = 0;
		foreach( $instancias as $x) {
			$datos[$a]['id'] = $x;
			$datos[$a]['desc'] = $x;
			$a++;
		}
		return $datos;
	}
	
	function get_lista_proyectos($instancia=apex_pa_instancia)
	{
		$instancia = catalogo_modelo::instanciacion()->get_instancia($instancia, new mock_gui);
		$proyectos = $instancia->get_lista_proyectos_vinculados();
		$datos = array();
		$a = 0;
		foreach( $proyectos as $x) {
			$datos[$a]['id'] = $x;
			$datos[$a]['desc'] = $x;
			$a++;
		}
		return $datos;
	}
	//-------------------------------------------------------------------
}
?>