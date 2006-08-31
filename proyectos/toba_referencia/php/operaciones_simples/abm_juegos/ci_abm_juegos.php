<?php
require_once('nucleo/componentes/interface/toba_ci.php'); 
require_once('operaciones_simples/consultas.php'); 

class ci_abm_juegos extends toba_ci
{
	protected $seleccion;
	protected $seleccion_anterior;

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = 'seleccion';			// Clave seleccionada por el cuadro
		$propiedades[] = 'seleccion_anterior';	// Clave del registro cargado en el formulario
		return $propiedades;
	}

	private function get_tabla() 
	{
		return $this->dependencia('datos');
	}

	function resetear()
	{
		$this->get_tabla()->resetear();
		unset($this->seleccion);
		unset($this->seleccion_anterior);
	}

	//-------------------------------------------------------------------
	//--- Dependencias
	//-------------------------------------------------------------------

	//-- CUADRO --
	
	function conf__cuadro()
	{
		return consultas::get_juegos();
	}

	function evt__cuadro__seleccion($seleccion)
	{
		$this->seleccion = $seleccion;
	}

	//-- FORMULARIO

	function conf__formulario()
	{
		if(isset($this->seleccion)){
			$this->seleccion_anterior = $this->seleccion;
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
			toba::notificacion()->agregar('Error insertando');
			toba::logger()->error( $e->getMessage() );
		}
	}

	function evt__formulario__modificacion($datos)
	{
		if(isset($this->seleccion_anterior)){
			$t = $this->get_tabla();
			$t->set($datos);
			$t->sincronizar();
			$this->resetear();
		}
	}

	function evt__formulario__baja()
	{
		if(isset($this->seleccion_anterior)){
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