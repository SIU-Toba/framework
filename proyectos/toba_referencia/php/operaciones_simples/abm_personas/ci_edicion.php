<?php
php_referencia::instancia()->agregar(__FILE__);

class ci_edicion extends toba_ci
{
	protected $s__deporte;

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
		$this->s__deporte = $seleccion;
	}
	
	//-- Formulario --

	function conf__form_deportes()
	{
		if(isset($this->s__deporte)) {	
			return $this->get_relacion()->tabla('deportes')->get_fila($this->s__deporte);	
		}
	}

	function evt__form_deportes__modificacion($registro)
	{
		if(isset($this->s__deporte)){
			$this->get_relacion()->tabla('deportes')->modificar_fila($this->s__deporte, $registro);	
			$this->evt__form_deportes__cancelar();	
		}
	}

	function evt__form_deportes__baja()
	{
		if(isset($this->s__deporte)){
			$this->get_relacion()->tabla('deportes')->eliminar_fila( $this->s__deporte );	
			$this->evt__form_deportes__cancelar();	
		}
	}

	function evt__form_deportes__alta($registro)
	{
		$this->get_relacion()->tabla('deportes')->nueva_fila($registro);
	}

	function evt__form_deportes__cancelar()
	{
		unset($this->s__deporte);
	}
}
?>