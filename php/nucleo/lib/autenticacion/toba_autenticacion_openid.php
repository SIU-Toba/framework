<?php
/**
 * @package Centrales
 */
class toba_autenticacion_openid  extends toba_autenticacion implements toba_autenticable
{
	protected $campos = array();
	protected $providers = array();
	protected $permite_login_toba = false;
	protected $crear_usuario = false;
	protected $crear_usuario_proyecto = null;
	protected $crear_usuario_perfil_funcional = null;
	protected $campo_usuario;
	protected $campo_nombre;
	
	function __construct($campos = null, $providers = null, $permite_login_toba = false) 
	{
		$this->campos = $campos;
		$this->providers = $providers;
		$this->permite_login_toba = false;
		
		//--- Levanto la CONFIGURACION de ldap.ini
		$archivo_ini_instalacion = toba::nucleo()->toba_instalacion_dir().'/openid.ini';
		if (is_file( $archivo_ini_instalacion ) ) {
			$parametros = parse_ini_file( $archivo_ini_instalacion,true);
			$sufijo = 'provider_';
			foreach ($parametros as $campo => $valor) {
				if (substr($campo, 0, strlen($sufijo)) == $sufijo) {
					$valor['id'] = substr($campo, strlen($sufijo));				
					$this->providers[$valor['id']] = $valor;
				}
			}			
			$sufijo = 'campo_';
			foreach ($parametros as $campo => $valor) {
				if (substr($campo, 0, strlen($sufijo)) == $sufijo) {
					$this->campos[substr($campo, strlen($sufijo))] = $valor;
				}
			}		
			if (isset($parametros['basicos']['permite_login_toba'])) {
				$this->permite_login_toba = $parametros['basicos']['permite_login_toba'];
			}
			if (isset($parametros['basicos']['crear_usuario'])) {
				$this->crear_usuario = $parametros['basicos']['crear_usuario'];
				if (isset($parametros['basicos']['crear_usuario_proyecto'])) {
					$this->crear_usuario_proyecto =  $parametros['basicos']['crear_usuario_proyecto'];
				} 
				$this->crear_usuario_perfil_funcional = $parametros['basicos']['crear_usuario_perfil_funcional'];
			}
			$this->campo_usuario = $parametros['basicos']['campo_usuario'];
			if (isset($parametros['basicos']['campo_nombre'])) {
				$this->campo_nombre = explode(' ', trim($parametros['basicos']['campo_nombre']));
			} else {
				$this->campo_nombre = array($this->campo_usuario);
			}			
					
		}
	}
	
	function autenticar($id_usuario, $clave, $datos_iniciales=null)
	{
		if (isset($_SESSION['openid'])) {
			if (!isset($_SESSION['openid']['validated'])
				|| !$_SESSION['openid']['validated']) {
				unset($_SESSION['openid']);
				throw new toba_error_autenticacion("Acceso a la cuenta no autorizado. Por favor intente nuevamente");
			}			
			//Vuelta del servicio, existe el usuario?
			$datos_usuario = toba::instancia()->get_info_autenticacion($id_usuario);
			if (!isset($datos_usuario) 
					&& $this->crear_usuario
					&& (! isset($this->crear_usuario_proyecto) || $this->crear_usuario_proyecto == toba::proyecto()->get_id())) {
				//Dar de alta el usuario en el proyecto actual
				$this->alta_usuario($id_usuario, $clave, $this->crear_usuario_perfil_funcional);
			}
			//Validar el login usando el identity como clave
			return toba::manejador_sesiones()->invocar_autenticar($id_usuario, $clave, $datos_iniciales);
		}
				
		if (! isset($_SESSION['openid_url_actual'])) {
			//Login normal
			return toba::manejador_sesiones()->invocar_autenticar($id_usuario, $clave, $datos_iniciales);
		} else {
			//Inicio login openid
			$this->iniciar_pedido($_SESSION['openid_url_actual']);
		}
	}	
	
	function logout()
	{
		//Definicion para completar API. Se podria implementar el dia que OpenID estandarice el logout, hoy cada provider lo hace en url distinta o no lo hace.
		if ($this->uso_login_basico() && $this->permite_login_toba()) {				//Si es login toba no redirecciono al servidor CAS
			$this->eliminar_marca_login(self::$marca_login_basico);
		} else {
			unset($_SESSION['openid_url_actual']);
		}
	}
	
