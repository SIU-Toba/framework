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
	//----------------------------
	function get_fecha_vencimiento();
	function get_horario_permitido();
	function get_dias_semana_permitidos();
	function get_ip_permitida();
}
?>