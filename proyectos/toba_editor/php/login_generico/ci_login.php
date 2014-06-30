<?php
class ci_login extends toba_ci
{
	protected $s__datos;
	protected $s__datos_openid;
	protected $en_popup = false;
	protected $s__item_inicio;
	private $es_cambio_contrasenia = false;
	
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
		/*if(isset($this->s__datos)) {							//Si hay valores de inicios viejos, se limpian.
			unset($this->s__datos);
		}*/
		if (isset($this->s__datos_openid)) {
			unset($this->s__datos_openid);
		}
	}

	function ini()
	{
		toba_ci::set_navegacion_ajax(false);
		$this->en_popup = toba::proyecto()->get_parametro('item_pre_sesion_popup');
		if (toba::instalacion()->get_tipo_autenticacion() == 'openid') {
			try {
				toba::manejador_sesiones()->get_autenticacion()->verificar_acceso();
			} catch (toba_error_autenticacion $e) {
				//-- Caso error de validación
				toba::notificacion()->agregar($e->getMessage());
			}
		}
		$tipo_auth = toba::instalacion()->get_tipo_autenticacion();
		if (in_array($tipo_auth, array('cas','saml'))) {
			if (! toba::manejador_sesiones()->get_autenticacion()->permite_login_toba()) {
				$this->evt__cas__ingresar();
			}
		}		
	}
	
	function conf__login()
	{
		if ( ! toba::proyecto()->get_parametro('validacion_debug') ) {
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
			if ($this->pantalla()->existe_evento('Ingresar')) {
				$this->pantalla()->eliminar_evento('Ingresar');
			}
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
		}	
	}
	
	/**
	 * 
	 * @throws toba_reset_nucleo
	 * @ignore
	 */
	function post_eventos()
	{
		if ($this->es_cambio_contrasenia) {
			return;						//Fuerza a que no intente loguear, sino que redirija a la pantalla de login
		}
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
		} catch (toba_error_login_contrasenia_vencida $e) {
			$this->resetear_marca_login();
			$this->set_pantalla('cambiar_contrasenia');
		} catch (toba_reset_nucleo $reset) {
			//-- Caso validacion exitosa, elimino la marca de intentos fallidos
			if (toba::memoria()->get_dato_instancia('toba_intentos_fallidos_login') !== null) {
				toba::memoria()->eliminar_dato_instancia('toba_intentos_fallidos_login');
			}
			//-- Se redirige solo si no es popup
			if (! $this->en_popup) {
				throw $reset;
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
		if (isset($this->s__datos['usuario']) || isset($this->s__datos_openid['provider'])) {			//Para el caso de autenticacion basica y OpenId
			if ($tipo_auth == 'openid' && isset($this->s__datos_openid)) {
				toba::manejador_sesiones()->get_autenticacion()->set_provider($this->s__datos_openid);
			}
			$usuario = (isset($this->s__datos['usuario'])) ? $this->s__datos['usuario'] : '';
			$clave = (isset($this->s__datos['clave'])) ? $this->s__datos['clave'] : '';

			if (in_array($tipo_auth, array('cas','saml'))) {
				toba::manejador_sesiones()->get_autenticacion()->usar_login_basico();
			}			
			toba::manejador_sesiones()->login($usuario, $clave);

		} elseif (in_array($tipo_auth, array('cas','saml')) && toba::manejador_sesiones()->get_autenticacion()->uso_login_centralizado()) {	//El control por session es para que no redireccione automaticamente
			toba::manejador_sesiones()->get_autenticacion()->verificar_acceso();
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
	}
	
	function conf__datos(toba_ei_formulario $form)
	{
		if (toba::memoria()->get_dato_instancia('toba_intentos_fallidos_login') === null) {
			$form->desactivar_efs(array('test_error_repetido'));
		}
		if (toba::instalacion()->get_tipo_autenticacion() != 'openid') {
			$form->set_titulo('');
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
		try {
			toba::manejador_sesiones()->get_autenticacion()->verificar_acceso();
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
	
	//-----------------------------------------------------------------------------------
	//---- form_passwd_vencido ----------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_passwd_vencido($form)
	{
		$form->set_datos(array());
	}
	
	function evt__form_passwd_vencido__modificacion($datos)
	{
		$usuario = $this->s__datos['usuario'];		
		if (toba::manejador_sesiones()->invocar_autenticar($usuario, $datos['clave_anterior'], null)) {		//Si la clave anterior coincide	
			//Verifico que no intenta volver a cambiarla antes del periodo permitido
			$dias_minimos = toba::proyecto()->get_parametro('proyecto', 'dias_minimos_validez_clave', false);
			if (! is_null($dias_minimos)) {
				if (! toba_usuario::verificar_periodo_minimo_cambio($usuario, $dias_minimos)) {
					toba::notificacion()->agregar('No transcurrio el período minimo para poder volver a cambiar su contraseña. Intentelo en otra ocasión');
					return;
				}
			}		
			//Obtengo el largo minimo de la clave
			$largo_clave = toba::proyecto()->get_parametro('proyecto', 'pwd_largo_minimo', false);
			try {
				toba_usuario::verificar_composicion_clave($datos['clave_nueva'], $largo_clave);
			
				//Obtengo los dias de validez de la nueva clave
				$dias = toba::proyecto()->get_parametro('proyecto', 'dias_validez_clave', false);
				toba_usuario::verificar_clave_no_utilizada($datos['clave_nueva'], $usuario);
				toba_usuario::reemplazar_clave_vencida($datos['clave_nueva'], $usuario, $dias);
				$this->es_cambio_contrasenia = true;				//Bandera para el post_eventos
			} catch(toba_error_pwd_conformacion_invalida $e) {
				toba::logger()->info($e->getMessage());
				toba::notificacion()->agregar($e->getMessage(), 'error');
				return;
			}
		} else {
			throw new toba_error_usuario('La clave ingresada no es correcta');
		}
		$this->set_pantalla('login');
	}

	function evt__form_passwd_vencido__cancelar()
	{
		$this->set_pantalla('login');
	}
	
	//-------------------------------------------------------------------
	
	function extender_objeto_js()
	{
		if (toba::instalacion()->get_tipo_autenticacion() == 'openid') {
			$personalizable = '';
			foreach ($this->get_openid_providers() as $id => $provider) {
				if (isset($provider['personalizable']) && $provider['personalizable']) {
					$personalizable = $id;
				}
			}
			echo "
				{$this->dep('openid')->objeto_js}.evt__provider__procesar = function(inicial) {
					if (this.ef('provider').get_estado() == '$personalizable') {
						this.ef('provider_url').mostrar();
					} else {
						this.ef('provider_url').ocultar();
					}
				}
			";
		}
		
		if ($this->en_popup) {
			$finalizar = toba::memoria()->get_parametro(apex_sesion_qs_finalizar);
			//Si cierra la sesión y es popup, cierra la ventana y al parent (si existe) lo recarga			
			if (isset($finalizar)) {
				echo '
					if (window.opener &&  window.opener.location) {
						window.opener.location.href = window.opener.location.href; 
					}
					window.close();
				';
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