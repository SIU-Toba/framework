<?php

class ci_edicion extends toba_ci
{
	protected $seleccion_deporte;

	function get_relacion()	
	{
		return $this->controlador->get_relacion();
	}

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "seleccion_deporte";
		return $propiedades;
	}

	//-------------------------------------------------------------------
	//--- Pantalla 'persona'
	//-------------------------------------------------------------------


	function conf__form_persona()
	{
	  return $this->get_relacion()->tabla('persona')->get();
	}

	function evt__form_persona__modificacion($registro)
	{
		$this->get_relacion()->tabla('persona')->set($registro);
	}

	//-------------------------------------------------------------------
	//--- Pantalla 'juegos'
	//-------------------------------------------------------------------

	function conf__form_juegos()	
	{
		return $this->get_relacion()->tabla('juegos')->get_filas(null,true);	
	}

	function evt__form_juegos__modificacion($datos)
	{
		$this->get_relacion()->tabla('juegos')->procesar_filas($datos);	
	}

	//-------------------------------------------------------------------
	//--- Pantalla 'deportes'
	//-------------------------------------------------------------------

	//-- Cuadro --

	function conf__cuadro_deportes()	
	{
		return $this->get_relacion()->tabla('deportes')->get_filas();	
	}

	function evt__cuadro_deportes__seleccion($seleccion) {	
		$this->seleccion_deporte = $seleccion;
	}
	
	//-- Formulario --

	function conf__form_deportes()
	{
		if(isset($this->seleccion_deporte)) {	
			return $this->get_relacion()->tabla('deportes')->get_fila($this->seleccion_deporte);	
		}
	}

	function evt__form_deportes__modificacion($registro)
	{
		if(isset($this->seleccion_deporte)){
			$this->get_relacion()->tabla('deportes')->modificar_fila($this->seleccion_deporte, $registro);	
			$this->evt__form_deportes__cancelar();	
		}
	}

	function evt__form_deportes__baja()
	{
		if(isset($this->seleccion_deporte)){
			$this->get_relacion()->tabla('deportes')->eliminar_fila( $this->seleccion_deporte );	
			$this->evt__form_deportes__cancelar();	
		}
	}

	function evt__form_deportes__alta($registro)
	{
		$this->get_relacion()->tabla('deportes')->nueva_fila($registro);
	}

	function evt__form_deportes__cancelar()
	{
		unset($this->seleccion_deporte);
	}
}
?>