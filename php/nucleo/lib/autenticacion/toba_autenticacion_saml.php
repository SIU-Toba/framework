<?php

class toba_autenticacion_saml  extends toba_autenticacion implements  toba_autenticable
{
	protected $auth_source = "default-sp";
	protected $atributo_usuario = "urn:oid:0.9.2342.19200300.100.1.1";
	protected $permite_login_toba = false;
	protected $path_sp = "3ros/simplesamlphp";
	protected $saml_sp;
	protected $saml_attributes;
		
	function __construct()
	{
		$archivo_ini_instalacion = toba::nucleo()->toba_instalacion_dir().'/saml.ini';
		if (is_file( $archivo_ini_instalacion)) {
			$parametros = parse_ini_file($archivo_ini_instalacion, true);
			if (isset($parametros['basicos']['auth_source'])) {
				$this->auth_source = $parametros['basicos']['auth_source'];
			}			
			if (isset($parametros['basicos']['atributo_usuario'])) {
				$this->atributo_usuario = $parametros['basicos']['atributo_usuario'];
			}	
			if (isset($parametros['basicos']['path_sp'])) {
				$this->path_sp = $parametros['basicos']['path_sp'];
			}				
			if (isset($parametros['basicos']['permite_login_toba'])) {
				$this->permite_login_toba = ($parametros['basicos']['permite_login_toba'] == 1);
			}
		}
		require_once($this->path_sp.'/lib/_autoload.php');
	}
	
	function autenticar($id_usuario, $clave, $datos_iniciales=null)
	{
		if ($this->uso_login_basico() && $this->permite_login_toba()) {				//Si es login toba no redirecciono al servidor CAS
			return toba::manejador_sesiones()->invocar_autenticar($id_usuario, $clave, $datos_iniciales);
		}
		$id_usuario = $this->recuperar_usuario_toba();							//Recupero usr y verifico existencia en toba, excepcion si no existe
		return true;													
	}
	
	function verificar_acceso($datos_iniciales=null) 
	{
		$this->iniciar_pedido_saml();
		$id_usuario = $this->recuperar_usuario_toba();						//Recupero usr y verifico existencia en toba, excepcion si no existe
		toba::manejador_sesiones()->login($id_usuario, 'foobar', $datos_iniciales);	//La clave no importa porque se autentifica via ticket
		return $id_usuario;
	}

	function logout()
	{
		if ($this->uso_login_basico() && $this->permite_login_toba()) {				//Si es login toba no redirecciono al servidor CAS
			$this->eliminar_marca_login(self::$marca_login_basico);
			return;
		}		
		if ($this->uso_login_centralizado()) {
			$this->eliminar_marca_login(self::$marca_login_central);
		}		
		$this->saml_sp = new SimpleSAML_Auth_Simple($this->auth_source);
		$this->saml_sp->logout();

	}
	
	function verificar_logout()
	{
		 if ($this->uso_login_basico() && $this->permite_login_toba()) {    //Si es login toba, no chequear logout de SAML
			return;
		}
		
		$this->saml_sp = new SimpleSAML_Auth_Simple($this->auth_source);
		if (! $this->saml_sp->isAuthenticated()) {
			throw new toba_error_usuario("Ha sido deslogueado");
		}
	}	
		
	function verificar_clave_vencida($id_usuario)
	{
		return false;
	}	
	
	function permite_login_toba()
	{
		return $this->permite_login_toba;
	}
	
	function activar_debug()
	{		
	}	
	
	//-------------------------------------------------------------------------------------------------------------------------------------------------------//
	//							METODOS PROTEGIDOS
	//-------------------------------------------------------------------------------------------------------------------------------------------------------//
	protected function iniciar_pedido_saml()
	{
		$param = array();
		if (isset($this->parametros_url) && is_array($this->parametros_url)) {
			$param['ReturnTo'] = $this->generar_url($this->parametros_url);
		}
		$this->saml_sp = new SimpleSAML_Auth_Simple($this->auth_source);
		$this->saml_sp->requireAuth($param);
		$this->saml_attributes = $this->saml_sp->getAttributes();
		toba::logger()->debug("Attributos SAML: ".print_r($this->saml_attributes, true));
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
	
	
}
?>
