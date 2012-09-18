<?php 
class toba_rf_subcomponente_evento extends toba_rf_subcomponente
{
	protected $id_evento;
	
	function __construct($nombre, $padre, $id, $proyecto, $item, $restriccion, $estado_original) 
	{
		$this->id_evento = $id;
		$id = 'evento_'.$id;
		parent::__construct($nombre, $padre, $id, $proyecto, $item, $restriccion, $estado_original);
	}	
	
	function inicializar()
	{
		$this->iconos[] = array(
				'imagen' => toba_recurso::imagen_toba( 'enter.png', false),
				'ayuda' => 'Botón',
				);		
	}

	function sincronizar()
	{
		if ($this->no_visible_actual != $this->no_visible_original) {
			$proyecto = quote($this->proyecto);
			$restriccion = quote($this->restriccion);
			$item = quote($this->item);
			$evento = quote($this->id_evento);
			$invisible = ($this->no_visible_actual)? '1': '0';			
			if ($this->no_visible_actual) {
				$sql = "INSERT INTO 
							apex_restriccion_funcional_evt (proyecto, restriccion_funcional, item, evento_id, no_visible)
						VALUES
							($proyecto, $restriccion, $item, $evento, $invisible);";
			} else {
				$sql = "DELETE FROM
							apex_restriccion_funcional_evt
						WHERE
								proyecto = $proyecto
							AND	restriccion_funcional = $restriccion
							AND evento_id = $evento;";
			}
			toba::db()->ejecutar($sql);
		}
	}
	
}
?>