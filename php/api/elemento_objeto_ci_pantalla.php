<?php

class elemento_objeto_ci_pantalla implements recorrible_como_arbol
{
	protected $dependencias = array();
	protected $datos;
	
	function __construct($datos, $dependencias_posibles)
	{
		//etiqueta, posicion
		$this->datos = $datos;
		$this->asociar_dependencias($dependencias_posibles);
	}
	
	protected function asociar_dependencias($posibles)
	{
		$eis = explode(',', $this->datos['objetos']);
		foreach ($posibles as $posible) {
			if (in_array($posible->rol_en_consumidor(), $eis)) {
				$this->dependencias[] = $posible;
			}
		}
	}
	
	public function tiene_dependencia($dep)
	{
		return in_array($dep, $this->dependencias);
	}

	//---- Recorrido como arbol
	function hijos()
	{
		return $this->dependencias;
	}
	
	function es_hoja()
	{
		return (count($this->dependencias) == 0);
	}
	
	function tiene_propiedades()
	{
		return false;
	}	
	
	function nombre_corto()
	{
		return str_replace('&', '', $this->datos['etiqueta']);
	}
	
	function nombre_largo()
	{
		if (trim($this->datos['descripcion']) != '')
			return $this->datos['descripcion'];
		else
			return $this->nombre_corto();
	}
	
	function id()
	{
		return "Pantalla ".$this->datos['posicion'];
	}
	
	function iconos()
	{
		$iconos = array();
		return $iconos;
	}
	
	function utilerias()
	{
		$iconos = array();
		return $iconos;	
	}	
	

}


?>