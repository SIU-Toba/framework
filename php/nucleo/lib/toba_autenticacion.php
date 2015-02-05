<?php
class toba_autenticacion
{
	static $marca_login_basico = 'uso_login_basico';
	static $marca_login_central = 'uso_login_central';
	static  $metodos_centralizados = array('cas','saml', 'saml_onelogin');
	protected $parametros_url;

	static function es_autenticacion_centralizada($id)
	{
		return in_array($id, self::$metodos_centralizados);
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
}
?>
