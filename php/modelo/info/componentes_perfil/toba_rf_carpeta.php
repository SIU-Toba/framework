<?php

class toba_rf_carpeta extends toba_rf 
{
	protected $icono = "nucleo/carpeta.gif";
	protected $carpeta = true;
	
	function __construct($restriccion, $proyecto, $item, $id_padre) 
	{
		$this->restriccion = $restriccion;
		$this->proyecto = $proyecto;
		$this->item = $item;
		$datos = $this->cargar_datos();
		$this->imagen = $datos['imagen'];
		$this->imagen_origen = $datos['imagen_recurso_origen'];
		$this->id_padre = $id_padre;
		parent::__construct($datos['nombre'], null, $this->item);
		if (!isset($datos['descripcion']) || empty($datos['descripcion'])) {
			$this->nombre_largo = $this->nombre_corto;
		}else{
			$this->nombre_largo = $datos['descripcion'];
		}
		$this->get_imagen();
		if ($this->es_raiz()) {
			$this->abierto = true;
		}
	}
	
	function get_id()
	{
		return 'item_'.parent::get_id();
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
		$item = quote($this->item);
		$proyecto = quote($this->proyecto);
		$sql = "SELECT 		nombre,
							descripcion,
							padre,
							imagen_recurso_origen,
							imagen							
				FROM apex_item 
				WHERE item = $item
				AND proyecto = $proyecto";
		return toba::db()->consultar_fila($sql);
	}	
}
?>