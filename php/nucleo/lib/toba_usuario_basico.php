<?php
/**
 * Usuario estandar de la instancia
 * @package Seguridad
 * @subpackage TiposUsuario
 */
class toba_usuario_basico extends toba_usuario
{
	protected $datos_basicos;
	protected $grupos_acceso;
	protected $perfil_datos;
	protected $restricciones_funcionales;

	/**
	*	Realiza la autentificacion.
	*	@return $value	Retorna TRUE o FALSE de acuerdo al estado de la autentifiacion
	*/
	static function autenticar($id_usuario, $clave, $datos_iniciales=null, $usar_log=true)
	{
		/*if (self::autenticar_ldap($id_usuario, $clave, $datos_iniciales)) {
			return true;	
		}*/
		$datos_usuario = toba::instancia()->get_info_autenticacion($id_usuario);
		if ( empty($datos_usuario) ) {
			if ($usar_log) {
				toba::logger()->error("El usuario '$id_usuario' no existe", 'toba');
			}
			return false;
		} else {
			//--- Autentificación
			$algoritmo = $datos_usuario['autentificacion'];
			if ($algoritmo != 'plano')  { 
				if ($algoritmo == 'md5') {
					$clave = hash($algoritmo, $clave);
				} else {
					$clave = encriptar_con_sal($clave, $algoritmo, $datos_usuario['clave']);
				}
			}
			if( !($datos_usuario['clave'] === $clave) ) {
				if ($usar_log) {
					toba::logger()->error("El usuario '$id_usuario' ingreso una clave incorrecta", 'toba');
				}
				return false;
			}
		}
		return true;
	}
	
	//----------------------------------------------------------------------------------
	
	/**
	*	Realiza la autentificacion utilizando un servidor LDAP
	*	@return $value	Retorna TRUE o FALSE de acuerdo al estado de la autentifiacion
	*/
	static function autenticar_ldap($id_usuario, $clave, $datos_iniciales=null)
	{	
		if (! extension_loaded('ldap')) {
			throw new toba_error("[Autenticación LDAP] no se encuentra habilitada la extensión LDAP");	
		}
		$dn = 'dc=siu,dc=edu,dc=ar';
		$host = 'localhost';
		
		$conexion = @ldap_connect($host);
		ldap_set_option($conexion, LDAP_OPT_PROTOCOL_VERSION, 3);
		if (! $conexion) {
			toba::logger()->error('[Autenticación LDAP] No es posible conectarse con el servidor: '.ldap_error($conexion));
			return false;
		}
		$bind = @ldap_bind($conexion);
		if (! $bind) {
			toba::logger()->error('[Autenticación LDAP] No es posible conectarse con el servidor: '.ldap_error($conexion));
			return false;
		}
		$res_id = @ldap_search($conexion, "$dn", "uid=$id_usuario");
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
		toba::logger()->debug("[Autenticación LDAP] OK");
		return true;
	}
	
	//----------------------------------------------------------------------------------
	
	function __construct($id_usuario)
	{
		$this->datos_basicos = toba::instancia()->get_info_usuario($id_usuario);
		$this->grupos_acceso = toba::instancia()->get_perfiles_funcionales( $id_usuario, toba::proyecto()->get_id() );
		$this->perfil_datos = toba_proyecto_implementacion::get_perfil_datos( $id_usuario, toba::proyecto()->get_id() );
		$this->restricciones_funcionales = toba_proyecto_implementacion::get_restricciones_funcionales( $this->grupos_acceso, toba::proyecto()->get_id() );
	}

	/**
	*	Retorna el identificador del usuario
	*/
	function get_id()
	{
		return $this->datos_basicos['id'];
	}

	/**
	*	Retorna el nombre del usuario
	*/
	function get_nombre()
	{
		return $this->datos_basicos['nombre'];
	}
	
	//-------------------------------------------------------
	//----- Perfil
	//-------------------------------------------------------
	
	/**
	*	Retorna un array de perfiles funcionales a los que el usuario actual tiene acceso en este proyecto
	*	@return Array de perfiles funcionales
	*/
	function get_perfiles_funcionales()
	{
		return $this->grupos_acceso;
	}	
	
	/**
	*	@deprecated Desde 1.5 usar get_perfiles_funcionales
	*/
	function get_grupos_acceso()
	{
		return $this->get_perfiles_funcionales();
	}

	function get_perfil_datos()
	{
		return $this->perfil_datos;
	}
	
	function get_restricciones_funcionales()
	{
		return $this->restricciones_funcionales;
	}

	//-------------------------------------------------------
	//----- Parametros
	//-------------------------------------------------------

	/*
	*	Devuelve el valor contenido en el parametro de usuario especificado.
	*	@param 	$parametro	char	Identificador del parametro de usuario, las opciones validas son 'A','B' o 'C'
	*	@return $value	Retorna el valor del parametro o null si es que no se encuentra seteado.
	*/
	function get_parametro($parametro)
	{
		$parametro = strtolower(trim($parametro));
		if ( !($parametro=='a'||$parametro=='b'||$parametro=='c') ) {
			throw new toba_error("Consulta de parametro de usuario: El parametro '$parametro' es invalido.");	
		}
		//Las opciones correctas son 'a','b' o 'c'
		$nombre_parametro = 'parametro_'. $parametro;	
		if (isset($this->datos_basicos[$nombre_parametro])){
			return $this->datos_basicos[$nombre_parametro];
		}else{
			return null;
		}
	}

	//-------------------------------------------------------
	//----- Bloqueo de USUARIOS y de IPs
	//-------------------------------------------------------

	static function es_ip_rechazada($ip)
	{
		return toba::instancia()->es_ip_rechazada($ip);
	}
	
	static function registrar_error_login($usuario, $ip, $texto)
	{
		return toba::instancia()->registrar_error_login($usuario, $ip, $texto);
	}

	static function bloquear_ip($ip)
	{
		return toba::instancia()->bloquear_ip($ip);
	}
	
	static function get_cantidad_intentos_en_ventana_temporal($ip, $ventana_temporal=null)
	{
		return toba::instancia()->get_cantidad_intentos_en_ventana_temporal($ip, $ventana_temporal);
	}
	
	//-------------------- Bloqueo de Usuarios en LOGIN  ----------------------------
	
	static function get_cantidad_intentos_usuario_en_ventana_temporal($usuario, $ventana_temporal=null)
	{
		return toba::instancia()->get_cantidad_intentos_usuario_en_ventana_temporal($usuario, $ventana_temporal);
	}
	
	static function bloquear_usuario($usuario)
	{
		return toba::instancia()->bloquear_usuario($usuario);
	}
	
	static function es_usuario_bloqueado($usuario)
	{
		return toba::instancia()->es_usuario_bloqueado($usuario);		
	}
}
?>