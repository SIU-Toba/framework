<?php

/**
 * Clase base de los menus de aplicacion
 *
 * @package SalidaGrafica
 */
abstract class toba_menu
{
	protected $items;
	
	function __construct()
	{
		$this->items = $this->items_de_menu();		
	}
	
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
	
	function set_datos_opcion($id_item, $datos)
	{
		$ok = false;
		for ($i = 0; $i < count($this->items); $i++) {
			if ($this->items[$i]['item'] == $id_item) {
				$this->items[$i] = array_merge($this->items[$i], $datos);
				$ok = true;				
			}
		}
		if (! $ok) {
			toba::logger()->warning("Se intento modificar la opci�n de men� '$id_item', pero la misma no se encuenta en el men�");
		}
	}
	
	function agregar_opcion($datos)
	{
		if (! isset($datos['carpeta'])) {
			$datos['carpeta'] = false;
		}
		if (! isset($datos['es_primer_nivel'])) {
			$datos['es_primer_nivel'] = false;
		}		
		if (! isset($datos['padre'])) {
			$datos['es_primer_nivel'] = true;
			$datos['padre'] = null;
		}		
		if (! isset($datos['proyecto'])) {
			$datos['proyecto'] = toba::proyecto()->get_id();
		}	
		if (! isset($datos['item'])) {
			$datos['item'] = toba::solicitud()->get_datos_item('item');
		}	
		$this->items[] = $datos;
	}
	
	function quitar_opcion($id_item)
	{
		$ok = false;
		for ($i = 0; $i < count($this->items); $i++) {
			if ($this->items[$i]['item'] == $id_item) {
				array_splice($this->items, $i, 1);
				$ok = true;				
			}
		}
		if (! $ok) {
			toba::logger()->warning("Se intento quitar la opci�n de men� '$id_item', pero la misma no se encuenta en el men�");
		}
	}
	
	
	/**
	 * Muestra una confirmaci�n antes de navegar a cualquier opci�n del men�
	 *
	 * @param string $mensaje Mensaje que se utiliza para la confirmaci�n
	 * @param boolean $forzar Si es verdadero siempre muestra la confirmaci�n, sino depende de si alg�n ef de alg�n formulario fue modificado 
	 */
	function set_modo_confirmacion($mensaje, $forzar=true)
	{
		echo toba_js::abrir();
		$confirmar = toba_js::bool($forzar);
		
		//TODO: Hack para conservar la zona, cambiar con #662
		//echo "var toba_zona= ";
				
		echo "
			function confirmar_cambios(proyecto, operacion, url, es_poup) {
				var confirmar =  $confirmar;
				if (confirmar) {
					return confirm('$mensaje');
				} else {
					return true;
				}
			}
			toba.set_callback_menu(confirmar_cambios);		
		";
		echo toba_js::cerrar();
	}
	
}
?>