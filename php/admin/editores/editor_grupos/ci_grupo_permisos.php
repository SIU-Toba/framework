<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
//--------------------------------------------------------------------
class ci_grupo_permisos extends objeto_ci
{

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		//$propiedades[] = 'propiedad_a_persistir';
		return $propiedades;
	}

	//---- Eventos CI -------------------------------------------------------

	function evt__procesar()
	{
	}

	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	//---- form -------------------------------------------------------

	function evt__form__modificacion($datos)
	{
		//$this->dependencia('datos')
	}

	function evt__form__carga()
	{
	}


}

?>