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
	 * @ventana
	 */
	function plantilla_css()
	{
		return "";
	}
	
	/**
	 * Muestra el contenido del menu
	 */
	abstract function mostrar();
	
	protected function items_de_menu()
	{
		return toba::proyecto()->get_items_menu();
	}
	
}
?>