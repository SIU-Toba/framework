<?php 
class ci_combo_editable extends toba_ci
{
	protected $s__datos_form;
	protected $s__datos_ml;
	
	
	//--------------------------------------
	//----- FORM COMUN
	//--------------------------------------
	
	function evt__form__modificacion($datos)
	{
		$this->s__datos_form = $datos;
	}
	
	function conf__form(toba_ei_formulario $form)
	{
		if (isset($this->s__datos_form)) {
			$form->set_datos($this->s__datos_form);
		}
	}

	//--------------------------------------
	//----- FORM ML
	//--------------------------------------

	function evt__ml__modificacion($datos)
	{
		$this->s__datos_ml = $datos;
	}
	
	function conf__ml(toba_ei_formulario $form)
	{
		if (isset($this->s__datos_ml)) {
			$form->set_datos($this->s__datos_ml);
		}
	}
	
}

?>