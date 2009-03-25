<?php
php_referencia::instancia()->agregar(__FILE__);
require_once('operaciones_simples/consultas.php'); 

class ci_abm_deportes extends toba_ci
{
	protected $s__seleccion;
	protected $s__filtro;
		
	private function get_tabla() 
	{
		return $this->dependencia('datos');
	}

	function resetear()
	{
		$this->get_tabla()->resetear();
		$this->set_pantalla('seleccion');
		if (isset($this->s__seleccion)) {
			unset($this->s__seleccion);
		}
	}

	function evt__agregar()
	{
		$this->set_pantalla('edicion');
	}

	//-------------------------------------------------------------------
	//--- Dependencias
	//-------------------------------------------------------------------

	//-- FILTRO --

	function evt__filtro__filtrar($filtro)
	{
		$this->s__filtro = $filtro;
	}

	function conf__filtro($filtro)
	{
		if (isset($this->s__filtro)) {
			$filtro->set_datos($this->s__filtro);
		}
	}

	function evt__filtro__cancelar()
	{
		unset($this->s__filtro);
	}

	//-- CUADRO --
	
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__filtro)) {
			$datos = consultas::get_deportes($this->s__filtro);
		} else {
			$datos = consultas::get_deportes();
		}
		$cuadro->set_datos($datos);
	}

	function evt__cuadro__seleccion($seleccion)
	{
		$this->s__seleccion = $seleccion;
		$this->set_pantalla('edicion');
	}

	function evt__cuadro__eliminar($seleccion)
	{
		$t = $this->get_tabla();
		$t->cargar($seleccion);
		$t->eliminar_filas(false);
		$t->sincronizar();
		$this->resetear();
	}

	//-- FORMULARIO

	function conf__formulario()
	{
		if (isset($this->s__seleccion)) {
			$t = $this->get_tabla();
			$t->cargar($this->s__seleccion);
			return $t->get();
		}
	}

	function evt__formulario__alta($datos)
	{
		$t = $this->get_tabla();
		$t->nueva_fila($datos);
		try{
			$t->sincronizar();
			$this->resetear();
		}catch(toba_error $e){
			toba::notificacion()->agregar('Error insertando');
			toba::logger()->error($e->getMessage());
		}
	}

	function evt__formulario__modificacion($datos)
	{
		if (isset($this->s__seleccion)) {
			$t = $this->get_tabla();
			$t->set($datos);
			$t->sincronizar();
			$this->resetear();
		}
	}

	function evt__formulario__baja()
	{
		if (isset($this->s__seleccion)) {
			$t = $this->get_tabla();
			$t->eliminar_filas(false);
			$t->sincronizar();
			$this->resetear();
		}
	}

	function evt__formulario__cancelar()
	{
		$this->resetear();		
	}
}

?>