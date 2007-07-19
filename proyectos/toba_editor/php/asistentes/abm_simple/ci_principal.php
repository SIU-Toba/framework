<?php 
class ci_principal extends toba_ci
{
	protected $s__datos_form_basico;
	
	function get_tablas()
	{
		return toba::fuente()->get_db()->get_lista_tablas();
	}
	
	//---- form_basico ------------------------------------------------------------------

	function evt__form_basico__modificacion($datos)
	{
		$this->s__datos_form_basico = $datos;
	}

	//El formato del carga debe ser array('id_ef' => $valor, ...)
	function conf__form_basico(toba_ei_formulario $form)
	{
		$form->set_datos($this->s__datos_form_basico);
	}
}

?>