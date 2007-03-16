<?php 
require_once('modelo/catalogo_modelo.php');

class ci_ini_sesion extends toba_ci
{
	function evt__form__modificacion($datos)
	{
		if ( isset($datos)) {
			try {
				toba::manejador_sesiones()->iniciar_sesion_proyecto($datos['instancia']);
			} catch ( toba_error_login $e ) {
				toba::notificacion()->agregar( $e->getMessage() );
			}
		}		
	}

	function evt__form__ingresar($datos)
	{
		$this->evt__form__modificacion($datos);
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
}
?>