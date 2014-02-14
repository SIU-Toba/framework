<?php

/**
 * Clase base de los menus de aplicacion
 *
 * @package SalidaGrafica
 */
abstract class toba_menu
{
	protected $items = array();
	protected $modo_prueba = false;
	
	function __construct($carga_inicial = true)
	{
		if ($carga_inicial) {
			$this->items = $this->items_de_menu();		
		}
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
			toba::logger()->warning("Se intento modificar la opción de menú '$id_item', pero la misma no se encuenta en el menú");
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
			toba::logger()->warning("Se intento quitar la opción de menú '$id_item', pero la misma no se encuenta en el menú");
		}
	}
	
	/**
	 * @ignore
	 */
	function set_modo_prueba()
	{
		$this->modo_prueba = true;
	}
	
	
	/**
	 * Muestra una confirmación antes de navegar a cualquier opción del menú
	 *
	 * @param string $mensaje Mensaje que se utiliza para la confirmación
	 * @param boolean $forzar Si es verdadero siempre muestra la confirmación, sino depende de si algún ef de algún formulario fue modificado 
	 */
	function set_modo_confirmacion($mensaje, $forzar=true)
	{
		if ($this->menu_enviado) {
					echo toba_js::abrir();
					if ($forzar) {
						$confirmar = "var confirmar = true;";
					} else {
						$confirmar = "
							var confirmar = toba.hay_cambios();
						";
					}

					echo "
						function confirmar_cambios(proyecto, operacion, url, es_popup) {
							if (! es_popup || typeof(es_popup) == 'undefined') {
								$confirmar
								if (confirmar) {
									return confirm('$mensaje');
								} else {
									return true;
								}
							} else {
								return true;
							}
						}
						toba.set_callback_menu(confirmar_cambios);
					";
					echo toba_js::cerrar();
			}
	}
	
}
?>