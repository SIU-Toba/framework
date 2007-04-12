<?php
php_referencia::instancia()->agregar(__FILE__);
require_once('operaciones_simples/consultas.php');

class ci_principal extends toba_ci
{
	protected $s__datos_form;
	protected $s__datos_form_cascada;
	
	function evt__form__modificacion($datos)
	{
		$this->s__datos_form = $datos;	
	}
	
	function conf__form()
	{
		return $this->s__datos_form;	
	}

	//--------- CASCADAS

	function get_persona_de_combo($maestro, $id=null)
	{
		return array($maestro, "Persona $maestro");
	}

	function get_persona_nombre($id)
	{
		if ($id == 'A' || $id == 'B' || $id == 'C') {
			return "Persona $id";
		}
		return consultas::get_persona_nombre(array('id' => $id));
	}

	function evt__form_cascada__modificacion($datos)
	{
		$this->s__datos_form_cascada = $datos;	
	}
	
	function conf__form_cascada()
	{
		if (isset($this->s__datos_form_cascada)) {
			return $this->s__datos_form_cascada;
		} else {
			return array('maestro' => 'A', 'popup' => 1);
		}
	}

	
}

?>