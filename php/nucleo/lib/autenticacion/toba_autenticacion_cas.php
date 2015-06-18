<?php
class toba_autenticacion_cas extends toba_autenticacion implements toba_autenticable
{
	protected $url;
	protected $puerto;
	protected $host;
	protected $archivo_certificado;
	protected $permite_login_toba = false;
	protected $validar_cn = true;
	private $cliente_cas;
		
	function __construct()
	{
		$archivo_ini_instalacion = toba::nucleo()->toba_instalacion_dir().'/cas.ini';
		if (is_file( $archivo_ini_instalacion)) {
			$parametros = parse_ini_file($archivo_ini_instalacion, true);
			if (isset($parametros['basicos']['server'])) {
				$this->host = $parametros['basicos']['server'];
			}			
			if (isset($parametros['basicos']['port'])) {
				$this->puerto = (int) $parametros['basicos']['port'];
			}			
			if (isset($parametros['basicos']['url'])) {
				$this->url = $parametros['basicos']['url'];
			}
			if (isset($parametros['basicos']['certificadoCA'])) {						//Certificado para verificar que el servidor es quien dice ser por phpCas.
				$this->archivo_certificado = $parametros['basicos']['certificadoCA'];
			}
			if (isset($parametros['basicos']['validar_cn'])) {
				$this->validar_cn = ($parametros['basicos']['validar_cn'] == 1);
			}
			if (isset($parametros['basicos']['permite_login_toba'])) {
				$this->permite_login_toba = ($parametros['basicos']['permite_login_toba'] == 1);
			}
		}
	}
	
	function autenticar($id_usuario, $clave, $datos_iniciales=null)
	{
		if ($this->uso_login_basico() && $this->permite_login_toba()) {				//Si es login toba no redirecciono al servidor CAS
			return toba::manejador_sesiones()->invocar_autenticar($id_usuario, $clave, $datos_iniciales);
		}		
		$this->iniciar_pedido_cas();											//Hago el 	pedido al servidor CAS
		$id_usuario = $this->recuperar_usuario_toba();							//Recupero usr y verifico existencia en toba, excepcion si no existe
		return true;													
	}
	
	function verificar_acceso($datos_iniciales=null) 
	{
		$this->iniciar_pedido_cas();
		$id_usuario = $this->recuperar_usuario_toba();						//Recupero usr y verifico existencia en toba, excepcion si no existe
		$this->iniciar_sesion($id_usuario, 'foobar', $datos_iniciales);							//La clave no importa porque se autentifica via ticket
		return $id_usuario;
	}
			
	function iniciar_sesion($usuario, $clave, $datos_iniciales=null)
	{
		toba::manejador_sesiones()->login($usuario, $clave, $datos_iniciales);		
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
		// Se conecta al CAS
		$this->instanciar_cliente_cas(); 
		// Desloguea sin parametros porque igualmente CAS pide cerrar el browser por cuestiones de seguridad
		phpCAS::logout();
		exit;	
	}
	
	function verificar_logout()
	{
		if ($this->uso_login_basico() && $this->permite_login_toba()) {					//Si es login toba, no chequear logout de CAS
			return;
		}
		// Se conecta al CAS
		$this->instanciar_cliente_cas();
		return phpCAS::checkAuthentication();
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
		phpCAS::setDebug('cas.log');			
	}	
	
	//-------------------------------------------------------------------------------------------------------------------------------------------------------//
	//							METODOS PROTEGIDOS
	//-------------------------------------------------------------------------------------------------------------------------------------------------------//
	protected function iniciar_pedido_cas()
	{
		$this->instanciar_cliente_cas(); 

		phpCAS::setExtraCurlOption(CURLOPT_SSLVERSION, 3);
		// Se genera la URL de servicio
		$param = array();
		if (isset($this->parametros_url) && is_array($this->parametros_url)) {
			$param = $this->parametros_url;
		}
		$url = $this->generar_url($param);
		phpCAS::setFixedServiceURL($url);	

		// Tipo de auth
		if (toba::instalacion()->es_produccion()) {
			phpCAS::setCasServerCACert($this->archivo_certificado, $this->validar_cn);
		} else {		
			phpCAS::setNoCasServerValidation();
		}
		
		phpCAS::setServerLoginURL('');

		/** Llamada principal al authentificación de CAS, si no estás
		autenticado te redirecciona ahí adentro y no sigue ejecutando
		Si pasa está función significa que estás autenticado **/
		phpCAS::forceAuthentication();		
	}
	
	protected function recuperar_usuario_toba()
	{
		$id_usuario = phpCAS::getUser();
		$datos_usuario = false;
		
		$subclase = $this->get_subclase_usuario_proyecto();
		$datos_usuario = $subclase::existe_usuario($id_usuario);
		if ($datos_usuario === false) {													//El usuario no existe en la bd de toba.
				toba::logger()->crit("El usuario CAS '$id_usuario' no existe en la instancia toba");
				throw new toba_error_autenticacion("El usuario '$id_usuario' no esta dado de alta en el sistema");
		}
		return $id_usuario;
	}	
	
	private function instanciar_cliente_cas()
	{
		if (! isset($this->cliente_cas)) {													//Se hace una sola instanciacion, sino falla.
			phpCAS::client(CAS_VERSION_2_0, $this->host, $this->puerto, $this->url, false);
			$this->cliente_cas = true;
		}
	}	
}
?>
