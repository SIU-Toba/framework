<?php 
class toba_rf_subcomponente_filtro_col extends toba_rf_subcomponente
{
	protected $filtro;
	protected $id_columna;
	
	function __construct($nombre, $padre, $id, $proyecto, $item, $restriccion, $estado_original, $filtro) 
	{
		$this->filtro = $filtro;
		$this->id_columna = $id;
		$id = 'filtro_col_'.$id;
		parent::__construct($nombre, $padre, $id, $proyecto, $item, $restriccion, $estado_original);
		
	}
	
	function inicializar()
	{
		$this->iconos[] = array(
				'imagen' => toba_recurso::imagen_toba( 'objetos/columna.gif', false),
				'ayuda' => 'Columna de un filtro',
				);		
	}
	
	function sincronizar()
	{
		if ($this->no_visible_actual != $this->no_visible_original) {
			$proyecto = quote($this->proyecto);
			$restriccion = quote($this->restriccion);
			$item = quote($this->item);
			$filtro = quote($this->filtro);
			$columna = quote($this->id_columna);
			$invisible = ($this->no_visible_actual) ? '1': '0';			
			
			if ($this->no_visible_actual) {
				$sql = "INSERT INTO 
							apex_restriccion_funcional_filtro_cols (proyecto, restriccion_funcional, item, objeto_ei_filtro, objeto_ei_filtro_col, no_visible)
						VALUES
							($proyecto, $restriccion, $item, $filtro, $columna, $invisible);";
			} else {
				$sql = "DELETE FROM
							apex_restriccion_funcional_filtro_cols
						WHERE
								proyecto = $proyecto
							AND	restriccion_funcional = $restriccion
							AND objeto_ei_filtro_col = $columna;";
			}
			toba::db()->ejecutar($sql);
		}		
	}
}
?>