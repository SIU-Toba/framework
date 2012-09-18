<?php 
class toba_rf_subcomponente_columna extends toba_rf_subcomponente
{
	protected $cuadro;
	protected $id_columna;
	
	function __construct($nombre, $padre, $id, $proyecto, $item, $restriccion, $estado_original, $cuadro) 
	{
		$this->cuadro = $cuadro;
		$this->id_columna = $id;
		$id = 'col_cuadro_'.$id;
		parent::__construct($nombre, $padre, $id, $proyecto, $item, $restriccion, $estado_original);
		
	}
	
	function inicializar()
	{
		$this->iconos[] = array(
				'imagen' => toba_recurso::imagen_toba( 'objetos/columna.gif', false),
				'ayuda' => 'Columna de un cuadro',
				);		
	}
	
	function sincronizar()
	{
		if ($this->no_visible_actual != $this->no_visible_original) {				//si hubo cambio de valores
			$proyecto = quote($this->proyecto);
			$restriccion = quote($this->restriccion);
			$id_col = quote($this->id_columna);				
			
			if ($this->no_visible_actual) {
				$item = quote($this->item);
				$cuadro = quote($this->cuadro);				
				$estado = ($this->no_visible_actual) ? '1' : '0';
				
				$sql = "INSERT INTO 
							apex_restriccion_funcional_cols (proyecto, restriccion_funcional, item, objeto_cuadro, objeto_cuadro_col, no_visible)
						VALUES
							($proyecto, $restriccion, $item, $cuadro, $id_col, '$estado');";
			} else {
				$sql = "DELETE FROM
							apex_restriccion_funcional_cols
						WHERE
								proyecto = $proyecto
							AND	restriccion_funcional = $restriccion
							AND objeto_cuadro_col = $id_col;";
			}
			toba::db()->ejecutar($sql);
		}		
	}
}
?>