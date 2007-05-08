<?php 
class ci_edicion extends toba_ci
{
	//-----------------------------------------------------------------------------------
	//---- DEPENDENCIAS -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	//---- form_institucion -------------------------------------------------------------

	function evt__form_institucion__modificacion($datos)
	{
	}

	//El formato del retorno debe ser array('id_ef' => $valor, ...)
	function conf__form_institucion($componente)
	{
	}

	//---- form_uas ---------------------------------------------------------------------

	function evt__form_uas__modificacion($datos)
	{
	}

	//El formato debe ser una matriz array('id_fila' => array('id_ef' => valor, ...), ...)
	function conf__form_uas($componente)
	{
	}
}

?>