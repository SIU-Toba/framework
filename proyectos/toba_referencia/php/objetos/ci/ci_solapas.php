<?php
require_once('nucleo/componentes/interface/objeto_ci.php');

class ci_solapas extends objeto_ci
{
	var $pasadas_por_solapa;

    function __construct($id) 
    { 
		$this->pasadas_por_solapa = array('1'=>0, '2'=>0, '3'=>0);
        parent::__construct($id); 
    } 

    function mantener_estado_sesion() 
    { 
        $propiedades = parent::mantener_estado_sesion();
        $propiedades[] = "pasadas_por_solapa";
        return $propiedades; 
    } 	

	/**
	*	El cuadro se carga con la cantidad de pasadas producidas hasta el momento
	*/
	function evt__cuadro_pasadas__carga()
	{
		return array($this->pasadas_por_solapa);
	}
	
	/**
	*	Se registran la cantidad de pasadas cuando se entra a una solapa
	*/
    function evt__entrada__1()
    {
		$this->pasadas_por_solapa[1]++;
    }
	
    function evt__entrada__2()
    {
		$this->pasadas_por_solapa[2]++;
    }
	
    function evt__entrada__3()
    {
		$this->pasadas_por_solapa[3]++;
    }		
	

}


?>