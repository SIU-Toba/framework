<?php

class toba_catalogo_restricciones_funcionales extends toba_catalogo_items_base 
{
	protected $proyecto;
	protected $restriccion;
	protected $expande_dependencias_sueltas = false;
	
	function __construct($proyecto, $id_restriccion_funcional='')
	{
		$this->proyecto = $proyecto;
		$this->restriccion = $id_restriccion_funcional;
	}
	
	/*
		Si no existe una RF creada, muestra carpetas
		Si existe, busca la lista de items y recupera la rama de carpetas hacia la raiz
	
	*/
	function cargar($opciones, $id_item_inicial=null, $incluidos_forzados=array())
	{
		$nodos = array();
		$items = $this->get_lista_items();
		foreach($items as $item) {
			if ($item['carpeta']) {
				$obj = new toba_rf_carpeta($this->restriccion, $item['proyecto'], $item['item'], $item['padre']);
			}else{
				$obj = new toba_rf_item($this->restriccion, $item['proyecto'], $item['item'], $item['padre'], $this->expande_dependencias_sueltas);
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
		$proyecto = toba_contexto_info::get_db()->quote($this->proyecto);
		$sql = "SELECT
					proyecto, 
					item, 
					carpeta,
					padre 
				FROM 
					apex_item
				WHERE 
					proyecto = $proyecto
				ORDER BY carpeta, orden, nombre;";
		$items = toba_contexto_info::get_db()->consultar($sql);
		return $items;
	}
	
	function set_expandir_dependencias_sin_pantalla($expandir = false)
	{
		$this->expande_dependencias_sueltas = $expandir;
	}
}

?>