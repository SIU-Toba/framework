<?php 
require_once('modelo/catalogo_modelo.php');
require_once('modelo/lib/gui.php');

class ci_login extends objeto_ci
{
	protected $datos;

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = 'datos';
		return $propiedades;
	}

	function destruir()
	{
		parent::destruir();
		//ei_arbol($_SESSION['toba']);
	}

	function get_lista_ei()
	{
		$ei = parent::get_lista_ei();
		if ( true || (! info_proyecto::instancia()->get_parametro('validacion_debug')) ) {
			unset($ei);
			$ei[] = 'datos';
		}
		return $ei;
	}

	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

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

	//---- datos -------------------------------------------------------

	function evt__datos__procesar($datos)
	{
		try {
			$datos['instancia'] = apex_pa_instancia; //temp
			editor::iniciar($datos['instancia'], $datos['proyecto']);
			toba::get_sesion()->iniciar($datos['usuario'], $datos['clave']);
		} catch ( excepcion_toba_login $e ) {
			$this->datos = $datos;
			toba::get_cola_mensajes()->agregar( $e->getMessage() );
		}
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
		try {
			toba::get_sesion()->iniciar($seleccion['usuario']);
		} catch ( excepcion_toba_login $e ) {
			toba::get_cola_mensajes()->agregar( $e->getMessage() );
		}
	}

	function evt__seleccion_usuario__carga()
	{
		return datos_acceso::get_lista_usuarios();
	}
}
?>