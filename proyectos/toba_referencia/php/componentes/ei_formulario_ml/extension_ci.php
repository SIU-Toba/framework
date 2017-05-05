<?php
php_referencia::instancia()->agregar(__FILE__);

class extension_ci extends toba_ci
{
	//Se guardan en sesión los datos actuales del formulario
	protected $s__datos;

	//------------------------------------------------------------------------
	//----------------- ML con agregado en javascript -------------------------
	//------------------------------------------------------------------------
	
	function conf__ml($ml)
	{
		if (isset($this->s__datos)) {
			$ml->set_datos($this->s__datos);
		}
	}
	
	function evt__ml__modificacion($datos)
	{
		$this->s__datos = $datos;
	}
	
	function evt__ml__seleccion($id_fila)
	{
		$this->informar_msg('Se selecciona la fila con importe : '.$this->s__datos[$id_fila]['importe'], 'info');
	}
	
	function evt__ml__describir($id_fila)
	{
		$this->informar_msg("Datos de la fila $id_fila: ". str_replace("\n", '',print_r($this->s__datos[$id_fila], true))."\n", 'info');
		$this->dependencia('ml')->deseleccionar();
	}

	//------------------------------------------------------------------------
	//----------------- ML con agregado en php -------------------------------
	//------------------------------------------------------------------------
	
	function evt__ml_php__pedido_registro_nuevo()
	{
		$this->dep('ml_php')->set_registro_nuevo(array('fecha' => date('Y-m-d'), 'importe' => 100));
	}
	
	function conf__ml_php($ml)
	{
		return $this->conf__ml($ml);	
	}
	
	function evt__ml_php__modificacion($datos)
	{
		$this->evt__ml__modificacion($datos);
	}
	
	function evt__ml_php__seleccion($id_fila)
	{
		$this->evt__ml__seleccion($id_fila);
	}
	
	function evt__ml_php__describir($id_fila)
	{
		$this->evt__ml__describir($id_fila);
	}	
	
	
	function evt__procesar()
	{
		
	}

}


?>
