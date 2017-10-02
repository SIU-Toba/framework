<?php 
//--------------------------------------------------------------------
class subclase_ci extends toba_testing_pers_ci
{
	function extender_objeto_js()
	{
	}

	/*function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		//$propiedades[] = 'propiedad_a_persistir';
		return $propiedades;
	}*/

	//---- Eventos CI -------------------------------------------------------

	function evt__procesar()
	{
	}

	function evt__cancelar()
	{
	}

	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	//---- cuadro -------------------------------------------------------

	function evt__cuadro__seleccion($seleccion)
	{
	}

	function conf__cuadro()
	{
	}

	//---- form1 -------------------------------------------------------

	function evt__form1__modificacion($datos)
	{
	}

	function conf__form1()
	{
	}


}

?>