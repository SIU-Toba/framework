<?php

/**
 * Menu CSS basado en SimpleMenu
 * @package SalidaGrafica
 */
class toba_menu_libmenu extends toba_menu
{
	private $arbol;

	function plantilla_css()
	{
		return "libmenu_horizontal";
	}

	//-----------------------------------------------------------

	protected function preparar_arbol()
	{
		if (toba::memoria()->get_celda_memoria_actual_id() != 'paralela') {
			  $this->arbol .= toba_js::abrir();
			  $this->arbol .= '
					  function on_menu_set_popup_on(e) {
							  var id = (window.event) ? event.keyCode : e.keyCode;
							  if (id == 16) {
									  toba.set_menu_popup(true);
							  }
					  }
					  function on_menu_set_popup_off(e) {
							  var id = (window.event) ? event.keyCode : e.keyCode;
							  if (id == 16) {
									  toba.set_menu_popup(false);
							  }
					  }
					  agregarEvento(document, "keyup", on_menu_set_popup_off);
					  agregarEvento(document, "keydown", on_menu_set_popup_on);
			  ';
			  $this->arbol .= toba_js::cerrar();
		}

		$id_tag = ($this->modo_prueba) ? 'prueba' : 'id_menu';
		$this->arbol .= "\n<div class='m_m' id='$id_tag' style=''>\n";
		$this->buscar_raiz();
		$this->arbol .= "</div>";
	}

	protected function get_padres($nodo)
	{
		$inden = str_repeat("\t",$this->prof );
		$clase_base = ($this->prof == 1) ? 'm_r' : '';
		if (!$this->items[$nodo]['carpeta']) {
			$js = '';
			$proyecto = $this->items[$nodo]['proyecto'];
			$item = $this->items[$nodo]['item'];
			if (isset($this->items[$nodo]['js'])) {
				$js = $this->items[$nodo]['js'];
			}  elseif (! $this->modo_prueba) {
				$js = "return toba.ir_a_operacion(\"$proyecto\", \"$item\", false)";
			}
			$this->arbol .= $inden . "<div class='m_o $clase_base' onclick='$js'>";
			if ($this->item_abre_popup($nodo)) {
				$this->arbol .= '<img title="Abrir la operación en paralelo a la actual" class="menu-link-nueva-ventana" src="'. $this->imagen_nueva_ventana. '" ';
				$this->arbol .= " onclick='return toba.ir_a_operacion(\"$proyecto\", \"$item\", true)' />";
			}
			$this->arbol .= $this->get_imagen($nodo).$this->items[$nodo]['nombre'];
			$this->arbol .= $inden . "</div>\n";
			$this->hay_algun_item = true;
		} else {
			//Es carpeta
			$this->arbol .= $inden . "<div class='m_s $clase_base'>\n" .
										"<div class='m_e $clase_base'>" .
											$this->get_imagen($nodo). $this->items[$nodo]['nombre'] .
										"</div>";
			$this->arbol .= $inden . "<div class='m_n'>\n";
			$this->recorrer_hijos($nodo);
			$this->arbol .= $inden . "</div>\n";
			$this->arbol .= $inden . "</div>\n";
		}
	}

	protected function get_imagen($nodo)
	{
		$img = '';
		if (isset($this->items[$nodo]['imagen'])) {
			$url_img = toba_recurso::imagen_de_origen($this->items[$nodo]['imagen'],
									$this->items[$nodo]['imagen_recurso_origen']);
			$img .= "<img src='$url_img' border=0 alt='' /> ";
		} else {
			//$url_img = toba_recurso::imagen_toba('nulo.gif');
			//$img .= "<img src='$url_img' width=1 height=16 border=0 alt='' />";
		}
		return $img;
	}

	//-----------------------------------------------------------

	function mostrar()
	{
		$this->preparar_arbol();
		echo $this->arbol;
		if ($this->hay_algun_item) {
			$id_tag = ($this->modo_prueba) ? 'prueba' : 'id_menu';
			toba_js::cargar_consumos_globales(array('basicos/libmenu'));
			echo toba_js::ejecutar("var menu = Menu(); menu.inicializarMenu('$id_tag');");
		}
	}
}

?>
