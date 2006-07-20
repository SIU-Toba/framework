<?php 
require_once('modelo/catalogo_modelo.php');

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
		if (isset($this->datos['usuario']) ) {
			try {
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
		$this->datos = $datos;
	}

	function evt__datos__carga()
	{
		if (isset($this->datos)) {
			if (isset($this->datos['clave'])) {
				unset($this->datos['clave']);
			}
			return $this->datos;	
		}
	}

	//---- seleccion_usuario -------------------------------------------------------

	function evt__seleccion_usuario__seleccion($seleccion)
	{
		$this->datos['usuario'] = $seleccion['usuario'];
		$this->datos['clave'] = null;
	}

	function evt__seleccion_usuario__carga()
	{
		return info_instancia::get_lista_usuarios();
	}
	
	//-------------------------------------------------------------------
}
?>