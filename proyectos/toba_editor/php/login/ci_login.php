<?php 
require_once('modelo/catalogo_modelo.php');
require_once('modelo/lib/gui.php');

class pantalla_login extends objeto_ei_pantalla 
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
		echo js::ejecutar($codigo_js);
		parent::generar_html();	
	}	
	
}

class ci_login extends objeto_ci
{
	protected $datos = array();

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = 'datos';
		return $propiedades;
	}

	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	//---- datos -------------------------------------------------------

	function evt__datos__modificacion($datos)
	{
		$this->datos = $datos;
		if ( info_proyecto::instancia()->get_parametro('validacion_debug') ) {
			if ( $this->datos['autologin'] ) {
				$this->datos['usuario'] = $this->datos['autologin'];
			}
		}
		
		if ( isset($this->datos['instancia']) && isset($this->datos['proyecto']) && isset($this->datos['usuario']) ) {
			if (!isset($this->datos['clave'])) {
				$this->datos['clave'] = null;
			}			
			try {
				editor::iniciar($this->datos['instancia'], $this->datos['proyecto']);
				toba::get_sesion()->iniciar($this->datos['usuario'], $this->datos['clave']);
			} catch ( excepcion_toba_login $e ) {
				toba::get_cola_mensajes()->agregar( $e->getMessage() );
			}
		}		
	}

	function conf__datos()
	{	
		if ( info_proyecto::instancia()->get_parametro('validacion_debug') ) {
			$this->dependencia('datos')->desactivar_efs( array('usuario','clave') );
		} else {
			$this->dependencia('datos')->desactivar_efs('autologin');
		}
		if (isset($this->datos['clave'])) {
			unset($this->datos['clave']);
		}
		return $this->datos;	
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
	
	function get_lista_proyectos($instancia_id)
	{
		$instancia = catalogo_modelo::instanciacion()->get_instancia($instancia_id, new mock_gui);
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

	function get_lista_usuarios($instancia_id, $proyecto_id)
	{
		$instancia = catalogo_modelo::instanciacion()->get_instancia($instancia_id, new mock_gui);
		$usuarios = $instancia->get_lista_usuarios($proyecto_id);
		$datos = array();
		$a = 0;
		foreach( $usuarios as $x => $desc) {
			$datos[$a]['id'] = $desc['usuario'];
			$datos[$a]['nombre'] = $desc['usuario'] . ' - ' . $desc['nombre'];
			$a++;
		}
		return $datos;
	}
	//-------------------------------------------------------------------
	
}

?>