<?php 

class ci_ini_sesion extends toba_ci
{
	function ini()
	{
		toba_ci::set_navegacion_ajax(false);
	}
	
	function evt__form__modificacion($datos)
	{
		if (isset($datos)) {
			try {
				toba::manejador_sesiones()->iniciar_sesion_proyecto($datos);
			} catch ( toba_error_login $e ) {
				toba::notificacion()->agregar($e->getMessage());
			}
		}		
	}

	function evt__form__ingresar($datos)
	{
		$this->evt__form__modificacion($datos);
	}
	
	function conf__form()
	{
		return array('instancia' => toba::instancia()->get_id());
	}
	

	//--- COMBOS ----------------------------------------------------------------

	function get_lista_proyectos()
	{
		$instancia_id = toba::instancia()->get_id();
		$instancia = toba_modelo_catalogo::instanciacion()->get_instancia($instancia_id, new toba_mock_proceso_gui);
		$proyectos = $instancia->get_lista_proyectos_vinculados();
		$datos = array();
		$a = 0;
		foreach ($proyectos as $x) {
			$datos[$a]['id'] = $x;
			$datos[$a]['desc'] = $x;
			$a++;
		}
		return $datos;
	}
}
?>