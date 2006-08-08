<?php
require_once('nucleo/componentes/interface/objeto_ci.php');

class extension_ci extends objeto_ci
{
	protected $datos;
	protected $describir = false;
	
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion(); 
		$propiedades[] = "datos";
		return $propiedades;		
	}
	
	function conf__ml()
	{
		if (isset($this->datos))
			return $this->datos;			
	}
	
	function evt__ml__modificacion($datos)
	{
		$this->datos = $datos;
	}
	
	function evt__ml__seleccion($id_fila)
	{
		$this->informar_msg('Se selecciona la fila con importe : '.$this->datos[$id_fila]['importe'], 'info');
	}
	
	function evt__ml__describir($id_fila)
	{
		$this->describir = $id_fila;
		$this->dependencia('ml')->deseleccionar();		
	}
	
	function obtener_html()
	{
		if ($this->describir !== false) {
			ei_arbol($this->datos[$this->describir], 'Descripción de la fila');			
		}
		parent::obtener_html();
	}

}


?>
