<?php 

class toba_autenticacion_ldap extends toba_autenticacion implements toba_autenticable
{
	protected $server;
	protected $dn;
	protected $bind_dn = null;
	protected $bind_pass = null;
	protected $filter = 'uid=%s';	
	
	function __construct($server=null, $dn=null) {
		$this->server = $server;
		$this->dn = $dn;
		
		//--- Levanto la CONFIGURACION de ldap.ini
		$archivo_ini_instalacion = toba::nucleo()->toba_instalacion_dir().'/ldap.ini';
		if (is_file( $archivo_ini_instalacion ) ) {
			$datos = parse_ini_file( $archivo_ini_instalacion,true);
			if (isset($datos['basicos']['server'])) {
				$this->server = $datos['basicos']['server']; 
			}
			if (isset($datos['basicos']['dn'])) {
				$this->dn = $datos['basicos']['dn'];
			}
			if (isset($datos['basicos']['bind_dn'])) {
				$this->bind_dn = $datos['basicos']['bind_dn'];
			}
			if (isset($datos['basicos']['bind_pass'])) {
				$this->bind_pass = $datos['basicos']['bind_pass'];
			}
			if (isset($datos['basicos']['filter'])) {
				$this->filter = $datos['basicos']['filter'];
			}
		}
	}
	
	/**
	*	Realiza la autentificacion utilizando un servidor LDAP
	*	@return $value	Retorna TRUE o FALSE de acuerdo al estado de la autentifiacion
	*/
	function autenticar($id_usuario, $clave, $datos_iniciales=null)
	{	
		if (! extension_loaded('ldap')) {
			throw new toba_error("[Autenticación LDAP] no se encuentra habilitada la extensión LDAP");	
		}
		
		$conexion = @ldap_connect($this->server);
		ldap_set_option($conexion, LDAP_OPT_PROTOCOL_VERSION, 3);
		if (! $conexion) {
			toba::logger()->error('[Autenticación LDAP] No es posible conectarse con el servidor: '.ldap_error($conexion));
			return false;
		}
		//$bind = @ldap_bind($conexion);
		$bind = @ldap_bind($conexion, $this->bind_dn, $this->bind_pass); 
		if (! $bind) {
			toba::logger()->error('[Autenticación LDAP] No es posible conectarse con el servidor: '.ldap_error($conexion));
			return false;
		}
		$res_id = @ldap_search($conexion, $this->dn, sprintf($this->filter, $id_usuario));
		if (! $res_id) {
			toba::logger()->error('[Autenticación LDAP] Fallo búsqueda en el árbol: '.ldap_error($conexion));
			return false;
		}
		$cantidad = ldap_count_entries($conexion, $res_id);
		if ($cantidad == 0) {
			toba::logger()->error("[Autenticación LDAP] El usuario $id_usuario no tiene una entrada en el árbol");
			return false;
		}
		if ($cantidad > 1) {
			toba::logger()->error("[Autenticación LDAP] El usuario $id_usuario tiene más de una entrada en el árbol");
			return false;
		}
		$entrada_id = ldap_first_entry($conexion, $res_id);
		if ($entrada_id == false) {
			toba::logger()->error("[Autenticación LDAP] No puede obtenerse el resultado de la búsqueda". ldap_error($conexion));   	
			return false;
		}
		$usuario_dn = ldap_get_dn($conexion, $entrada_id);
		if ($usuario_dn == false) {
			toba::logger()->error("[Autenticación LDAP] No pude obtenerse el DN del usuario: ". ldap_error($conexion));   	
			return false;
		}
		$link_id = @ldap_bind($conexion, $usuario_dn, $clave);
		if ($link_id == false) {
			toba::logger()->error("[Autenticación LDAP] Usuario/Contraseña incorrecta: ".ldap_error($conexion));
			return false;
		}
		ldap_close($conexion);		
		$usuario = $this->recuperar_usuario_toba($id_usuario);		
		toba::logger()->debug("[Autenticación LDAP] OK");
		return true;
	}
			
	function verificar_clave_vencida($id_usuario)
	{
		return false;
	}
	
	function logout()
	{
		//Definicion para completar API.
		$this->eliminar_marca_login(self::$marca_login_basico);
		$this->eliminar_marca_login(self::$marca_login_central);
	}	
	
	function verificar_logout()
	{		
	}
	
	protected function recuperar_usuario_toba($id_usuario)
	{
		$datos_usuario = toba::instancia()->get_info_autenticacion($id_usuario);
		if (! isset($datos_usuario)) {													//El usuario no existe en la bd de toba.
				toba::logger()->crit("El usuario LDAP '$id_usuario' no existe en la instancia toba");
				throw new toba_error_autenticacion("El usuario '$id_usuario' no esta dado de alta en el sistema");
		}
		return $id_usuario;
	}	
}

?>