	protected function alta_usuario($id_usuario, $clave, $perfil)
	{
		$nombre = "";
		foreach ($this->campo_nombre as $campo) {
			$valor = $this->get_valor($campo);
			if (isset($valor)) {
				$nombre .= $valor. ' ';
			} 
		}
		$nombre = trim($nombre);
		if ($nombre == '') {
			$nombre = $id_usuario; //Fallback en caso que el nombre no venga en el AX
		}
		toba::instancia()->agregar_usuario($id_usuario, $nombre, $clave);
		toba::instancia()->vincular_usuario(toba::proyecto()->get_id(), $id_usuario, $perfil, array(), false);
	} 
	
	function set_provider($datos)
	{
		$providers = $this->get_providers();
		$personalizable = false;
		foreach ($providers as $id => $provider) {
			if ($id == $datos['provider'] && isset($provider['personalizable']) && $provider['personalizable']) {
				$personalizable = true;
			}
		}
		if ($personalizable) {
			$_SESSION['openid_url_actual'] = $datos['provider_url'];
		} else {
			$_SESSION['openid_url_actual'] = $providers[$datos['provider']]['url'];
		}
	}
	
	function permite_login_toba() 
	{
		return $this->permite_login_toba;
	}
	
	function get_providers() 
	{
		return $this->providers;
	}	
	
	function get_valor($campo)
	{
		if (isset($_SESSION['openid']['ax']['data'][$campo])) {
			$valor = $_SESSION['openid']['ax']['data'][$campo];
			$enc = mb_detect_encoding($valor);
			if (strtolower(substr($enc, 0, 8)) != 'iso-8859') {
				$valor = iconv($enc, 'iso-8859-1', $_SESSION['openid']['ax']['data'][$campo]);
			}			
			return $valor;
		} else {
			return null;
		}	
	}
	
	function verificar_acceso($datos_iniciales = null) 
	{
		if(isset($_REQUEST['openid_mode'])) {
			$this->iniciar_pedido();
		}
		if(isset($_SESSION['openid']) && isset($_SESSION['openid_url_actual'])) {
			if(isset($_SESSION['openid']['error'])) {
				$error = $_SESSION['openid']['error'];
				unset($_SESSION['openid']);
				throw new toba_error_autenticacion($error);
			}
			$usuario = $this->get_valor($this->campo_usuario);
			$clave = isset($_SESSION['openid']['identity']) ? $_SESSION['openid']['identity'] : null;
			$clave .= $_SESSION['openid_url_actual'];
			$this->iniciar_sesion($usuario, $clave, $datos_iniciales);
		}		
	}
	
	function iniciar_sesion($usuario, $clave, $datos_iniciales = null)
	{
		toba::manejador_sesiones()->login($usuario, $clave, $datos_iniciales);
	}
	
	function verificar_clave_vencida($id_usuario)
	{
		return false;
	}
	
	function verificar_logout()
	{
		
	}
	
	protected function iniciar_pedido($url=null) 
	{
		global $_POIDSY;
		$opcionales = array();
		$requeridos = array();
		foreach ($this->campos as $nombre => $opciones) {
			if ($opciones['requerido']) {
				$requeridos[] = $nombre;
			} else {
				$opcionales[] = $nombre;
			}
		}
		if (! empty($opcionales)) {
			// Request some information from the identity provider. Note that providers
			// don't have to implement the extension that provides this, so you can't
			// rely on getting results back.			
			define('OPENID_SREG_OPTIONAL', implode(',', $opcionales));
		}
		if (isset($url)) {
			define('OPENID_URL', $url);
		}
		require(toba_dir().'/php/3ros/poidsy/sreg.ext.php');
		require(toba_dir().'/php/3ros/poidsy/ax.ext.php');
		 
		foreach ($requeridos as $campo) {
			$atributo = $this->campos[$campo]['atributo'];
			AttributeExchange::addRequiredType($campo, constant("AttributeExchange::$atributo"));
		}
		foreach ($opcionales as $campo) {
			$atributo = $this->campos[$campo]['atributo'];
			AttributeExchange::addOptionalType($campo, constant("AttributeExchange::$atributo"));
		}		
		
		$res =  toba_http::get_url_actual();
		$p = toba::proyecto()->get_www();
		if (isset($p['url']) && trim($p['url']) != '') {
			$res .= '/' . $p['url'];
		}
		define('OPENID_RETURN_URL', $res.'/');
		require(toba_dir().'/php/3ros/poidsy/processor.php');
	}
	
	
}
?>