<?php

/**
 * Menu CSS basado en SimpleMenu
 * @package SalidaGrafica
 */
class toba_menu_css extends toba_menu
{
	private $arbol;
	protected $menu_enviado = false;
	
	function plantilla_css()
	{
		return "listmenu_horizontal";
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
		$this->arbol .= '		
				<style type="text/css">
					ul.horizontal .carpeta {
						background-repeat: no-repeat;
						background-position: center right;
						background-image: url("'.$this->imagen_nodo.'");
					}
				</style>
				<!--[if IE 7]>
				<style type="text/css">
					ul.horizontal ul ul {
						margin-left: 200px;
					}
				</style>							
				<![endif]-->
			';
		
		$id_tag = ($this->modo_prueba) ? 'prueba' : 'menu-h';
		$this->arbol .= "\n<ul id='$id_tag'  class='horizontal'>\n";		
		//-- Recorro para encontrar la raiz
		$this->buscar_raiz();
		$this->arbol .= "</ul>";	
	}
	
	protected function get_padres($nodo)
	{
		$inden = str_repeat("\t",$this->prof );
		$clase_base = ($this->prof == 1) ? 'nivel-0' : '';
		
		if (!$this->items[$nodo]['carpeta']) {			
			$js = '';
			$proyecto = $this->items[$nodo]['proyecto'];
			$item = $this->items[$nodo]['item'];
			
			if (isset($this->items[$nodo]['js'])) {
				$js = $this->items[$nodo]['js']; 
			} elseif (! $this->modo_prueba) {
				$js = "return toba.ir_a_operacion(\"$proyecto\", \"$item\", false)";
			}			
			$this->arbol .= $inden . "<li><a class='$clase_base' tabindex='32767' href='#' onclick='$js' title='".$this->items[$nodo]['nombre']."'>";
			
			if ($this->item_abre_popup($nodo)) {
				$this->arbol .= '<img title="Abrir la operación en paralelo a la actual" class="menu-link-nueva-ventana" src="'. $this->imagen_nueva_ventana. '" ';
				$this->arbol .= " onclick='return toba.ir_a_operacion(\"$proyecto\", \"$item\", true)' />";
			}											
			$this->arbol .= $this->get_imagen($nodo).$this->items[$nodo]['nombre'];
			$this->arbol .= '</a>';
			$this->arbol .= $inden . "</li>\n";
			$this->hay_algun_item = true;
		} else {
			//Es carpeta
			$clase_base .= ($this->prof > 1) ? " carpeta" : "";
			$this->arbol .= $inden . "<li><a class='$clase_base'>".$this->get_imagen($nodo). $this->items[$nodo]['nombre'] . "</a>\n";
			$this->arbol .= $inden . "\t<ul>\n";
			$this->recorrer_hijos($nodo);
			$this->arbol .= $inden . "\t</ul>\n";
			$this->arbol .= $inden . "</li>\n";
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
			$url_img = toba_recurso::imagen_toba('nulo.gif');
			$img .= "<img src='$url_img' width=1 height=16 border=0 alt='' />";
		}		
		return $img;
	}
	
	//-----------------------------------------------------------

	function mostrar()
	{
		$nombre_var = ($this->modo_prueba) ? 'prueba_m': 'horizontals';
		$id_tag = ($this->modo_prueba) ? 'prueba' : 'menu-h';
		$this->preparar_arbol();
		echo $this->arbol;
		if ($this->hay_algun_item) {
			toba_js::cargar_consumos_globales(array('basicos/listmenu'));
			echo toba_js::ejecutar("var $nombre_var = new simpleMenu('$id_tag', 'horizontal');");
		}
		$this->menu_enviado = true;
	}
}

?>