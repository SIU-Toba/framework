<?php 
//--------------------------------------------------------------------
class ci_validacion_server extends toba_testing_pers_ci
{
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


	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	//---- form_server -------------------------------------------------------

	function evt__form_server__modificacion($datos)
	{
	}

	function conf__form_server()
	{
	}
	
	function conf__ml()
	{
		
	}

	function evt__ml__modificacion($datos)
	{
	}
}

?>