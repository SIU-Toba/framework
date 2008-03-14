<?php 
class toba_rf_subcomponente extends toba_rf
{
	protected $estado_original = null;
	protected $estado_actual = null;
	
	function __construct($nombre, $padre, $id, $item, $restriccion, $estado_original) 
	{
		parent::__construct($nombre, $padre);
		$this->id = $id;
		$this->restriccion = $restriccion;
		$this->item = $item;
		$this->estado_original = $estado_original;
		$this->estado_actual = $this->estado_original;
	}

	
}
?>