<?php
class ci_edi_instituciones extends toba_ci
{

	function tabla($id)
	{
		return $this->controlador->dep('datos')->tabla($id);	
	}

	//---- form_institucion -------------------------------------------------------------

	function evt__form_institucion__modificacion($datos)
	{
		$this->tabla('institucion')->set($datos);
	}

	function conf__form_institucion($componente)
	{
		$componente->set_datos($this->tabla('institucion')->get());
	}

	//---- form_uas ---------------------------------------------------------------------

	function evt__form_uas__modificacion($datos)
	{
		$this->tabla('uas')->procesar_filas($datos);
	}

	function conf__form_uas($componente)
	{
		$componente->set_datos($this->tabla('uas')->get_filas());
	}
}

?>