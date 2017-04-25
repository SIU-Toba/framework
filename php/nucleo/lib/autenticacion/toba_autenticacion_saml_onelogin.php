<?php

class toba_autenticacion_saml_onelogin extends toba_autenticacion implements toba_autenticable
{
	protected $auth_source = "default-sp";	
	protected $atributo_usuario = "urn:oid:0.9.2342.19200300.100.1.1";
	protected $permite_login_toba = false;
	protected $proyecto_login;
	protected $settingsInfo=array();
	protected $idp = '';

	function __construct()
	{
		$sp_name = 'sp';
		$archivo_ini_instalacion = toba::nucleo()->toba_instalacion_dir().'/saml_onelogin.ini';
		if (is_file( $archivo_ini_instalacion)) {
			$parametros = parse_ini_file($archivo_ini_instalacion, true);
			if (isset($parametros['basicos']['atributo_usuario'])) {
				$this->atributo_usuario = $parametros['basicos']['atributo_usuario'];
			}	
			if (isset($parametros['basicos']['permite_login_toba'])) {
				$this->permite_login_toba = ($parametros['basicos']['permite_login_toba'] == 1);
			}			
			if (! isset($parametros[$sp_name])) {
				$buscado = 'sp:' . toba::proyecto()->get_id();
				foreach (array_keys($parametros) as $cada_clave) {
					if ($cada_clave == $buscado) {
						$sp_name = $cada_clave;
					}
				}
			}			
			if (isset($parametros[$sp_name]['auth_source'])) {
				$this->auth_source = $parametros[$sp_name]['auth_source'];
			}
			if (!isset($parametros[$sp_name]['proyecto_login'])) {
				throw new toba_error("Debe definir proyecto_login en ".$archivo_ini_instalacion);
			}
			$this->proyecto_login = trim($parametros[$sp_name]['proyecto_login']);

			//Creo configuracion del SP
			$this->settingsInfo= array ('sp' => $this->get_sp_config());			
			//Agrego configuracion del IdP
			if (isset($parametros[$sp_name]['idp'])) {
				$this->idp = $parametros[$sp_name]['idp'];
			}			
			$idp_name = 'idp:' . $this->idp;
			if (isset($parametros[$idp_name]) && !empty($parametros[$idp_name])) {												
				$this->settingsInfo['idp'] = $this->get_idp_config($parametros[$idp_name], $this->idp);	
			}			
		}
	}
	
	
	function autenticar($id_usuario, $clave, $datos_iniciales=null)
	{
		if ($this->uso_login_basico() && $this->permite_login_toba()) {				//Si es login toba no redirecciono al servidor saml
			return toba::manejador_sesiones()->invocar_autenticar($id_usuario, $clave, $datos_iniciales);
		}
		$id_usuario = $this->recuperar_usuario_toba();	
		return true;
	}

	function verificar_acceso($datos_iniciales=null)
	{
		$auth = $this->instanciar_pedido_onelogin();

		if (! is_null(toba::memoria()->get_parametro('acs'))) {						//Se verifica la respuesta y se chequea la autenticacion
			$auth->processResponse();
			
			$this->verificar_errores_onelogin($auth);

			if (!$auth->isAuthenticated()) {
				throw new toba_error_autenticacion('No ha sido posible autenticar al usuario');
			}

			$this->set_atributos_usuario($auth->getAttributes());
			$id_usuario = $this->recuperar_usuario_toba();							//Recupero usr y verifico existencia en toba, excepcion si no existe
			try {
				toba::manejador_sesiones()->login($id_usuario, 'foobar', $datos_iniciales);                    //La clave no importa porque se autentifica via token
			} catch (toba_reset_nucleo $e) {
				if (isset($_POST['RelayState']) && OneLogin_Saml2_Utils::getSelfURL() != $_POST['RelayState']) {
					$auth->redirectTo($_POST['RelayState']);
				} else {
					throw $e;
				}
			}
			return $id_usuario;
			
		} else {
			$this->procesar_logout($auth);
			
			//Se hace el redirect hacia el idp
			$parametros_url = array();
			if (isset($this->parametros_url) && is_array($this->parametros_url)) {
				$parametros_url = $this->parametros_url;
			}
			$auth->login($this->generar_url($parametros_url));
		}
	}
		
