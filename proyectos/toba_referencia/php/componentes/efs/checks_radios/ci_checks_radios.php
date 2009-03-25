<?php 
php_referencia::instancia()->agregar(__FILE__);

class ci_checks_radios extends toba_ci
{
	protected $s__solo_lectura;
	protected $s__datos;
	protected $cant = 10;
	
	function ini()
	{
		if (! isset($this->s__solo_lectura)) {
			$this->s__solo_lectura = 0;	
		}
		if (! isset($this->s__datos)) {
			$this->s__datos = array('columnas' => 3);	
		}
	}
	
	function get_opciones($master=null)
	{
		$salida = array();
		for ($i = 0; $i < $this->cant; $i++) {
			if (! isset($master) || $master == 0 || $i % $master == 0) {
				$salida[] = array($i, "Opción $i");
			}
		}
		return $salida;
	}
	
	function evt__form_columnas__modificacion($datos)
	{
		$this->s__datos = $datos;
	}
	
	function conf__form_columnas(toba_ei_formulario $form)
	{
		$form->set_solo_lectura(null, $this->s__solo_lectura);
		$form->ef('multi_check')->set_cantidad_columnas($this->s__datos['columnas']);
		$form->ef('radio')->set_cantidad_columnas($this->s__datos['columnas']);
		$form->set_datos($this->s__datos);
	}
	
	function evt__solo_lectura()
	{
		$this->s__solo_lectura = ! $this->s__solo_lectura;	
	}
}

?>