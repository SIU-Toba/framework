<?php
require_once('contrib/lib/toba_nodo_basico.php');
require_once('contrib/catalogo_items_menu/toba_item_menu.php');
require_once('contrib/catalogo_items_menu/toba_carpeta_menu.php');

class toba_catalogo_items_menu extends toba_catalogo_items_base 
{
	protected $usa_niveles = false;
	
	function cargar($raiz)
	{
		$rs = toba::proyecto()->get_items_menu();
		//ei_arbol($rs);
		$this->items = array();
		if (!empty($rs)) {
			foreach ($rs as $fila) {
				if ($fila['carpeta']) {
					$obj = new toba_carpeta_menu( $fila['nombre'], null, $fila['item'], $fila['padre']);

				}else{
					$obj = new toba_item_menu( $fila['nombre'], null, $fila['item'], $fila['padre']);	
				}				
				$obj->set_imagen($fila['imagen_recurso_origen'], $fila['imagen']);				
				$this->items[$fila['item']] = $obj;
			}
			$this->carpeta_inicial = $raiz;
			$this->mensaje = "";
			$this->ordenar();
		}
	}

	/**
	 * Retorna un arreglo con los arboles que componen los hijos de un nodo raiz dado
	 */
	function get_hijos($raiz)
	{
		$hijos = array();
		foreach ($this->items as $item) {
			if ($item->get_id_padre() == $raiz)
				$hijos[] = $item;
		}
		return $hijos;
	}
	
	
}

?>