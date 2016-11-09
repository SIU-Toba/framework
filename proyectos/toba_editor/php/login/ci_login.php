<?php
class ci_login extends toba_ci
{
	protected $s__datos;
	protected $s__datos_openid;
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
		if (isset($item_original) && isset($item_actual) &&	$item_actual[1] != $item_original[1]) {
			toba::proyecto()->set_parametro('item_inicio_sesion', $item_original[1]);
		}
		$this->s__item_inicio = null;
		if (isset($this->s__datos_openid)) {
			unset($this->s__datos_openid);
		}
	}

	/*function ini()
	{
		toba_ci::set_navegacion_ajax(false);
		if (toba::instalacion()->get_tipo_autenticacion() == 'openid') {
			try {
				toba::manejador_sesiones()->get_autenticacion()->verificar_acceso();
			} catch (toba_error_autenticacion $e) {
				//-- Caso error de validación
				toba::notificacion()->agregar($e->getMessage());
			}
		}
		$tipo_auth = toba::instalacion()->get_tipo_autenticacion();
		if (toba_autenticacion::es_autenticacion_centralizada($tipo_auth)) {
			if (! toba::manejador_sesiones()->get_autenticacion()->permite_login_toba()) {
				$this->evt__cas__ingresar();
			}
		}
	}*/
		
	function conf__login()
	{
		if (! toba::proyecto()->get_parametro('validacion_debug') ) {
			$this->pantalla()->eliminar_dep('seleccion_usuario');
		}
		
		$this->eliminar_dependencias_no_usadas();										//Quito los forms que no uso dependiendo del tipo de autenticacion
		if ($this->en_popup && toba::manejador_sesiones()->existe_usuario_activo()) {
			//Si ya esta logueado y se abre el sistema en popup, ocultar componentes visuales
			$this->pantalla()->set_titulo('');			
			if ($this->pantalla()->existe_dependencia('seleccion_usuario')) {
				$this->pantalla()->eliminar_dep('seleccion_usuario');
			}
			if ($this->pantalla()->existe_dependencia('datos')) {
				$this->pantalla()->eliminar_dep('datos');
			}			
			/*if ($this->pantalla()->existe_evento('Ingresar')) {
				$this->pantalla()->eliminar_evento('Ingresar');
			}*/
			/*if ($this->pantalla()->existe_dependencia('form_solo_proyecto')) {
				$this->pantalla()->eliminar_dep('form_solo_proyecto');
			}*/
		}
	}	
	
	/**
	 * Elimina los formularios que no se usan segun el tipo de autenticacion indicado en instalacion.ini
	 */
	function eliminar_dependencias_no_usadas()
	{
		$tipo_auth = toba::instalacion()->get_tipo_autenticacion();	
		switch($tipo_auth) {
		case 'openid':
			if (! toba::manejador_sesiones()->get_autenticacion()->permite_login_toba() && $this->pantalla()->existe_dependencia('datos')) {
				$this->pantalla()->eliminar_dep('datos');
			}
			if ($this->pantalla()->existe_dependencia('cas')) {
				$this	->pantalla()->eliminar_dep('cas');
			}
			break;
		case 'cas':
		case 'saml':
		case 'saml_onelogin':
			if (! toba::manejador_sesiones()->get_autenticacion()->permite_login_toba() && $this->pantalla()->existe_dependencia('datos')) {
				$this->pantalla()->eliminar_dep('datos');
			}
			if ($this->pantalla()->existe_dependencia('openid')) {
				$this->pantalla()->eliminar_dep('openid');
			}
			break;				
		default:
			if ($this->pantalla()->existe_dependencia('openid')) {
				$this->pantalla()->eliminar_dep('openid');
			}
			if ($this->pantalla()->existe_dependencia('cas')) {
				$this->pantalla()->eliminar_dep('cas');
			}
			/*if ($this->pantalla()->existe_dependencia('form_solo_proyecto')) {
				$this->pantalla()->eliminar_dep('form_solo_proyecto');
			}*/
		}	
	}
	
	/**
	 * 
	 * @throws toba_reset_nucleo
	 * @ignore
	 */
	function post_eventos()
	{
		try {		
			$this->invocar_autenticacion_por_tipo();
		} catch (toba_error_autenticacion $e) {
			//-- Caso error de validación
			$this->resetear_marca_login();
			toba::notificacion()->agregar($e->getMessage());
		} catch (toba_error_autenticacion_intentos $e) {
			//-- Caso varios intentos fallidos con captcha
			$this->resetear_marca_login();
			list($msg, $intentos) = explode('|', $e->getMessage());
			toba::notificacion()->agregar($msg);
			toba::memoria()->set_dato_instancia('toba_intentos_fallidos_login', $intentos);
		} catch (toba_reset_nucleo $reset) {
			//-- Caso validacion exitosa, elimino la marca de intentos fallidos
			if (toba::memoria()->get_dato_instancia('toba_intentos_fallidos_login') !== null) {
				toba::memoria()->eliminar_dato_instancia('toba_intentos_fallidos_login');
			}
			$this->s__item_inicio = $reset->get_item();	//Se guarda el item de inicio al que queria derivar el nucleo
		}
		return;
	}

	/**
	 * Hace el llamado a toba_manejador_sesiones segun el metodo indicado en instalacion.ini 
	 * y que ingreso el usuario.
	 */
	function invocar_autenticacion_por_tipo()
	{
		$tipo_auth = toba::instalacion()->get_tipo_autenticacion();
		$datos_editor = array('instancia' => toba::instancia()->get_id());
		$datos_editor['proyecto'] = (isset($this->s__datos['proyecto']))? $this->s__datos['proyecto'] : 'toba_editor' ;
		
		if (isset($this->s__datos['usuario']) || isset($this->s__datos_openid['provider'])) {			//Para el caso de autenticacion basica y OpenId
			if ($tipo_auth == 'openid' && isset($this->s__datos_openid)) {
				toba::manejador_sesiones()->get_autenticacion()->set_provider($this->s__datos_openid);
			}
			$usuario = (isset($this->s__datos['usuario'])) ? $this->s__datos['usuario'] : '';
			$clave = (isset($this->s__datos['clave'])) ? $this->s__datos['clave'] : '';			
			if (toba_autenticacion::es_autenticacion_centralizada($tipo_auth)) {
				toba::manejador_sesiones()->get_autenticacion()->usar_login_basico();
			}			
			toba::manejador_sesiones()->login($usuario, $clave, $datos_editor);

		} elseif (toba_autenticacion::es_autenticacion_centralizada($tipo_auth) && toba::manejador_sesiones()->get_autenticacion()->uso_login_centralizado()) {	//El control por session es para que no redireccione automaticamente
			toba::manejador_sesiones()->get_autenticacion()->verificar_acceso($datos_editor);			
		}	
	}	
	
	/**
	 * Elimina  la marca del login basico ante un fallido, de manera que si luego loguea centralizado desloguee correctamente
	 * @ignore
	 */
	protected function resetear_marca_login()
	{
		if (toba::manejador_sesiones()->get_autenticacion()->uso_login_basico()) {
			toba::manejador_sesiones()->get_autenticacion()->eliminar_login_basico();
		}
	}
	
	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	//---- datos -------------------------------------------------------
	function evt__datos__ingresar($datos)
	{
		if (isset($this->s__datos_openid)) {
			unset($this->s__datos_openid);
		}		
		toba::logger()->desactivar();
		if (isset($datos['test_error_repetido']) && !$datos['test_error_repetido']) {
			throw new toba_error_autenticacion('El valor ingresado de confirmación no es correcto');
		} else {
			$this->s__datos = $datos;
		}
		if ( ! isset($this->s__datos['instancia']) && isset($this->s__datos['proyecto']) 
				&& ( isset($this->s__datos['usuario']) || isset($this->s__datos['autologin'])) ) {
			toba::notificacion()->agregar('Es necesario completar todos los parametros.');
		}
		if (toba::proyecto()->get_parametro('validacion_debug')) {
			$this->s__datos['usuario'] = $this->s__datos['autologin'];
			$this->s__datos['clave'] = null;
		} else {
			if (!isset($this->s__datos['clave'])) {
				throw new toba_error('Es necesario ingresar la clave');
			}
		}		
	}
	
	function conf__datos(toba_ei_formulario $form)
	{
		$efs_desactivar = array();
		if (toba::instalacion()->get_tipo_autenticacion() != 'openid') {
			$form->set_titulo('');
		}		
		if (toba::proyecto()->get_parametro('validacion_debug')) {
			$efs_desactivar[] = 'usuario';
			$efs_desactivar[] = 'clave';
		} else {
			$efs_desactivar[] = 'autologin';
		}
		
		if (toba::memoria()->get_dato_instancia('toba_intentos_fallidos_login') === null) {
			$efs_desactivar[] = 'test_error_repetido';
		}
		
		if (! empty($efs_desactivar)) {
			$form->desactivar_efs($efs_desactivar);
		}
		
		if (!isset($this->s__datos['instancia'])) {
			$this->s__datos['instancia'] = toba::instancia()->get_id();
		}
		
		if (isset($this->s__datos)) {
			if (isset($this->s__datos['clave'])) {
				unset($this->s__datos['clave']);
			}
			$form->set_datos($this->s__datos);
		}
	}	
	
	
	//---- open_id -------------------------------------------------------
	
	function evt__openid__ingresar($datos)
	{
		if (isset($this->s__datos)) {
			unset($this->s__datos);
		} 
		$this->s__datos_openid = $datos;
	}	

	function conf__openid(toba_ei_formulario $form)
	{
		$providers = $this->get_openid_providers();
		if (! empty($providers)) {
			$provider = current($providers);
			$form->set_datos_defecto(array('provider' => $provider['id']));
		}
		if (isset($this->s__datos_openid)) {
			$form->set_datos($this->s__datos_openid);
		}
	}	
	
	
	function get_openid_providers() 
	{
		return toba::manejador_sesiones()->get_autenticacion()->get_providers();
	}

	//---- cas -----------------------------------------------------------------------
	function evt__cas__ingresar()
	{
		toba::manejador_sesiones()->get_autenticacion()->usar_login_centralizado();
		$datos_editor = array('instancia' => toba::instancia()->get_id());
		$datos_editor['proyecto'] = (isset($this->s__datos['proyecto']))? $this->s__datos['proyecto'] : 'toba_editor' ;
		try {
			toba::manejador_sesiones()->get_autenticacion()->verificar_acceso($datos_editor);
		} catch (toba_error_autenticacion $e) {
			//-- Caso error de validación				
			toba::notificacion()->agregar($e->getMessage());	
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
	
	//--- COMBOS ----------------------------------------------------------------

	function get_lista_usuarios()
	{
		$instancia = toba_modelo_catalogo::instanciacion()->get_instancia(toba::instancia()->get_id(), new toba_mock_proceso_gui);
		$usuarios = $instancia->get_lista_usuarios('toba_editor');
		$datos = array();
		$a = 0;
		foreach ($usuarios as $desc) {
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
	
	function extender_objeto_js()
	{
		$escapador = toba::escaper();
		if (toba::instalacion()->get_tipo_autenticacion() == 'openid') {
			$personalizable = '';
			foreach ($this->get_openid_providers() as $id => $provider) {
				if (isset($provider['personalizable']) && $provider['personalizable']) {
					$personalizable = $escapador->escapeJs($id);
				}
			}
			echo $escapador->escapeJs($this->dep('openid')->objeto_js)
				.".evt__provider__procesar = function(inicial) {
					if (this.ef('provider').get_estado() == '$personalizable') {
						this.ef('provider_url').mostrar();
					} else {
						this.ef('provider_url').ocultar();
					}
				}
			";
		}
				
		$finalizar = toba::memoria()->get_parametro(apex_sesion_qs_finalizar);
		if (is_null($finalizar)) {											//Sesion activa
			if (toba::manejador_sesiones()->existe_usuario_activo()) {
				//Si ya esta logueado y se abre el sistema en popup, abrirlo
				if (isset($this->s__item_inicio)) {
					list($proyecto, $item) = explode($this->s__item_inicio);
				} else {
					$proyecto = toba::proyecto()->get_id();
					$item = toba::proyecto()->get_parametro('item_inicio_sesion');
				}
				$url = $escapador->escapeJs(toba::vinculador()->get_url($proyecto, $item));
				
				if ($this->en_popup) {
					echo " abrir_popup('sistema', '$url', {resizable: 1});	";
				} else {
					echo " window.location.href = '$url';";
				}
			}
		} elseif ($this->en_popup) {									//Se finaliza la sesion
				echo '
					if (window.opener &&  window.opener.location) {
						window.opener.location.href = window.opener.location.href; 
					}
					window.close();
				';
		}		
	}
}
?>