<?php

abstract class menu
{
	abstract function mostrar();
}

/**
*	Recorre el arbol de item dejando lugar para que las extensiones puedan sacar el HTML para armar el menu
*/
abstract class menu_recorrido extends menu 
{
	function mostrar()
	{
		$this->pre_arbol();
		$this->armar_arbol();
		$this->post_arbol();
	}

	/**
	*	Cosas necesarias previas a inicial el recorrido
	*/
	abstract protected function pre_arbol();
	
	/**
	*	Cosas necesarias posteriores al recorrido
	*/	
	abstract protected function post_arbol();
	
	/**
	*	Recorrido del arbol en forma nodo y sus hijos comenzando desde la raiz
	*/
	protected function armar_arbol()
	{
		$rs = $this->items_de_menu();
		reset($rs);
		$actual = current($rs);
		while ($actual !== false) {
			$padre = trim($actual["padre"]);
			$padre_hermanos = $actual;
			$this->inicio_nodos_hermanos($padre_hermanos);
			//Busca los nodos hijos de esta rama
			while($actual !== false && $padre == trim($actual["padre"])) {
				if($actual["carpeta"] == 1){
					// Agrego CARPETAS al menu
					$this->carpeta($actual);
				} else {
					// Agrego ITEMS al menu
					$this->item($actual);
				}
				$actual = next($rs);
			}
			$this->fin_nodos_hermanos($padre_hermanos);
		}
	}
	
	protected function items_de_menu($solo_primer_nivel=false)
	{
		$rest = "";
		if ($solo_primer_nivel) {
			$rest = " AND i.padre = '' ";
		}
		$sql = "SELECT 	i.padre as 		padre,
						i.carpeta as 	carpeta, 
						i.proyecto as	proyecto,
						i.item as 		item,
						i.nombre as 	nombre
				FROM 	apex_item i, apex_usuario_grupo_acc_item u
				WHERE 	(i.item = u.item)
				AND		(i.proyecto = u.proyecto)
				AND 	(i.menu = 1)
				AND 	(i.item <> '')
				$rest
				AND		(u.usuario_grupo_acc = '".toba::get_hilo()->obtener_usuario_grupo_acceso()."' )
				AND		(i.proyecto = '".toba::get_hilo()->obtener_proyecto()."')
				ORDER BY i.padre,i.orden;";
		return toba::get_db('instancia')->consultar($sql);
	}
	
	/**
	*	Comienza el recorrido de un conjunto de hermanos
	*/
	abstract protected function inicio_nodos_hermanos($item);
	
	/**
	*	Fin del recorrido de un conjunto de hermanos
	*/	
	abstract protected function fin_nodos_hermanos($item);
	
	/**
	*	Se pasa por un nodo que es una carpeta.
	*	Solo se debe sacar la salida del item carpeta, no sus hijos que se recorren como hermanos
	*/
	abstract protected function carpeta($item);
	
	/**
	*	Se pasa por un nodo que es un item
	*/
	abstract protected function item($item);		

}

?>