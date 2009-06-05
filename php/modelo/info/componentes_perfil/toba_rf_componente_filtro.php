<?php 
class toba_rf_componente_filtro extends toba_rf_componente
{
	function inicializar()
	{
		$this->cargar_cols();
	}	

	function cargar_cols()
	{
		$cols = $this->cargar_datos_cols();
		if(count($cols) > 0) {
			$grupo = new toba_rf_grupo_columnas('<b>COLUMNAS</b>',$this);
			$e = array();
			foreach($cols as $col) {
				$e[] = new toba_rf_subcomponente_filtro_col($col['etiqueta'], $grupo, $col['id'] , $col['proyecto'], $this->item, 
														$this->restriccion,
														$col['no_visible'],
														$col['filtro']);		
			}
			$grupo->set_hijos($e);
			$this->agregar_hijo($grupo);
		}
	}

	function cargar_datos_cols()
	{
		$item = quote($this->item);
		$restriccion = quote($this->restriccion);
		$componente = quote($this->componente);
		$proyecto = quote($this->proyecto);
		$sql = "SELECT 	col.objeto_ei_filtro			as	filtro,
						col.objeto_ei_filtro_col 		as	id,
						col.objeto_ei_filtro_proyecto 	as	proyecto,
						col.etiqueta as						etiqueta, 
						rcol.no_visible as					no_visible
				FROM 	apex_objeto_ei_filtro_col col
						LEFT OUTER JOIN apex_restriccion_funcional_filtro_cols rcol
							ON col.objeto_ei_filtro_col = rcol.objeto_ei_filtro_col 
								AND col.objeto_ei_filtro = rcol.objeto_ei_filtro
								AND col.objeto_ei_filtro_proyecto = rcol.proyecto
								AND rcol.item = $item
								AND rcol.restriccion_funcional = $restriccion
				WHERE	(col.obligatorio <> 1 OR col.obligatorio IS NULL) 
				AND		col.objeto_ei_filtro = $componente
				AND		col.objeto_ei_filtro_proyecto = $proyecto
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