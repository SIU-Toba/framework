<?php 
class toba_rf_subcomponente extends toba_rf
{
	
	function __construct($nombre, $padre, $id, $proyecto, $item, $restriccion, $estado_original) 
	{
		parent::__construct($nombre, $padre);
		$this->proyecto = $proyecto;
		$this->nombre_largo = $this->nombre_corto;
		$this->id = $id;
		$this->restriccion = $restriccion;
		$this->item = $item;
		$this->no_visible_original = ($estado_original != '') ? 1 : 0;
		$this->no_visible_actual = $this->no_visible_original;
		if ($this->no_visible_original) {
			$this->marcar_abiertos();
		}
	}
	
}
?>