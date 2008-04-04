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
		toba::logger()->debug($sql);
		return toba::db()->consultar_fila($sql);
	}
	
}


?>