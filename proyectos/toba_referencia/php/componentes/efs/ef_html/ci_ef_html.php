<?php 
php_referencia::instancia()->agregar(__FILE__);
class ci_ef_html extends toba_ci
{
	protected $s__datos;
	protected $cambiar_toolbar = false;

	function evt__form__probar($datos)
	{
		$this->s__datos = $datos;
		//Se pasa al campo solo_lectura el valor del campo editable
		$this->s__datos['solo_lectura'] = $this->s__datos['editable'];
	}
	
	function evt__form__toolbar($datos)
	{
		$this->evt__form__probar($datos);
		$this->cambiar_toolbar = true;
	}

	function conf__form(toba_ei_formulario $form)
	{
		if (isset($this->s__datos)) {
			$form->set_datos($this->s__datos);
		}
		
		if ($this->cambiar_toolbar) {
			//Se utilizar el API de fckeditor para cambiar el toolbar
			$form->ef('editable')->set_botonera('Full');
		}
	}
}

?>