	function logout()
	{
		$auth = $this->instanciar_pedido_onelogin();
		$auth->logout();							//No se verifica la respuesta, para toba el usuario se deslogueo
	}		
	
	/**
	 * Verifica en cada pedido de pagina que el usuario actual siga logueado (si aplica al metodo de autenticacion)
	 */
	function verificar_logout()
	{
		//Definicion para completar API. Se podria implementar el dia que OpenID estandarice el logout, hoy cada provider lo hace en url distinta o no lo hace.
		if ($this->uso_login_basico() && $this->permite_login_toba()) {    //Si es login toba, no chequear logout de onelogin
			return;
		}
		
		$auth = $this->instanciar_pedido_onelogin();
		$this->procesar_logout($auth);

		return false;
	}
	
	function verificar_clave_vencida($id_usuario)
	{
		return false;
	}
	
	function permite_login_toba() 
	{
		return $this->permite_login_toba;
	}
	
	//-------------------------------------------------------------------------------------------------------------------------------------------------------//
	//							METODOS PROTEGIDOS
	//-------------------------------------------------------------------------------------------------------------------------------------------------------//
	protected function instanciar_pedido_onelogin()
	{
		$auth = new OneLogin_Saml2_Auth($this->settingsInfo);
		return $auth;
	}
	
	protected function recuperar_usuario_toba()
	{
		$atributos_usuario = $this->get_atributos_usuario();
		$id_usuario = utf8_d_seguro($atributos_usuario[$this->atributo_usuario][0]);
		$datos_usuario = false;
		
		$subclase = $this->get_subclase_usuario_proyecto();
		$datos_usuario = $subclase::existe_usuario($id_usuario);
		if ($datos_usuario === false) {													//El usuario no existe en la bd de toba.
				toba::logger()->crit("El usuario SAML '$id_usuario' no existe en la instancia toba");
				throw new toba_error_autenticacion("El usuario '$id_usuario' no esta dado de alta en el sistema");
		}
		return $id_usuario;
	}
	
	protected function get_sp_config()
	{
		//Arma el entityID en base a una URL fija de toba
		$entityID = toba_http::get_protocolo() . toba_http::get_nombre_servidor();
		$entityID .= toba::instancia()->get_url_proyecto($this->proyecto_login);

		$info =  array (	'entityId' => $entityID.'/' . $this->auth_source,
					'assertionConsumerService' => array ( 'url' => $entityID.'?acs'),
					'singleLogoutService' => array ('url' => $entityID.'?sls'	),
					'NameIDFormat' =>$this->atributo_usuario
					);
		return $info;
	}
		
	protected function get_idp_config($parametros, $entity)
	{
		$contenido = '';
		if (isset($parametros['certFile']) && trim($parametros['certFile']) != '') {
			$nombre = realpath($parametros['certFile']);
			if (file_exists($nombre)) {
				$contenido = file_get_contents($nombre);
			}
		}	
		$sso = (isset($parametros['SingleSignOnService'])) ? $parametros['SingleSignOnService'] : '';
		$slo = (isset($parametros['SingleLogoutService'])) ? $parametros['SingleLogoutService'] : '';		
		$info =  array (	'entityId' =>$entity, 
					'singleSignOnService' => array ('url' => $sso),
					'singleLogoutService' => array ('url' => $slo),
					'x509cert' => $contenido,
				);
		return $info;
	}
	
	private function verificar_errores_onelogin(OneLogin_Saml2_Auth $auth)
	{
		$errors = $auth->getErrors();
		if (!empty($errors)) {
			toba::logger()->error('Errores en el proceso de onelogin: ');
			toba::logger()->error($errors);
			toba::logger()->error($auth->getLastErrorReason());
			throw new toba_error_seguridad('Se produjo un error durante el procedimiento de login, contacte un administrador');
		}
	}
	
	private function procesar_logout(OneLogin_Saml2_Auth $auth)
	{
		if (! is_null(toba::memoria()->get_parametro('sls'))) {
			$auth->processSLO();
		} elseif (isset($_GET['slo'])) {
			$auth->logout();
		}
		$this->verificar_errores_onelogin($auth);
	}
}
