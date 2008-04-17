<?php

class toba_rf_item extends toba_rf
{
	protected $icono = "item.gif";
	
	function __construct($restriccion, $proyecto, $item, $id_padre) 
	{
		$this->restriccion = $restriccion;
		$this->proyecto = $proyecto;
		$this->item = $item;
		$datos = $this->cargar_datos();
		$this->imagen = $datos['imagen'];
		$this->imagen_origen = $datos['imagen_recurso_origen'];
		$this->cargar_hijos();
		$this->id_padre = $id_padre;
		parent::__construct($datos['nombre'], null, $this->item);
		if (!isset($datos['descripcion']) || empty($datos['descripcion'])) {
			$this->nombre_largo = $this->nombre_corto;
		}else{
			$this->nombre_largo = $datos['descripcion'];
		}
		$this->get_imagen();
	}
	
	function sincronizar()
	{
		if ($this->tiene_hijos_cargados()) {
			foreach ($this->get_hijos() as $hijo) {
				$hijo->sincronizar();
			}
		}
	}
	
	function cargar_datos()
	{
		$sql = "SELECT 		nombre,
							descripcion,
							padre,
							imagen_recurso_origen,
							imagen							
				FROM apex_item 
				WHERE item = '$this->item' 
				AND proyecto = '$this->proyecto'";
		return toba::db()->consultar_fila($sql);
	}

	function cargar_hijos()
	{
		$hijos = array();
		foreach( $this->buscar_hijos() as $hijo) {
			$hijos[] = new toba_rf_ci($this->restriccion, $this->proyecto, $this->item, $hijo['componente'], $this, true);
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