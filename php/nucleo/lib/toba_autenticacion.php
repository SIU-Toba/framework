<?php
class toba_autenticacion
{
	static $marca_login_basico = 'uso_login_basico';
	static $marca_login_central = 'uso_login_central';
	protected $parametros_url;

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
		$url = toba_http::get_protocolo();
		$url .= toba_http::get_nombre_servidor();
		$url .= ":{$_SERVER['SERVER_PORT']}";
		$url .= $this->strleft($_SERVER['REQUEST_URI'], '?');
		
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
	
	protected function strleft($s1, $s2) 
	{
		$length = strpos($s1, $s2);
		if ($length !== false) {
			return substr($s1, 0, $length);
		} 		
		return $s1;
	}
}
?>