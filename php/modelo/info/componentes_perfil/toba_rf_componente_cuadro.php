<?php 
class toba_rf_componente_cuadro extends toba_rf_componente
{
	function inicializar()
	{
		$this->cargar_columnas();
	}	

	function cargar_columnas()
	{
		$cols = $this->cargar_datos_columnas();
		if(count($cols) > 0) {
			$grupo = new toba_rf_grupo_columnas('<b>COLUMNAS</b>',$this);
			$c = array();
			foreach($cols as $col) {
				$c[] = new toba_rf_subcomponente_columna($col['etiqueta'], $grupo, $col['id'], $col['proyecto'], $this->item, 
														$this->restriccion,
														$col['no_visible'], $col['cuadro']);		
			}
			$grupo->set_hijos($c);
			$this->agregar_hijo($grupo);
		}
	}

	function cargar_datos_columnas()
	{
		$item = quote($this->item);
		$restriccion = quote($this->restriccion);
		$componente = quote($this->componente);
		$proyecto = quote($this->proyecto);
		$sql = "SELECT 	col.objeto_cuadro_proyecto as 		proyecto,
						col.objeto_cuadro as				cuadro,
						col.objeto_cuadro_col as			id,
						col.titulo as						etiqueta, 
						rcol.no_visible as					no_visible
				FROM 	apex_objeto_ei_cuadro_columna col
						LEFT OUTER JOIN apex_restriccion_funcional_cols rcol
							ON col.objeto_cuadro_col = rcol.objeto_cuadro_col 
								AND col.objeto_cuadro = rcol.objeto_cuadro
								AND col.objeto_cuadro_proyecto = rcol.proyecto
								AND rcol.item = $item
								AND rcol.restriccion_funcional = $restriccion
				WHERE	col.objeto_cuadro = $componente
				AND		col.objeto_cuadro_proyecto = $proyecto
				ORDER BY col.orden";
		return toba::db()->consultar($sql);
	}
	
	function sincronizar()
	{
		if($this->no_visible_original != $this->no_visible_actual) {
			if ($this->no_visible_actual == 1) {
				$this->agregar_restriccion();
			}else{
				$this->eliminar_restriccion();
			}
		}
		foreach ($this->get_hijos() as $hijo){
			$hijo->sincronizar();
		}
	}
	
}
?>