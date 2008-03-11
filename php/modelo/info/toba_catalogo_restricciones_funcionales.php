<?php

class toba_catalogo_restricciones_funcionales
{
	protected $proyecto;
	protected $restriccion;
	
	function __construct($proyecto, $id_restriccion_funcional)
	{
		$this->proyecto = $proyecto;
		$this->restriccion = $id_restriccion_funcional;
	}
	
	function cargar()
	{
		$nodos = array();
		$items = $this->get_lista_items();
		foreach($items as $item) {
			$nodos[] = new toba_rf_item($this->restriccion, $item['proyecto'], $item['item']);
		}
		return $nodos;
	}
	
	
	function get_lista_items()
	{
		$items = array();
		$sql = "SELECT item FROM apex_perfil_funcional_ef WHERE restriccion_funcional = '$this->restriccion'
				UNION
				SELECT item apex_perfil_funcional_pantalla WHERE restriccion_funcional = '$this->restriccion'
				UNION
				SELECT item apex_perfil_funcional_evt WHERE restriccion_funcional = '$this->restriccion'
				UNION
				SELECT item apex_perfil_funcional_ei WHERE restriccion_funcional = '$this->restriccion'
				UNION
				SELECT item apex_perfil_funcional_cols WHERE restriccion_funcional = '$this->restriccion'";
		
		$items[] = array('proyecto' => 'toba_referencia', 'item' => 2656 );
		$items[] = array('proyecto' => 'toba_referencia', 'item' => 2654 );
		return $items;
	}
}

?>