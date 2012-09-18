<?php 
class toba_rf_grupo extends toba_rf
{
	protected $id;
	
	function __construct($nombre, $padre)
	{
		parent::__construct($nombre, $padre);	
		$this->id = uniqid();
	}
	
	function sincronizar()
	{
		foreach ($this->get_hijos() as $hijo) {
			$hijo->sincronizar();
		}
	}	
}
?>