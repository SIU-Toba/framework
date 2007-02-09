<?php
php_referencia::instancia()->agregar(__FILE__);

class ci_edicion extends toba_ci
{
	function get_relacion()	
	{
		return $this->controlador->get_relacion();
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
		$this->get_relacion()->tabla('deportes')->set_cursor($seleccion);
	}
	
	//-- Formulario --

	function conf__form_deportes()
	{
		if ($this->get_relacion()->tabla('deportes')->hay_cursor()) {
			return $this->get_relacion()->tabla('deportes')->get();
		}
	}

	function evt__form_deportes__modificacion($registro)
	{
		$this->get_relacion()->tabla('deportes')->set($registro);
		$this->evt__form_deportes__cancelar();
	}

	function evt__form_deportes__baja()
	{
		$this->get_relacion()->tabla('deportes')->set(null);
		$this->evt__form_deportes__cancelar();
	}

	function evt__form_deportes__alta($registro)
	{
		$this->get_relacion()->tabla('deportes')->nueva_fila($registro);
	}

	function evt__form_deportes__cancelar()
	{
		$this->get_relacion()->tabla('deportes')->resetear_cursor();
	}
}
?>