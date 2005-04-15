<?php
require_once("nucleo/negocio/objeto_cn_t.php"); 

class cn extends objeto_cn_t 
{ 
	protected $datos_ml;

    function mantener_estado_sesion() 
    //Propiedades que necesitan persistirse en la sesion 
    { 
        $propiedades = parent::mantener_estado_sesion(); 
        $propiedades[] = "datos_ml"; 
        return $propiedades; 
    } 

	function get_estado_proceso()
	{
		return false;
	}
	
	function get_datos_ml() 
	{
		return $this->datos_ml;
	}
	
	function set_datos_ml($datos)
	{
		$this->datos_ml = $datos;
	}	
	
	function debug()
	{
		ei_arbol($this->datos_ml);
	}

	
}

?>