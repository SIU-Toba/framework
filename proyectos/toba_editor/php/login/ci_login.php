<?php 

class pantalla_login extends toba_ei_pantalla  
{
	function generar_html()
	{
		// si se da un timeout, esta pagina puede cargarse en un frame...
		// esta funcion detecta este caso y lo soluciona
		$codigo_js = "
			if(self.name!=top.name)	{
				top.location.href='{$_SERVER['PHP_SELF']}';
			}
		";
		echo toba_js::ejecutar($codigo_js);
		echo "
			<style type='text/css'>
			.ci-barra-sup {
				-moz-border-radius:6px 6px 0 0;
				border-radius:6px 6px 0 0;
				-webkit-border-radius:6px 6px 0 0;
				padding: 3px;
					background-image: -webkit-gradient(
				    linear,
				    left top,
				    left bottom,
				    color-stop(0.5, #7485b3),
				    color-stop(0.5, #5368a1)
				);
				background-image: -moz-linear-gradient(
				    center top,
				    #7485b3 50%,
				    #5368a1 50%
				);
				margin-bottom: 3px;
								
								
			}
			.cuerpo {
				border-top: 2px solid black;

			}
			</style>
		";
		parent::generar_html();	
	}	
}

class ci_login extends toba_ci
{
	protected $s__datos = array();

	function conf()
	{
		toba_ci::set_navegacion_ajax(false);		
		if ( toba::proyecto()->get_parametro('validacion_debug') ) {
			$this->pantalla()->eliminar_evento('ingresar');
		}
	}

	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	//---- datos -------------------------------------------------------

	function evt__datos__modificacion($datos)
	{
		toba::logger()->desactivar();
		$datos['instancia'] = toba::instancia()->get_id();
		$this->s__datos = $datos;
		if ( ! isset($this->s__datos['instancia']) && isset($this->s__datos['proyecto']) 
				&& ( isset($this->s__datos['usuario']) || isset($this->s__datos['autologin'])) ) {
			toba::notificacion()->agregar('Es necesario completar todos los parametros.');
		}
		if ( toba::proyecto()->get_parametro('validacion_debug') ) {
			$this->s__datos['usuario'] = $this->s__datos['autologin'];
			$this->s__datos['clave'] = null;
		} else {
			if (!isset($this->s__datos['clave'])) {
				throw new toba_error('Es necesario ingresar la clave');
			}
		}
		try {
			$datos_editor['proyecto'] = $this->s__datos['proyecto'];
			toba::manejador_sesiones()->login($this->s__datos['usuario'], $this->s__datos['clave'], $datos_editor);
		} catch ( toba_error $e ) {
			//toba_editor::finalizar();
			toba::notificacion()->agregar($e->getMessage());
		}
	}

	function conf__datos()
	{	
		if ( toba::proyecto()->get_parametro('validacion_debug') ) {
			$this->dependencia('datos')->desactivar_efs(array('usuario', 'clave'));
		} else {
			$this->dependencia('datos')->desactivar_efs('autologin');
		}
		if (isset($this->s__datos['clave'])) {
			unset($this->s__datos['clave']);
		}
		if (!isset($this->s__datos['instancia'])) {
			$this->s__datos['instancia'] = toba::instancia()->get_id();
		}
		return $this->s__datos;	
	}

	//--- COMBOS ----------------------------------------------------------------

	function get_lista_usuarios()
	{
		$instancia = toba_modelo_catalogo::instanciacion()->get_instancia(toba::instancia()->get_id(), new toba_mock_proceso_gui);
		$usuarios = $instancia->get_lista_usuarios('toba_editor');
		$datos = array();
		$a = 0;
		foreach ($usuarios as $x => $desc) {
			$datos[$a]['id'] = $desc['usuario'];
			$datos[$a]['nombre'] = $desc['usuario'] . ' - ' . $desc['nombre'];
			$a++;
		}
		return $datos;
	}
	
	function get_lista_instancias()
	{
		$instancias = toba_modelo_instancia::get_lista();
		$datos = array();
		$a = 0;
		foreach ($instancias as $x) {
			$datos[$a]['id'] = $x;
			$datos[$a]['desc'] = $x;
			$a++;
		}
		return $datos;
	}
	
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
	//-------------------------------------------------------------------
}
?>