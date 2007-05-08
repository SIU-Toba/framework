<?php 
class ci_edicion extends toba_ci
{
	//-----------------------------------------------------------------------------------
	//---- DEPENDENCIAS -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	//---- form_edificios ---------------------------------------------------------------

	function evt__form_edificios__alta($datos)
	{
	}

	function evt__form_edificios__baja()
	{
	}

	function evt__form_edificios__modificacion($datos)
	{
	}

	function evt__form_edificios__cancelar()
	{
	}

	//El formato del retorno debe ser array('id_ef' => $valor, ...)
	function conf__form_edificios($componente)
	{
	}

	//---- form_sedes -------------------------------------------------------------------

	function evt__form_sedes__modificacion($datos)
	{
	}

	//El formato del retorno debe ser array('id_ef' => $valor, ...)
	function conf__form_sedes($componente)
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