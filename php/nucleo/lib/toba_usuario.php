<?php
/**
 * Encapsula al usuario actualmente logueado a la instancia
 *
 * Consumir usando toba::usuario()->
 * @package Seguridad
 */
class toba_usuario implements toba_interface_usuario
{
	function __construct($id_usuario)
	{
	}

	static function autenticar($id_usuario, $clave, $datos_inciales=null)
	{
		return false;
	}
	
	function get_id()
	{
		return null;
	}
	
	function get_nombre()
	{
		return null;
	}
	
	function get_grupos_acceso()
	{
		return array();	
	}

	//-------- Bloqueos --------------------------------------

	static function es_ip_rechazada($ip){}
	static function registrar_error_login($usuario, $ip, $texto){}
	static function bloquear_ip($ip){}
	static function get_cantidad_intentos_en_ventana_temporal($ip, $ventana_temporal=null){}
	static function get_cantidad_intentos_usuario_en_ventana_temporal($usuario, $ventana_temporal=null){}
	static function bloquear_usuario($usuario){}
	static function es_usuario_bloqueado($usuario){}

	//-------- Restricciones de acceso ------------------------
	
	function get_restricciones_funcionales()
	{
		return array();
	}

	function get_perfil_datos()
	{
		return null;
	}
}
?>