<?php 
php_referencia::instancia()->agregar(__FILE__);

class ci_navegacion_principal extends toba_ci
{
	protected $precio_entrada = 300;
	protected $maximas_reservas = 3;
	protected $s__pago;
	protected $s__reservas = array();
	

	function evt__reservar()
	{
		$this->s__reservas[] = $this->s__pago;
		//--- Cambio explicito de pantalla
		$this->set_pantalla('pant_pago');	
	}
	
	//----------------------------------------------------------
	//-------------------------- Formulario --------------------
	//----------------------------------------------------------
	
	function conf__form_pago()
	{
		if (isset($this->s__pago)) {
			return $this->s__pago;
		}
	}
    
	function evt__form_pago__pagar($datos)
	{
    	$this->s__pago = $datos;
    	//--- Cambio explicito de pantalla
    	$this->set_pantalla('pant_ubicacion');
	}
	
	//----------------------------------------------------------
	//-------------------------- Pantallas --------------------------
	//----------------------------------------------------------
    
    
	function conf__pant_pago()
	{
		$cantidad = isset($this->s__reservas) ? count($this->s__reservas) : 0;
		$this->pantalla()->set_descripcion("Precio de la entrada $ {$this->precio_entrada}<br>"
					. "Cantidad de reservas: $cantidad");
	}
	
	function conf__pant_ubicacion()
	{
		$this->pantalla()->set_descripcion("Se reservará una entrada por $ {$this->s__pago['importe']}");
	}
	
	function evt__pant_pago__salida()
	{
		if ($this->s__pago['importe'] < $this->precio_entrada) {
            throw new toba_error("Debe abonar al menos $ {$this->precio_entrada}");
		}
	}

	function evt__pant_ubicacion__entrada()
	{
		if (count($this->s__reservas) >= $this->maximas_reservas) {
			throw new toba_error("Lo sentimos, se ha llegado al límite de reservas ({$this->maximas_reservas})");
		}
	}
    

}

?>