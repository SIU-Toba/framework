<?php 
//--------------------------------------------------------------------
class ci_activacion extends toba_testing_pers_ci
{
	protected $datos_1;
	protected $datos_2;
	
	function ini()
	{
		$this->set_propiedades_sesion(array('datos_1', 'datos_2'));
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
		$this->dependencia('form_oblig')->set_efs_obligatorios('obligatorio', false);
		$this->dependencia('form_oblig')->set_efs_obligatorios('no_obligatorio', true);
		
		if (isset($this->datos_1)) {
			return $this->datos_1;
		}
	}
	
	function conf__form_activacion()
	{
		$this->dependencia('form_activacion')->desactivar_efs(array('editable', 'combo'));		
		if (isset($this->datos_2)) {
			return $this->datos_2;
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