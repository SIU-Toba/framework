<?php 
//--------------------------------------------------------------------
class ci_activacion extends objeto_ci
{
	protected $datos_1;
	protected $datos_2;
	
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = 'datos_1';
		$propiedades[] = 'datos_2';
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

	function conf__form_oblig()
	{
		if (isset($this->datos_1)) {
			return $this->datos_1;
		} else {
			$this->dependencia('form_oblig')->set_efs_obligatorios('obligatorio', false);
			$this->dependencia('form_oblig')->set_efs_obligatorios('no_obligatorio', true);
		}
	}
	
	function conf__form_activacion()
	{
		if (isset($this->datos_2)) {
			return $this->datos_2;
		} else {
			$this->dependencia('form_activacion')->desactivar_efs(array('editable', 'combo'));
		}
	}
	
	function evt__form_activacion__modificacion($datos)
	{
		$this->datos_2 = $datos;	
	}

	function evt__procesar()
	{
		
	}

}

?>