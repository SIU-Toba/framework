<?php

class cuadro_fotos extends toba_ei_cuadro
{
	protected $fotos_predefinidas = array();
	
	function set_fotos_predefinidas($fotos)
	{
		$this->fotos_predefinidas = $fotos;
	}
	
	function conf_evt__defecto($evento, $f)
	{
		 $clave = $this->get_clave_fila($f);
		 if (in_array($clave, $this->fotos_predefinidas)) {
			$evento->anular();
		 }
	}
	
	function conf_evt__baja($evento, $f)
	{
		 return $this->conf_evt__defecto($evento, $f);
	}	
}
?>