<?php
/**
 * Interface que debe implementar una extensión o redefinición de toba::usuario()
 * @package Centrales
 */
interface toba_interface_usuario
{
	static function autenticar($id_usuario, $clave, $datos_iniciales=null);
	/* ID del usuario */
	function get_id();
	/* Nombre descriptivo del usuario */
	function get_nombre();
	/* Array con los grupos de acceso a los que pertenece */
	function get_grupos_acceso();
	//-- Manejo de bloqueos --------------------------------------------
	static function es_ip_rechazada($ip);
	static function registrar_error_login($usuario, $ip, $texto);
	static function bloquear_ip($ip);
	static function get_cantidad_intentos_en_ventana_temporal($ip, $ventana_temporal=null);
	static function get_cantidad_intentos_usuario_en_ventana_temporal($usuario, $ventana_temporal=null);
	static function bloquear_usuario($usuario);
	static function es_usuario_bloqueado($usuario);
	//------------------------------------------------------------------
	function get_fecha_vencimiento();
	function get_horario_permitido();
	function get_dias_semana_permitidos();
	function get_ip_permitida();
}
?>