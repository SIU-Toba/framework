<?php

class ci_opciones_ef_seleccion extends toba_ci
{
	protected $s__datos_form;
	protected $s__datos_ml;
	
	function get_carpetas()
	{
		return toba_info_editores::get_carpetas_posibles();
	}
	
	function get_items($parametros)
	{
		return toba_info_editores::get_items_carpeta($parametros['carpeta_id'], $parametros['carpeta_proyecto']);		
	}
	
	
	//----- FORM Comun
	
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
	
	//----- FORM ML	
	
	function evt__ml__modificacion($datos)
	{
		$this->s__datos_ml = $datos;
	
	}
	
	function conf__ml(toba_ei_formulario_ml $form)
	{
		if (isset($this->s__datos_ml)) {
			$form->set_datos($this->s__datos_ml);
		}
	}	
	
	//------ GENERAL
	
	function evt__probar()
	{
		$descripcion = 'OK<br><pre>';
		if (isset($this->s__datos_form)) {
			$descripcion .= "\n\nFORM: ".var_export($this->s__datos_form, true);
		}
		if (isset($this->s__datos_ml)) {
			$descripcion .= "\n\nML: ".var_export($this->s__datos_ml, true);
		}		
		$descripcion .= '</pre>';
		$this->pantalla()->set_descripcion($descripcion);
	}
}

?>