<?php
/**
 * Clase que importa un plan para componentes
 * @package Centrales
 * @subpackage Personalizacion
 * @author sp14ab
 */
class toba_importador_componentes extends toba_importador {

	protected function cargar_tareas()
	{
		foreach($this->plan as $item) {
			$tarea =  new toba_tarea_componente($item, $this->db);
			$this->tareas[] = $tarea;
		}
	}
}
?>
