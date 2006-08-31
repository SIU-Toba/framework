<?php
require_once('nucleo/componentes/interface/objeto_ci.php'); 
require_once('operaciones_simples/consultas.php'); 

class ci_abm_deportes extends objeto_ci
{
	protected $seleccion;
	protected $filtro;
	protected $pantalla_actual = 'seleccion';
		
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = 'seleccion';			// Clave seleccionada por el cuadro
		$propiedades[] = 'filtro';				// Clave seleccionada por el cuadro
		$propiedades[] = 'pantalla_actual';
		return $propiedades;
	}

	private function get_tabla() 
	{
		return $this->dependencia('datos');
	}

	function resetear()
	{
		$this->get_tabla()->resetear();
		$this->pantalla_actual = 'seleccion';
		if(isset($this->seleccion)){
			unset($this->seleccion);
		}
	}

	function get_pantalla_actual()
	{
		return $this->pantalla_actual;	
	}
	
	function evt__agregar()
	{
		$this->pantalla_actual = 'edicion';
	}

	//-------------------------------------------------------------------
	//--- Dependencias
	//-------------------------------------------------------------------

	//-- FILTRO --

	function evt__filtro__filtrar($filtro)
	{
		$this->filtro = $filtro;
	}

	function conf__filtro()
	{
		if(isset($this->filtro)) return $this->filtro;
	}

	function evt__filtro__cancelar()
	{
		unset($this->filtro);
	}

	//-- CUADRO --
	
	function conf__cuadro()
	{
		if(isset($this->filtro)){
			return consultas::get_deportes($this->filtro);
		}else{
			return consultas::get_deportes();
		}
	}

	function evt__cuadro__seleccion($seleccion)
	{
		$this->seleccion = $seleccion;
		$this->pantalla_actual = 'edicion';
	}

	function evt__cuadro__eliminar($seleccion)
	{
		$t = $this->get_tabla();
		$t->cargar($seleccion);
		$t->eliminar_filas();
		$t->sincronizar();
		$this->resetear();
	}

	//-- FORMULARIO

	function conf__formulario()
	{
		if(isset($this->seleccion)){
			$t = $this->get_tabla();
			$t->cargar($this->seleccion);
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
		}catch(toba_excepcion $e){
			toba::get_cola_mensajes()->agregar('Error insertando');
			toba::get_logger()->error( $e->getMessage() );
		}
	}

	function evt__formulario__modificacion($datos)
	{
		if(isset($this->seleccion)){
			$t = $this->get_tabla();
			$t->set($datos);
			$t->sincronizar();
			$this->resetear();
		}
	}

	function evt__formulario__baja()
	{
		if(isset($this->seleccion)){
			$t = $this->get_tabla();
			$t->eliminar_filas();
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