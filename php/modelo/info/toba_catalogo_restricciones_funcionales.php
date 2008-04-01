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
		$this->carpeta_inicial = '__raiz__';
		$this->mensaje = "";
		$this->ordenar();
		//filtrar???
	}
	
	
	function get_lista_items()
	{
		$items = array();
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
		
		$items[] = array('proyecto' => 'toba_referencia', 'item' => 2656 );
		$items[] = array('proyecto' => 'toba_referencia', 'item' => 2654 );
		$items[] = array('proyecto' => 'toba_referencia', 'item' => '/objetos/ei_formulario' );
		$items[] = array('proyecto' => 'toba_referencia', 'item' => '/objetos/ei_formulario_ml' );
		//arbolito harcodeadito
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