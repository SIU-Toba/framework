<?php 
class ci_editor extends toba_ci
{
	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__agregar()
	{
	}

	function evt__cancelar()
	{
	}

	function evt__eliminar()
	{
	}

	function evt__guardar()
	{
	}

	//-----------------------------------------------------------------------------------
	//---- DEPENDENCIAS -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	//---- cuadro -----------------------------------------------------------------------

	function evt__cuadro__seleccionar($seleccion)
	{
	}

	function conf__cuadro($componente)
	{
		return toba_info_editores::get_clases_editores();
	}

	//---- form_clase -------------------------------------------------------------------

	function evt__form_clase__modificacion($datos)
	{
	}

	function conf__form_clase($componente)
	{
	}

	//---- form_relaciones --------------------------------------------------------------

	function evt__form_relaciones__modificacion($datos)
	{
	}

	//El formato debe ser una matriz array('id_fila' => array('id_ef' => valor, ...), ...)
	function conf__form_relaciones($componente)
	{
	}
}

?>