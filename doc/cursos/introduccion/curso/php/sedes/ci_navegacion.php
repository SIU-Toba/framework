<?php 
class ci_navegacion extends toba_ci
{
	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__agregar()
	{
	}

	function evt__eliminar()
	{
	}

	function evt__guardar()
	{
	}

	function evt__volver()
	{
	}

	//-----------------------------------------------------------------------------------
	//---- DEPENDENCIAS -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	//---- cuadro_sedes -----------------------------------------------------------------

	function evt__cuadro_sedes__seleccion($seleccion)
	{
	}

	//El formato del retorno debe ser array( array('columna' => valor, ...), ...)
	function conf__cuadro_sedes($componente)
	{
	}

	//---- filtro_sedes -----------------------------------------------------------------

	function evt__filtro_sedes__filtrar($datos)
	{
	}

	function evt__filtro_sedes__cancelar()
	{
	}

	//El formato del retorno debe ser array('id_ef' => $valor, ...)
	function conf__filtro_sedes($componente)
	{
	}
}

?>