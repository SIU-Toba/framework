<?php
/**
 * Encapsula al usuario actualmente logueado a la instancia
 * @package Centrales
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
	
	function get_grupo_acceso()
	{
		return null;	
	}

	function get_fecha_vencimiento()
	{
		return null;
	}
	
	function get_horario_permitido()
	{
		return null;
	}

	function get_dias_semana_permitidos()
	{
		return null;
	}
		
	function get_ip_permitida()
	{
		return null;
	}
}
?>