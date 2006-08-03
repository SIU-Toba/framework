<?php

class cuadro_fotos extends objeto_ei_cuadro
{
	protected $fotos_predefinidas = array();
	
	function set_fotos_predefinidas($fotos)
	{
		$this->fotos_predefinidas = $fotos;
	}
	
	function filtrar_evt__defecto($f)
	{
		 $clave = $this->obtener_clave_fila($f);
		 if (! in_array($clave, $this->fotos_predefinidas)) {
		 	return true;
		 } else {
		 	return false;
		 }
	}
	
	function filtrar_evt__baja($f)
	{
		 return $this->filtrar_evt__defecto($f);
	}	
	
}

?>