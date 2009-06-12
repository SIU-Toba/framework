<?php 

class ci_login extends toba_ci
{
	protected $s__datos;
	protected $en_popup = false;
	protected $s__item_inicio;
	
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
		$this->s__item_inicio = null;
	}

	function ini()
	{
		toba_ci::set_navegacion_ajax(false);
		$this->en_popup = toba::proyecto()->get_parametro('item_pre_sesion_popup');		
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
				//-- Caso error de validación
				toba::notificacion()->agregar($e->getMessage());
			} catch (toba_reset_nucleo $reset) {
				//-- Caso validacion exitosa, se redirige solo si no es popup
				if (! $this->en_popup) {
					throw $reset;
				}
				$this->s__item_inicio = $reset->get_item();	//Se guarda el item de inicio al que queria derivar el nucleo
			}
		}
	}

	function conf()
	{
		if ( ! toba::proyecto()->get_parametro('validacion_debug') ) {
			$this->pantalla()->eliminar_dep('seleccion_usuario');
		}
		if ($this->en_popup && toba::manejador_sesiones()->existe_usuario_activo()) {
			//Si ya esta logueado y se abre el sistema en popup, ocultar componentes visuales
			$this->pantalla()->set_titulo('');			
			if ($this->pantalla()->existe_dependencia('seleccion_usuario')) {
				$this->pantalla()->eliminar_dep('seleccion_usuario');
			}
			if ($this->pantalla()->existe_dependencia('datos')) {
				$this->pantalla()->eliminar_dep('datos');
			}			
			if ($this->pantalla()->existe_evento('Ingresar')) {
				$this->pantalla()->eliminar_evento("Ingresar");
			}
		}		
	}	
	
	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	//---- datos -------------------------------------------------------

	function evt__datos__modificacion($datos)
	{
		toba::logger()->desactivar();
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
		return toba::instancia()->get_lista_usuarios();
	}
	
	//-------------------------------------------------------------------
	
	function extender_objeto_js()
	{
		$finalizar = toba::memoria()->get_parametro(apex_sesion_qs_finalizar);
		if ($this->en_popup) {
			//Si cierra la sesión y es popup, cierra la ventana y al parent (si existe) lo recarga			
			if (isset($finalizar)) {
				echo "
					if (window.opener &&  window.opener.location) {
						window.opener.location.href = window.opener.location.href; 
					}
					window.close();
				";
			}
			if (toba::manejador_sesiones()->existe_usuario_activo()) {
				//Si ya esta logueado y se abre el sistema en popup, abrirlo
				if (isset($this->s__item_inicio)) {
					list($proyecto, $item) = explode($this->s__item_inicio);
				} else {
					$proyecto = toba::proyecto()->get_id();
					$item = toba::proyecto()->get_parametro('item_inicio_sesion');
				}
				$url = toba::vinculador()->get_url($proyecto, $item);
				echo "
					abrir_popup('sistema', '$url', {resizable: 1});
				";
			}
		}		
	}
}
?>