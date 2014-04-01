<?php
class toba_autenticacion_cas  implements  toba_autenticable
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
	
	function verificar_acceso() 
	{
		$this->iniciar_pedido_cas();
		$id_usuario = $this->recuperar_usuario_toba();						//Recupero usr y verifico existencia en toba, excepcion si no existe
		$this->iniciar_sesion($id_usuario, 'foobar');							//La clave no importa porque se autentifica via ticket
		return $id_usuario;
	}
			
	function iniciar_sesion($usuario, $clave)
	{
		toba::manejador_sesiones()->login($usuario, $clave);		
	}

	function logout()
	{
		if ($this->uso_login_basico() && $this->permite_login_toba()) {				//Si es login toba no redirecciono al servidor CAS
			unset($_SESSION['uso_login_basico']);
			return;
		}
		// Se conecta al CAS
		$this->instanciar_cliente_cas(); 
		// Desloguea sin parametros porque igualmente CAS pide cerrar el browser por cuestiones de seguridad
		phpCAS::logout();
		exit;	
	}
	
	function verificar_logout()
	{
		
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
	
	function usar_login_basico()
	{
		$_SESSION['uso_login_basico'] = 'true';								//Usa SESSION porque el pedido se termina antes de sincronizar toba_memoria
	}
	
	function uso_login_basico()
	{
		return (isset($_SESSION['uso_login_basico']) && $_SESSION['uso_login_basico']);
	}
	
	//-------------------------------------------------------------------------------------------------------------------------------------------------------//
	//							METODOS PROTEGIDOS
	//-------------------------------------------------------------------------------------------------------------------------------------------------------//
	protected function iniciar_pedido_cas()
	{
		$this->instanciar_cliente_cas(); 

		//$this->activar_debug();
		phpCAS::setExtraCurlOption(CURLOPT_SSLVERSION, 3);
		// Se genera la URL de servicio
		$url = $this->generar_url(array());
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
		$datos_usuario = toba::instancia()->get_info_autenticacion($id_usuario);
		if (! isset($datos_usuario)) {													//El usuario no existe en la bd de toba.
				toba::logger()->crit("El usuario CAS '$id_usuario' no existe en la instancia toba");
				throw new toba_error_autenticacion('El usuario que se especifico no existe');
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
	
	private function generar_url($params) 
	{
		$url = toba_http::get_protocolo();
		$url .= toba_http::get_nombre_servidor();
		$url .= ":{$_SERVER['SERVER_PORT']}";
		$url .= $this->strleft($_SERVER['REQUEST_URI'], '?');
		
		$param = array();
		foreach(array_reverse($params) as $key => $valor) {
			if (isset($valor)) {
				$arranque_var = substr($key, 0, 1);
				$clave_final = ($arranque_var == '_') ? $key : "_$key";
				$param[] = urlencode($clave_final) . '=' . urlencode($valor);
			}
		}
		if (! empty($param)) {
			$url .= '?' . implode('&', $param);
		}
		return $url;
	}
	
	private function strleft($s1, $s2) 
	{
		$length = strpos($s1, $s2);
		if ($length !== false) {
			return substr($s1, 0, $length);
		} 		
		return $s1;
	}
}
?>
