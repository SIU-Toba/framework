<?php

/**
 * Clase base de los menus de aplicacion
 *
 * @package SalidaGrafica
 */
abstract class toba_menu
{
	/**
	 * Ventana para retornar nombre de los .css a incluir
	 */
	function plantilla_css()
	{
		return "";
	}
	
	/**
	 * Muestra el contenido del menu
	 */
	abstract function mostrar();
	
	protected function items_de_menu($solo_primer_nivel=false)
	{
		return toba::proyecto()->items_menu($solo_primer_nivel);
	}	
}
?>