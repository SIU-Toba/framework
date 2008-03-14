<?php

class toba_rf_item extends toba_rf
{
	protected $restriccion;
	protected $proyecto;
	protected $item;
	
	function __construct($restriccion, $proyecto, $item) 
	{
		$this->restriccion = $restriccion;
		$this->proyecto = $proyecto;
		$this->item = $item;
		$datos = $this->cargar_datos();
		$this->cargar_hijos();		
		parent::__construct($datos['nombre']);
	}

	function cargar_datos()
	{
		$sql = "SELECT nombre, descripcion, padre
				FROM apex_item 
				WHERE item = '$this->item' 
				AND proyecto = '$this->proyecto'";
		return toba::db()->consultar_fila($sql);
	}

	function cargar_hijos()
	{
		$hijos = array();
		foreach( $this->buscar_hijos() as $hijo) {
			$hijos[] = new toba_rf_ci($this->restriccion, $this->proyecto, $this->item, $hijo['componente'], $this);
		}
		if ($hijos) $this->set_hijos($hijos);
	}

	function buscar_hijos()
	{
		$sql = "SELECT 
					o.objeto as componente
				FROM
					apex_item_objeto io,
					apex_objeto o
				WHERE
					io.item = '$this->item' AND
					io.proyecto = '$this->proyecto' AND
					io.objeto = o.objeto AND
					io.proyecto = o.proyecto AND
					o.clase = 'toba_ci'";
		return toba::db()->consultar($sql);
	}
}
?>