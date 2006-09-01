<?php
require_once('nucleo/componentes/interface/toba_ci.php'); 
//--------------------------------------------------------------------
class ci_principal extends toba_ci
{
	protected $s__datos_form;
	
	function evt__form__modificacion($datos)
	{
		$this->s__datos_form = $datos;	
	}
	
	function conf__form()
	{
		return $this->s__datos_form;	
	}
}

?>