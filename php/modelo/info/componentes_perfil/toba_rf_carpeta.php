<?php

class toba_rf_carpeta extends toba_rf 
{
	protected $icono = "nucleo/carpeta.gif";
	protected $carpeta = true;
	
	function __construct($restriccion, $proyecto, $item, $padre) 
	{
		$this->restriccion = $restriccion;
		$this->proyecto = $proyecto;
		$this->item = $item;
		$datos = $this->cargar_datos();
		$this->iconos[] = array(
				'imagen' => toba_recurso::imagen_toba($this->icono, false),
				'ayuda' => "Carpeta que contiene operaciones.",
				);
		parent::__construct($datos['nombre'], $padre, $this->item);
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