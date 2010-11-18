<?php 
php_referencia::instancia()->agregar(__FILE__);

class ci_mascaras extends toba_ci
{
	protected $s__datos;
	protected $s__datos_expreg;

	function ini()
	{
		if (! isset($this->s__datos)) {	
			$this->s__datos = array(
						'numero_sin' => '123456.789',
						'fecha_sin' => '2006-10-26',
						
						'numero_original' => '123456.789',
						'fecha_original' => '2006-10-26',
						'moneda_original' => '123456.789',
						
						'numero_personal' => '123456.78',
						'moneda_personal' => '123456.789',
						'fecha_personal' => '2006-10-26'
					);
		}
	}
	
	function conf__form(toba_ei_formulario $componente)
	{
		$componente->set_datos($this->s__datos);
	}
	
	function evt__form__modificacion($datos)
	{
		$this->s__datos = $datos;
	}
	
	function conf__form_expreg(toba_ei_formulario $form)
	{
		if (isset($this->s__datos_expreg)) {
			$form->set_datos($this->s__datos_expreg);
		}
	}
	
	function evt__form_expreg__modificacion($datos)
	{
		$this->s__datos_expreg = $datos;
	}

	function get_fecha_inicio($nro)
	{
		return array("$nro/4/2001", '24:88');
	}
}

?>