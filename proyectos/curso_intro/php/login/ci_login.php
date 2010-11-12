<?php 

class ci_login extends toba_ci
{
	protected $s__datos;
	
	/**
	 * Guarda el id de la operación original así se hace una redirección una vez logueado
	 */
	function ini__operacion()
	{
		//--- Si el usuario pidio originalmente algún item distinto al de login, se fuerza como item de inicio de sesión
		$item_original = toba::memoria()->get_item_solicitado_original();
		$item_actual = toba::memoria()->get_item_solicitado();
		if (isset($item_original) && isset($item_actual) &&
				$item_actual[1] != $item_original[1]) {
			toba::proyecto()->set_parametro('item_inicio_sesion', $item_original[1]);
		}		
	}	
	
	function post_eventos()
	{
		if (isset($this->s__datos['usuario']) ) {
			if (!isset($this->s__datos['clave'])) {
				$this->s__datos['clave'] = null;
			}			
			try {
				toba::manejador_sesiones()->login($this->s__datos['usuario'], $this->s__datos['clave']);
			} catch (toba_error_autenticacion $e) {
				toba::notificacion()->agregar($e->getMessage());
			}
		}
	}

	function conf()
	{
		if ( ! toba::proyecto()->get_parametro('validacion_debug') ) {
			$this->pantalla()->eliminar_dep('seleccion_usuario');
		}
	}	
	
	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	//---- datos -------------------------------------------------------

	function evt__datos__modificacion($datos)
	{
		$this->s__datos = $datos;
	}

	function conf__datos($componente)
	{
		if (isset($this->s__datos)) {
			if (isset($this->s__datos['clave'])) {
				unset($this->s__datos['clave']);
			}
			$componente->set_datos($this->s__datos);	
		}
	}

	//---- seleccion_usuario -------------------------------------------------------

	function evt__seleccion_usuario__seleccion($seleccion)
	{
		$this->s__datos['usuario'] = $seleccion['usuario'];
		$this->s__datos['clave'] = null;
	}

	function conf__seleccion_usuario($componente)
	{
		$componente->set_datos(toba_instancia::get_lista_usuarios());
	}
	
	//-------------------------------------------------------------------
}
?>