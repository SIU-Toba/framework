<?php
class toba_autenticacion
{
	static $marca_login_basico = 'uso_login_basico';
	static $marca_login_central = 'uso_login_central';
	static $metodos_centralizados = array('cas','saml', 'saml_onelogin');
	static $session_atributos_usuario = 'auth_atributos_usuario';
	static $session_usuarios_posibles = 'userAccounts';
	static $modo_debug=false;
	
	static protected $atributos_validos_usuario = [
				'uid', 'uniqueIdentifier',
				'appLauncherData', 'userAccounts', 
				'defaultUserAccount'];
	protected $parametros_url;
	protected $atributos_usuario;
	
	static function es_autenticacion_centralizada($id)
	{
		return in_array($id, self::$metodos_centralizados);
	}

	static function set_modo_debug($activo)
	{
		self::$modo_debug = $activo;
	}
	
	function set_parametros_url($parametros)
	{
		$this->parametros_url = $parametros;
	}
	
	function usar_login_basico()
	{
		$this->setear_marca_login(self::$marca_login_basico);
	}
	
	function uso_login_basico()
	{
		return $this->verificar_marca_login(self::$marca_login_basico);
	}
	
	function usar_login_centralizado()
	{
		$this->setear_marca_login(self::$marca_login_central);
	}
	
	function uso_login_centralizado()
	{
		return $this->verificar_marca_login(self::$marca_login_central);
	}
	
	function eliminar_login_basico()
	{
		$this->eliminar_marca_login(self::$marca_login_basico);
	}
			
	// --- Funciones que trabajan sobre la session de PHP, debido a que la memoria de Toba no alcanza a guardarse en este tipo de autenticacion.
	protected function eliminar_marca_login($marca)
	{
		if (isset($_SESSION[$marca])) {
			unset($_SESSION[$marca]);
		}
	}
	
	protected function setear_marca_login($marca)
	{
		$_SESSION[$marca] = 'true';
	}
	
	protected function verificar_marca_login($marca)
	{
		return (isset($_SESSION[$marca]) && $_SESSION[$marca]);
	}
			
	protected function generar_url($params) 
	{
		$url = toba_http::get_url_actual(false, true);
		$query_string = array();
		foreach($params as $key => $valor) {
			if (isset($valor) && trim($valor) != '') {
				$query_string[] = urlencode($key) .'='. urlencode($valor);
			}
		}		
		
		if (! empty($query_string)) {
			$url .= '?'. implode('&', $query_string);
		}
		return $url;
	}	
	
	protected function get_subclase_usuario_proyecto()
	{
		$subclase = 'toba_usuario';
		$archivo = toba::proyecto()->get_parametro('usuario_subclase_archivo');
		$pm = toba::proyecto()->get_parametro('pm_usuario');
		if (trim($archivo) != '') {
			toba_cargador::cargar_clase_archivo($pm, $archivo, toba::proyecto()->get_id());
			$subclase = toba::proyecto()->get_parametro('usuario_subclase');	
		}
		return $subclase;
	}
	
	// metodos sobre los atributos del usuario
	
	protected function set_atributos_usuario($atributos_usuario)
	{
		$claves = \array_fill_keys(self::$atributos_validos_usuario, 1);
		$this->atributos_usuario = array_intersect_key($atributos_usuario, $claves);
		$_SESSION[self::$session_atributos_usuario] = $this->atributos_usuario;
	}
	
	function get_atributos_usuario() 
	{
		if (! isset($this->atributos_usuario)) {
			$this->atributos_usuario = (isset ($_SESSION[self::$session_atributos_usuario])) ? $_SESSION[self::$session_atributos_usuario] : array();
		}
		return $this->atributos_usuario;
	}
	
	/**
	 * Recupera los ids de las cuentas alternativas del usuario desde los datos que envio el IDP
	 * @return array
	 */
	function get_lista_cuentas_posibles()
	{
		$datos = $this->get_atributos_usuario();
		if (! empty($datos) && isset($datos[self::$session_usuarios_posibles])) {
			return $datos[self::$session_usuarios_posibles];
		}
		return array();
	}
	
	function get_id_usuario_arai()
	{
		$atributos = $this->get_atributos_usuario();
		if (isset($atributos['uniqueIdentifier']) && ! empty($atributos['uniqueIdentifier'])) {
			return  utf8_d_seguro($atributos['uniqueIdentifier'][0]);
		}
	}	
}
?>
