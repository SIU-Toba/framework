<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
//--------------------------------------------------------------------
class ci_actividad_local extends objeto_ci
{
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		//$propiedades[] = 'propiedad_a_persistir';
		return $propiedades;
	}

	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	//---- cuadro -------------------------------------------------------

	function evt__cuadro__seleccion($seleccion)
	{
	}

	function evt__cuadro__carga()
	{
	}


}

?>