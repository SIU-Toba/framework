<?php 
require_once('modelo/catalogo_modelo.php');

class ci_login extends toba_ci
{
	protected $s__datos;
	
	function post_eventos()
	{
		if (isset($this->s__datos['usuario']) ) {
			if (!isset($this->s__datos['clave'])) {
				$this->s__datos['clave'] = null;
			}			
			try {
				toba::sesion()->iniciar($this->s__datos['usuario'], $this->s__datos['clave']);
			} catch ( toba_error_login $e ) {
				toba::notificacion()->agregar( $e->getMessage() );
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

	function conf__datos()
	{
		if (isset($this->s__datos)) {
			if (isset($this->s__datos['clave'])) {
				unset($this->s__datos['clave']);
			}
			return $this->s__datos;	
		}
	}

	//---- seleccion_usuario -------------------------------------------------------

	function evt__seleccion_usuario__seleccion($seleccion)
	{
		$this->s__datos['usuario'] = $seleccion['usuario'];
		$this->s__datos['clave'] = null;
	}

	function conf__seleccion_usuario()
	{
		return toba_instancia::get_lista_usuarios();
	}
	
	//-------------------------------------------------------------------
}
?>