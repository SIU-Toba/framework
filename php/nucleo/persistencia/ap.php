<?php
/*
	Administrador de persistencia generico
*/

abstract class ap
{
	protected $objeto_tabla;					// Referencia al objeto
	protected $columnas;
	protected $cambios;
	protected $datos;

	function set_objeto_tabla($ot)
	{
		$this->objeto_tabla = $ot;
		$this->columnas = $this->datos_tabla->get_columnas();
		$this->cambios = $this->datos_tabla->get_cambios();
		$this->datos = $this->datos_tabla->get_datos();
	}

	abstract function cargar_datos()
	{
		
	}
	
	abstract function sincronizar()
	{
		
	}	
}
?>