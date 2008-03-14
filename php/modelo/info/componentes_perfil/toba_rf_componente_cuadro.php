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
			$grupo = new toba_rf_grupo_columnas('COLUMNAS',$this);
			$c = array();
			foreach($cols as $col) {
				$c[] = new toba_rf_subcomponente_columna($col['etiqueta'], $grupo, $col['id'] , $this->item, 
														$this->restriccion,
														$col['estado_original_visible']);		
			}
			$grupo->set_hijos($c);
			$this->agregar_hijo($grupo);
		}
	}

	function cargar_datos_columnas()
	{
		$sql = "SELECT 	col.objeto_cuadro_col as			id,
						col.titulo as						etiqueta, 
						rcol.no_visible as					estado_original_visible
				FROM 	apex_objeto_ei_cuadro_columna col
						LEFT OUTER JOIN apex_restriccion_funcional_cols rcol
							ON col.objeto_cuadro_col = rcol.objeto_cuadro_col 
								AND col.objeto_cuadro = rcol.objeto_cuadro
								AND col.objeto_cuadro_proyecto = rcol.proyecto
								AND rcol.item = '$this->item'
								AND rcol.restriccion_funcional = '$this->restriccion'
				WHERE	col.objeto_cuadro = '$this->componente' 
				AND		col.objeto_cuadro_proyecto = '$this->proyecto'
				ORDER BY col.orden";
		return toba::db()->consultar($sql);
	}
}
?>