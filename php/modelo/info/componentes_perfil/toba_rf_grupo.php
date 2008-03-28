<?php 
class toba_rf_grupo extends toba_rf
{
	protected $id;
	
	function __construct($nombre, $padre)
	{
		parent::__construct($nombre, $padre);	
		$this->id = uniqid();
		$this->inicializar();
	}
	
	function sincronizar()
	{
		//ei_arbol($this->get_hijos());
		foreach ($this->get_hijos() as $hijo) {
			$hijo->sincronizar();
		}
	}
	
}
?>