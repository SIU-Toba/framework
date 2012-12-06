<?php

class toba_rf_item extends toba_rf
{
	protected $icono = "item.gif";
	protected $expandir_deps_sin_pantalla;
	
	function __construct($restriccion, $proyecto, $item, $id_padre, $expandir_dep_sueltas=false) 
	{
		$this->id_padre = $id_padre;		
		$this->restriccion = $restriccion;
		$this->proyecto = $proyecto;
		$this->item = $item;
		$datos = $this->cargar_datos();
		$this->imagen = $datos['imagen'];
		$this->imagen_origen = $datos['imagen_recurso_origen'];
		$this->expandir_deps_sin_pantalla = $expandir_dep_sueltas;
		if ($datos['cant_dependencias'] > 0) {
			$this->es_hoja = false;
			//-- Solo debe cargarse inicialmente si existe alguna dependencia que tiene una restricción
			if ($this->tiene_dependencia_con_restriccion($datos)) {
				$this->cargar_hijos();
			}
		}
		parent::__construct($datos['nombre'], null, $this->item);
		if (! isset($datos['descripcion']) || empty($datos['descripcion'])) {
			$this->nombre_largo = $this->nombre_corto;
		} else {
			$this->nombre_largo = $datos['descripcion'];
		}
		$this->get_imagen();
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
							imagen,
							(SELECT COUNT(*) FROM apex_item_objeto					WHERE item = i.item AND proyecto = i.proyecto) as cant_dependencias,							
							(SELECT COUNT(*) FROM apex_restriccion_funcional_ef		WHERE item = i.item AND proyecto = i.proyecto) as cant_rest_ef,
							(SELECT COUNT(*) FROM apex_restriccion_funcional_pantalla	WHERE item = i.item AND proyecto = i.proyecto) as cant_rest_pant,
							(SELECT COUNT(*) FROM apex_restriccion_funcional_evt		WHERE item = i.item AND proyecto = i.proyecto) as cant_rest_evt,
							(SELECT COUNT(*) FROM apex_restriccion_funcional_ei		WHERE item = i.item AND proyecto = i.proyecto) as cant_rest_ei,
							(SELECT COUNT(*) FROM apex_restriccion_funcional_cols	 	WHERE item = i.item AND proyecto = i.proyecto) as cant_rest_cols,
							(SELECT COUNT(*) FROM apex_restriccion_funcional_filtro_cols  WHERE item = i.item AND proyecto = i.proyecto) as cant_rest_filtro_cols
				FROM 
					apex_item i
				WHERE item = $item
				AND proyecto = $proyecto";
		return toba::db()->consultar_fila($sql);
	}

	protected function tiene_dependencia_con_restriccion($datos)
	{
		return ($datos['cant_rest_ef'] > 0 ||
				$datos['cant_rest_pant'] > 0 ||
				$datos['cant_rest_evt'] > 0 ||
				$datos['cant_rest_ei'] > 0 ||
				$datos['cant_rest_cols'] > 0 ||
				$datos['cant_rest_filtro_cols'] > 0);
	}
	
	function cargar_hijos()
	{
		$hijos = array();
		$opciones = $this->buscar_hijos();
		foreach ($opciones as $hijo) {
			$hijos[] = new toba_rf_ci($this->restriccion, $this->proyecto, $this->item, $hijo['componente'], $this, true, $this->expandir_deps_sin_pantalla);
		}
		if (! empty($hijos)) {
			$this->set_hijos($hijos);
		}
	}
	
	function buscar_hijos()
	{
		$item = quote($this->item);
		$proyecto = quote($this->proyecto);
		$sql = "SELECT
					o.objeto as componente
				FROM
					apex_item_objeto io,
					apex_objeto o
				WHERE
					io.item = $item AND
					io.proyecto = $proyecto AND
					io.objeto = o.objeto AND
					io.proyecto = o.proyecto AND
					o.clase = 'toba_ci'";
		return toba::db()->consultar($sql);
	}
}
?>