<?php

class ci_relacion_ml_dt extends objeto_ci
{
	function evt__ml__modificacion($datos)
	{
		ei_arbol($datos);
		$this->dep('dt')->procesar_filas($datos);
	}
	
	function conf__ml($ml)
	{
		return $this->dep('dt')->get_filas();	
	}
	
	function evt__cancelar()
	{
		$this->dep('dt')->resetear();	
	}
	
}

?>