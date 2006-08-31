<?php

class toba_menu_css extends toba_menu
{
	private $items;
	private $prof=1;
	private $arbol;
	protected $imagen_nodo ;
	protected $hay_algun_item = false;
	
	function __construct()
	{
		$this->imagen_nodo = toba_recurso::imagen_apl('menu_nodo_css.gif', false);
	}
	
	
	function plantilla_css()
	{
		return "listmenu_horizontal";
	}
	
	//-----------------------------------------------------------
	
	function preparar_arbol ()
	{

		$this->arbol .= '<style type="text/css">
							ul.horizontal .carpeta {
								background-repeat: no-repeat;
								background-position: center right;
								background-image: url("'.$this->imagen_nodo.'");
							}
						</style>';
		$this->items = $this->items_de_menu(false);
		$this->arbol .= "\n<ul id='menu-h'  class='horizontal'>\n";		
		for ($i=0;$i<count($this->items);$i++) {
			if ($this->items[ $i ]['padre'] == NULL) {
				$this->get_padres($i);
			}
		}
		$this->arbol .= "</ul>";	
	}
	
	function get_padres($nodo)
	{
		$inden = str_repeat("\t",$this->prof );
		
		if (!$this->items[$nodo]['carpeta']) {
			$vinculo = toba::get_vinculador()->crear_vinculo($this->items[$nodo]['proyecto'],
															 $this->items[$nodo]['item'], array(),
															 array('validar' => false, 'menu' => true));
			$proyecto = $this->items[$nodo]['proyecto'];
			$item = $this->items[$nodo]['item'];
			$this->arbol .= $inden . "<li><a tabindex='-1' href='$vinculo' " .
							"title='".$this->items[$nodo]['nombre']."'>" . 
							$this->items[$nodo]['nombre']."</a>";
			$this->arbol .= $inden . "</li>\n";
			$this->hay_algun_item = true;
		} else {
			//Es carpeta
			$class = ($this->prof > 1) ? " class='carpeta'" : "";
			$this->arbol .= $inden . "<li><a $class>" . $this->items[$nodo]['nombre'] . "</a>\n";
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