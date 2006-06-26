<?php 
//--------------------------------------------------------------------
class ci_activacion extends objeto_ci
{
	protected $datos_1;
	
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = 'datos_1';
		return $propiedades;
	}

	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	//---- form_oblig -------------------------------------------------------

	function evt__form_oblig__modificacion($datos)
	{
		$this->datos_1 = $datos;
	}

	function evt__form_oblig__carga()
	{
		$this->dependencia('form_oblig')->set_efs_obligatorios('obligatorio', false);
		$this->dependencia('form_oblig')->set_efs_obligatorios('no_obligatorio', true);
		return $this->datos_1;
	}


}

?>