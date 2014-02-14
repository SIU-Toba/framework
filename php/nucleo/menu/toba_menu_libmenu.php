<?php

/**
 * Menu CSS basado en SimpleMenu
 * @package SalidaGrafica
 */
class toba_menu_libmenu extends toba_menu
{
	private $prof=1;
	private $arbol;
	protected $imagen_nodo ;
	protected $hay_algun_item = false;
	protected $abrir_nueva_ventana = false;
	protected $imagen_nueva_ventana;
	protected $celda_memoria = 'paralela';

	function __construct($carga_inicial = true)
	{
		parent::__construct($carga_inicial);
		$this->imagen_nodo = toba_recurso::imagen_toba('nucleo/menu_nodo_css.gif', false);
	}

	function set_abrir_nueva_ventana($imagen='nucleo/abrir_nueva_ventana.gif')
	{
		if (toba::memoria()->get_celda_memoria_actual_id() != $this->celda_memoria) {
		   $this->abrir_nueva_ventana = true;
		   $this->imagen_nueva_ventana = toba_recurso::imagen_toba($imagen, false);
		}
	}

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
		//-- Recorro para encontrar la raiz
		for ($i=0;$i<count($this->items);$i++) {
			//--- Se recorre el primer nivel
			if ($this->items[ $i ]['es_primer_nivel']) {
			   $this->get_padres($i);
			}
		}
		$this->arbol .= "</div>";
	}

	protected function get_padres($nodo)
	{
		$inden = str_repeat("\t",$this->prof );
		$clase_base = ($this->prof == 1) ? 'm_r' : '';
		if (!$this->items[$nodo]['carpeta']) {
			/*$vinculo = toba::vinculador()->get_url($this->items[$nodo]['proyecto'],
												 $this->items[$nodo]['item'], array(),
												 array('validar' => false, 'menu' => true, 'zona' => false));*/

			$proyecto = $this->items[$nodo]['proyecto'];
			$item = $this->items[$nodo]['item'];
			$js = '';
			if (isset($this->items[$nodo]['js'])) {
				$js = $this->items[$nodo]['js'];
			}  elseif (! $this->modo_prueba) {
				$js = "return toba.ir_a_operacion(\"$proyecto\", \"$item\", false)";
			}
			$this->arbol .= $inden . "<div class='m_o $clase_base' onclick='$js'>";
			if (!isset($this->items[$nodo]['js']) && $this->abrir_nueva_ventana) {
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
			$rs = $this->get_hijos ($nodo);
			for ($i=0;$i<count($rs);$i++) {
				$this->prof++;
				$this->get_padres($rs[ $i ]);
				$this->prof--;
			}
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

	protected function get_hijos($nodo)
	{
		$hijos = array();
		for ($i=0;$i<count($this->items);$i++) {
			if ($this->items[ $i ]['padre'] == $this->items[ $nodo ][ 'item' ])  {
				$hijos[] = $i;
			}
		}
		return $hijos;
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
