<?php
/**
 * Interfaz que deben implementar aquellos que deseen configurar las opciones
 * generales de los graficos.
 */
interface toba_grafico_conf
{
    function get_color_fondo();
	function get_siguiente_color();
	function tiene_img_fondo();
	function get_img_fondo();
	function get_fuente();
}
?>
