<?php
include_once('nucleo/browser/interface/ef.php');

class casos_dao
{
	function get_categorias()
	{
		return array(apex_ef_no_seteado => 'Todas', 
					 'items' => 'Items', 
					 'ef' => "Ef's", 
					 'varios' => 'Varios');
	}
	
	function get_casos($categoria = apex_ef_no_seteado)
	{
		$casos = array(
					//Varios
					'test_parseo_etiquetas' => array('nombre' => 'Parseo de etiquetas', 'categoria' => 'varios'), 
					
					//Items
					'test_item' => array('nombre' => 'Comportamiento básico del ítem', 'categoria' => 'items'),
					'test_arbol_items' => array('nombre' => 'Manejo del árbol de ítems', 'categoria' => 'items'),
					
					//EF
					'test_multi_seleccion' => array('nombre' => 'EF Multi-selección', 'categoria' => 'ef')
				);	
		
		if ($categoria == apex_ef_no_seteado)
			return $casos;
		else {
			$casos_selecc = array();
			foreach ($casos as $clase => $caso) {
				if ($caso['categoria'] == $categoria)
					$casos_selecc[$clase] = $caso;
			}
			return $casos_selecc;
		}
	}
	
	function get_casos_para_form($categoria = apex_ef_no_seteado)
	{
		return aplanar_matriz(casos_dao::get_casos($categoria), 'nombre');	
	}
}


?>