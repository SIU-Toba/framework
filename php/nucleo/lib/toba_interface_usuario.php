<?php

/**
 * Interface que debe implementar una extensión o redefinición de toba::usuario()
 * @package Centrales
 */
interface toba_interface_usuario
{
	static function autenticar($id_usuario, $clave, $datos_iniciales=null);

	function get_id();
	function get_nombre();
	function get_grupo_acceso();
	function get_fecha_vencimiento();
	function get_horario_permitido();
	function get_dias_semana_permitidos();
	function get_ip_permitida();
}
?>