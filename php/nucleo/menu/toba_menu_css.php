<?php

/**
 * Menu CSS basado en SimpleMenu
 * @package SalidaGrafica
 */
class toba_menu_css extends toba_menu
{
	private $items;
	private $prof=1;
	private $arbol;
	protected $imagen_nodo ;
	protected $hay_algun_item = false;
	
	function __construct()
	{
		$this->imagen_nodo = toba_recurso::imagen_toba('nucleo/menu_nodo_css.gif', false);
	}
	
	
	function plantilla_css()
	{
		return "listmenu_horizontal";
	}
	
	//-----------------------------------------------------------
	
	function preparar_arbol()
	{

		$this->arbol .= '<style type="text/css">
							ul.horizontal .carpeta {
								background-repeat: no-repeat;
								background-position: center right;
								background-image: url("'.$this->imagen_nodo.'");
							}
						</style>
						<!--[if gte IE 7]>
						<style type="text/css">
							ul.horizontal ul ul {
								margin-left: 200px;
							}
						</style>							
						<![endif]-->
			';
		
		$this->items = $this->items_de_menu();
		$this->arbol .= "\n<ul id='menu-h'  class='horizontal'>\n";		
		for ($i=0;$i<count($this->items);$i++) {
			//--- Se recorre el primer nivel
			if ($this->items[ $i ]['padre'] == '__raiz__') {
				$this->get_padres($i);
			}
		}
		$this->arbol .= "</ul>";	
	}
	
	function get_padres($nodo)
	{
		$inden = str_repeat("\t",$this->prof );
		$clase_base = ($this->prof == 1) ? 'nivel-0' : '';
		
		if (!$this->items[$nodo]['carpeta']) {
			$vinculo = toba::vinculador()->get_url($this->items[$nodo]['proyecto'],
															 $this->items[$nodo]['item'], array(),
															 array('validar' => false, 'menu' => true, 'zona' => false));
			$proyecto = $this->items[$nodo]['proyecto'];
			$item = $this->items[$nodo]['item'];
			$this->arbol .= $inden . "<li><a class='$clase_base' tabindex='32767' href='$vinculo' " .
							"title='".$this->items[$nodo]['nombre']."'>" . 
							$this->get_imagen($nodo).
							$this->items[$nodo]['nombre']."</a>";
			$this->arbol .= $inden . "</li>\n";
			$this->hay_algun_item = true;
		} else {
			//Es carpeta
			$clase_base .= ($this->prof > 1) ? " carpeta" : "";
			$this->arbol .= $inden . "<li><a class='$clase_base'>".$this->get_imagen($nodo). $this->items[$nodo]['nombre'] . "</a>\n";
			$this->arbol .= $inden . "\t<ul>\n";
			$rs = $this->get_hijos ($nodo);
			for ($i=0;$i<count($rs);$i++) {
				$this->prof++;
				$this->get_padres($rs[ $i ]);
				$this->prof--;
			}
			$this->arbol .= $inden . "\t</ul>\n";
			$this->arbol .= $inden . "</li>\n";
		}
	}
	
	function get_imagen($nodo)
	{
		$img = '';
		if (isset($this->items[$nodo]['imagen'])) {
			$url_img = toba_recurso::imagen_de_origen($this->items[$nodo]['imagen'],
											$this->items[$nodo]['imagen_recurso_origen']);
			$img = "<img src='$url_img' border=0 alt='' /> ";
		} else {
			$url_img = toba_recurso::imagen_toba('nulo.gif');
			$img = "<img src='$url_img' width=1 height=16 border=0 alt='' />";
		}		
		return $img;
	}
	
	function get_hijos($nodo)
	{
		$hijos = array();
		for ($i=0;$i<count($this->items);$i++) {
			if ($this->items[ $i ]['padre'] == $this->items[ $nodo ][ 'item' ])	{
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
			toba_js::cargar_consumos_globales(array('basicos/listmenu'));
			echo toba_js::ejecutar("var horizontals = new simpleMenu('menu-h', 'horizontal');");
		}
	}
}

?>