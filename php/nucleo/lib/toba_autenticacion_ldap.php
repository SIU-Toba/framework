<?php 

class toba_autenticacion_ldap implements toba_autenticable
{
	protected $server;
	protected $dn;
	
	function __construct($server, $dn) {
		$this->server = $server;
		$this->dn = $dn;
	}
	
	/**
	*	Realiza la autentificacion utilizando un servidor LDAP
	*	@return $value	Retorna TRUE o FALSE de acuerdo al estado de la autentifiacion
	*/
	function autenticar($id_usuario, $clave, $datos_iniciales=null)
	{	
		if (! extension_loaded('ldap')) {
			throw new toba_error("[Autenticaci�n LDAP] no se encuentra habilitada la extensi�n LDAP");	
		}
		
		$conexion = @ldap_connect($this->server);
		ldap_set_option($conexion, LDAP_OPT_PROTOCOL_VERSION, 3);
		if (! $conexion) {
			toba::logger()->error('[Autenticaci�n LDAP] No es posible conectarse con el servidor: '.ldap_error($conexion));
			return false;
		}
		$bind = @ldap_bind($conexion);
		if (! $bind) {
			toba::logger()->error('[Autenticaci�n LDAP] No es posible conectarse con el servidor: '.ldap_error($conexion));
			return false;
		}
		$res_id = @ldap_search($conexion, $this->dn, "uid=$id_usuario");
		if (! $res_id) {
			toba::logger()->error('[Autenticaci�n LDAP] Fallo b�squeda en el �rbol: '.ldap_error($conexion));
			return false;
		}
		$cantidad = ldap_count_entries($conexion, $res_id);
		if ($cantidad == 0) {
			toba::logger()->error("[Autenticaci�n LDAP] El usuario $id_usuario no tiene una entrada en el �rbol");
			return false;
		}
		if ($cantidad > 1) {
			toba::logger()->error("[Autenticaci�n LDAP] El usuario $id_usuario tiene m�s de una entrada en el �rbol");
			return false;
		}
		$entrada_id = ldap_first_entry($conexion, $res_id);
		if ($entrada_id == false) {
			toba::logger()->error("[Autenticaci�n LDAP] No puede obtenerse el resultado de la b�squeda". ldap_error($conexion));   	
			return false;
		}
		$usuario_dn = ldap_get_dn($conexion, $entrada_id);
		if ($usuario_dn == false) {
			toba::logger()->error("[Autenticaci�n LDAP] No pude obtenerse el DN del usuario: ". ldap_error($conexion));   	
			return false;
		}
		$link_id = @ldap_bind($conexion, $usuario_dn, $clave);
		if ($link_id == false) {
			toba::logger()->error("[Autenticaci�n LDAP] Usuario/Contrase�a incorrecta: ".ldap_error($conexion));
			return false;
		}
		ldap_close($conexion);
		toba::logger()->debug("[Autenticaci�n LDAP] OK");
		return true;
	}
		
	
	
	
}

?>