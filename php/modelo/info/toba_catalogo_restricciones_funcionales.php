<?php

class toba_catalogo_restricciones_funcionales extends toba_catalogo_items_base 
{
	protected $proyecto;
	protected $restriccion;
	
	function __construct($proyecto, $id_restriccion_funcional='')
	{
		$this->proyecto = $proyecto;
		$this->restriccion = $id_restriccion_funcional;
	}
	
	/*
		Si no existe una RF creada, muestra carpetas
		Si existe, busca la lista de items y recupera la rama de carpetas hacia la raiz
	
	*/
	function cargar()
	{
		$nodos = array();
		$items = $this->get_lista_items();
		foreach($items as $item) {
			if ($item['carpeta']) {
				$obj = new toba_rf_carpeta($this->restriccion, $item['proyecto'], $item['item'], $item['padre']);
			}else{
				$obj = new toba_rf_item($this->restriccion, $item['proyecto'], $item['item'], $item['padre']);
			}
			$this->items[$item['item']] = $obj;
		}
		$this->carpeta_inicial = 'item_'.toba_info_editores::get_item_raiz($this->proyecto);
		$this->mensaje = "";
		$this->ordenar();
		//filtrar???
	}
	
	function ordenar()
	{
		//--- Se conocen entre padres e hijos
		foreach (array_keys($this->items) as $id) {
			$item = $this->items[$id];
			if (isset($this->items[$item->get_id_padre()])) {
				$padre = $this->items[$item->get_id_padre()];
	 			if ($padre !== $item) {			
					$item->set_padre($padre);
					$padre->agregar_hijo($item);
					//Truchada para que las carpetas se abran
					if ($item->get_apertura()) {
						$padre->set_apertura(true);
					}
				}
			}			
		}
		
		//---Se recorre el arbol para poner los niveles
		$raiz = $this->buscar_carpeta_inicial();
		$this->items_ordenados = array();
		$this->camino = array();
		$this->ordenar_recursivo($raiz, 0);
		$this->items = $this->items_ordenados;
		unset($this->item_ordenados);
	}	
	
	function get_lista_items()
	{
		/*$items = array();
		$sql = "SELECT proyecto, item FROM apex_restriccion_funcional_ef 
				WHERE restriccion_funcional = '$this->restriccion' and proyecto = '$this->proyecto'
				UNION
				SELECT proyecto, item FROM apex_restriccion_funcional_pantalla 
				WHERE restriccion_funcional = '$this->restriccion' and proyecto = '$this->proyecto'
				UNION
				SELECT proyecto, item FROM apex_restriccion_funcional_evt 
				WHERE restriccion_funcional = '$this->restriccion' and proyecto = '$this->proyecto'
				UNION
				SELECT proyecto, item FROM apex_restriccion_funcional_ei 
				WHERE restriccion_funcional = '$this->restriccion' and proyecto = '$this->proyecto'
				UNION
				SELECT proyecto, item FROM apex_restriccion_funcional_cols 
				WHERE restriccion_funcional = '$this->restriccion' and proyecto = '$this->proyecto'";
		*/
		$sql = "SELECT 
					proyecto, 
					item, 
					carpeta,
					padre 
				FROM 
					apex_item
				WHERE 
					proyecto = '$this->proyecto'
				ORDER BY carpeta, orden, nombre;";
		$items = toba::db()->consultar($sql);
		return $items;
	}
}

?>