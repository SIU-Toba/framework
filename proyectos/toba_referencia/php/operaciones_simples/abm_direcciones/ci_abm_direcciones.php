<?php 
php_referencia::instancia()->agregar(__FILE__);
php_referencia::instancia()->set_expandido(true);

class ci_abm_direcciones extends toba_ci
{
	protected $s__direcciones;
	protected $s__actual;
	
	function ini()
	{
		if (! isset($this->s__direcciones)) {
			$this->s__direcciones = array(
				'eperez@gmail.com' => 
					array('email' => 'eperez@gmail.com', 'nombre' => 'Ernesto Perez'),
				'msanchez@gmail.com' => 
					array('email' => 'msanchez@gmail.com', 'nombre' => 'Maria Sanchez'),
			);	
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
	 * Cuando se selecciona del cuadro, se guarda en sesión la selección
	 * Luego se fuerza la pantalla de edición
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
	 * Cuando se configura el formulario, si se seleccionó alguna dirección, se le pasan sus datos
	 */
	function conf__form(toba_ei_formulario $formulario)
	{
		if (isset($this->s__actual)) {
			$formulario->set_datos($this->s__direcciones[$this->s__actual]);	
			$formulario->ef('email')->set_solo_lectura();
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
	 * En la modificacion, reemplaza la dirección en el arreglo (gracias a que esta indexado)
	 */
	function evt__form__modificacion($datos)
	{
		$this->s__direcciones[$datos['email']] = $datos;
	}

	/**
	 * Cuando cancela la edición, se saca la selección actual y se vuelve al listado
	 */
	function evt__form__cancelar()
	{
		unset($this->s__actual);
		$this->set_pantalla('pant_listado');
	}

	
	/**
	 * En esta operación NO se transaccionó a medida que se hiban cambiando las direcciones
	 * La idea sería que esos cambios permanezcan en sesión hasta que el usuario 
	 * decida guardarlos.
	 * En este punto es que el usuario decide GUARDAR y aquí sería necesario analizar
	 * puntualmente que hacer con cada dirección: si actualizarla, borrarla o agregarla.
	 * 
	 * Como se ve más adelante, los componentes de persistencia son los que facilitan
	 * esta tarea, que de otra forma se tendría que hacer manualmente de alguna forma.
	 * 
	 * La otra opción sería transaccionar directamente con la base en cada evento.
	 * Esta última forma es la típica de los sistemas web, 
	 * pero para muchas operaciones no es una opción válida ya que generalmente es un requisito
	 * que la operación sea una sola transacción a nivel aplicación (aunque no lo sea a nivel base de datos)
	 */
	function evt__procesar()
	{
		$this->informar_msg('Aca se formaría la SQL para sincronizar con la base de datos', 'info');
	}
}

?>