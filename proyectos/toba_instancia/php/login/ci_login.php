<?php 
require_once('modelo/catalogo_modelo.php');
require_once('modelo/lib/gui.php');

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
		parent::generar_html();	
	}	
	
}

class ci_login extends toba_ci
{
	protected $s__datos = array();

	function conf()
	{
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
		$this->s__datos = $datos;
		if ( toba::proyecto()->get_parametro('validacion_debug') ) {
			if ( $this->s__datos['autologin'] ) {
				$this->s__datos['usuario'] = $this->s__datos['autologin'];
			}
		}
		
		if ( isset($this->s__datos['instancia']) && isset($this->s__datos['proyecto']) && isset($this->s__datos['usuario']) ) {
			if (!isset($this->s__datos['clave'])) {
				$this->s__datos['clave'] = null;
			}			
			try {
				//toba_editor::iniciar($this->s__datos['instancia'], $this->s__datos['proyecto']);
				toba::sesion()->iniciar($this->s__datos['usuario'], $this->s__datos['clave']);
			} catch ( toba_error_login $e ) {
				echo "AACA";
				toba::notificacion()->agregar( $e->getMessage() );
			}
		}		
	}

	function conf__datos()
	{	
		if ( toba::proyecto()->get_parametro('validacion_debug') ) {
			$this->dependencia('datos')->desactivar_efs( array('usuario','clave') );
		} else {
			$this->dependencia('datos')->desactivar_efs('autologin');
		}
		if (isset($this->s__datos['clave'])) {
			unset($this->s__datos['clave']);
		}
		return $this->s__datos;	
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
	
	function get_lista_usuarios($instancia_id)
	{
		$instancia = catalogo_modelo::instanciacion()->get_instancia($instancia_id, new mock_gui);
		$usuarios = $instancia->get_lista_usuarios();
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