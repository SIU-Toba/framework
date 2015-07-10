<?php

class toba_autenticacion_saml_onelogin extends toba_autenticacion implements toba_autenticable
{
	protected $auth_source = "default-sp";	
	protected $atributo_usuario = "urn:oid:0.9.2342.19200300.100.1.1";
	protected $permite_login_toba = false;
	protected $saml_attributes;
	protected $settingsInfo=array();
	protected $idp = '';

	function __construct()
	{
		$archivo_ini_instalacion = toba::nucleo()->toba_instalacion_dir().'/saml_onelogin.ini';
		if (is_file( $archivo_ini_instalacion)) {
			$parametros = parse_ini_file($archivo_ini_instalacion, true);
			if (isset($parametros['basicos']['atributo_usuario'])) {
				$this->atributo_usuario = $parametros['basicos']['atributo_usuario'];
			}	
			if (isset($parametros['basicos']['permite_login_toba'])) {
				$this->permite_login_toba = ($parametros['basicos']['permite_login_toba'] == 1);
			}			
			if (isset($parametros['sp']['auth_source'])) {
				$this->auth_source = $parametros['sp']['auth_source'];
			}			

			//Creo configuracion del SP
			$this->settingsInfo= array ('sp' => $this->get_sp_config());			
			//Agrego configuracion del IdP
			if (isset($parametros['sp']['idp'])) {
				$this->idp = $parametros['sp']['idp'];
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
			$errors = $auth->getErrors();

			if (!empty($errors)) {
				toba::logger()->error('Errores en el proceso de onelogin: ');
				toba::logger()->error($errors);
				throw new toba_error_seguridad('Se produjo un error durante el procedimiento de login, contacte un administrador');
			}

			if (!$auth->isAuthenticated()) {
				throw new toba_error_autenticacion('No ha sido posible autenticar al usuario');
			}

			$this->saml_attributes = $auth->getAttributes();
			$id_usuario = $this->recuperar_usuario_toba();							//Recupero usr y verifico existencia en toba, excepcion si no existe
			toba::manejador_sesiones()->login($id_usuario, 'foobar',  $datos_iniciales);					//La clave no importa porque se autentifica via token
			return $id_usuario;
			
		} else {																//Se hace el redirect hacia el idp
			$auth->login();
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
		$id_usuario = utf8_d_seguro($this->saml_attributes[$this->atributo_usuario][0]);
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
		//Uso la dir de instalacion toba como base del entity-id
		$spBaseUrl = toba::instalacion()->get_url();
		
		//Trata de obtener una url canonica del proyecto (sin aplicacion.php y sin QS), en base al request del cliente esta sera la URL de retorno
		$spReturnUrl = toba_http::get_url_actual(false, true);
		$spReturnUrl = str_replace("aplicacion.php", "", $spReturnUrl);

		$info =  array (	'entityId' => $spBaseUrl . '/' .$this->auth_source,
					'assertionConsumerService' => array ( 'url' => $spReturnUrl.'?acs'),
					'singleLogoutService' => array ('url' => $spReturnUrl.'?sls'),
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
}
