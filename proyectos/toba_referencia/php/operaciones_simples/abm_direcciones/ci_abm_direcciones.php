<?php 
php_referencia::instancia()->agregar(__FILE__);

class ci_abm_direcciones extends toba_ci
{
	protected $s__direcciones;
	protected $s__actual;
	
	function ini()
	{
		if (! isset($this->s__direcciones)) {
			$this->s__direcciones = array();	
		}	
	}

	//----------------------------------------------------------
	//--------------- PANTALLA LISTADO -------------------------
	//----------------------------------------------------------
	
	/**
	 * Cuando se configura el cuadro, se le brindan las direcciones actuales
	 */
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$cuadro->set_datos($this->s__direcciones);
	}
	
	
	/**
	 * Cuando se selecciona del cuadro, se guarda en sesi�n la selecci�n
	 * Luego se fuerza la pantalla de edici�n
	 */
	function evt__cuadro__seleccion($seleccion)
	{
		$this->s__actual = $seleccion['email'];
		$this->set_pantalla('pant_edicion');
	}

	//----------------------------------------------------------
	//--------------- PANTALLA EDICION -------------------------
	//----------------------------------------------------------
		
	
	/**
	 * Cuando se configura el formulario, si se seleccion� alguna direcci�n, se le pasan sus datos
	 */
	function conf__form(toba_ei_formulario $formulario)
	{
		if (isset($this->s__actual)) {
			$formulario->set_datos($this->s__direcciones[$this->s__actual]);	
		}
	}	
	
	/**
	 * En el alta agrega la direccion al arreglo, indexandolo por email
	 * Luego selecciono el registro que se dio de alta
	 */
	function evt__form__alta($datos)
	{
		$this->s__direcciones[$datos['email']] = $datos;
		$this->evt__cuadro__seleccion($datos);
	}

	/**
	 * En la baja toma la seleccion actual y la elimina del arreglo de direcciones
	 * Luego se vuelve al listado
	 */
	function evt__form__baja()
	{
		unset($this->s__direcciones[$this->s__actual]);
		$this->set_pantalla('pant_listado');
	}

	/**
	 * En la modificacion, reemplaza la direcci�n en el arreglo (gracias a que esta indexado)
	 */
	function evt__form__modificacion($datos)
	{
		$this->s__direcciones[$datos['email']] = $datos;
	}

	/**
	 * Cuando cancela la edici�n, se saca la selecci�n actual y se vuelve al listado
	 */
	function evt__form__cancelar()
	{
		unset($this->s__actual);
		$this->set_pantalla('pant_listado');
	}

}

?>