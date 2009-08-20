<?php
php_referencia::instancia()->agregar(__FILE__);
require_once('operaciones_simples/consultas.php'); 

//----------------------------------------------------------------
class ci_navegacion extends toba_ci
{
	protected $s__filtro;
	
	function get_relacion()
	{
		return $this->dependencia('datos');
	}

	function get_editor()
	{
		return $this->dependencia('editor');
	}

	function conf__edicion()
	{
		if (! $this->get_relacion()->esta_cargada()) {
			$this->pantalla()->eliminar_evento('eliminar');
		}
		$hay_cambios = $this->get_relacion()->hay_cambios();
		toba::menu()->set_modo_confirmacion('Esta a punto de abandonar la edición de la persona sin grabar, ¿Desea continuar?', $hay_cambios);
	}
	
	function evt__agregar()
	{
		$this->set_pantalla('edicion');
	}
	
	function evt__eliminar()
	{
		$this->get_relacion()->eliminar();
		$this->set_pantalla('seleccion');
	}
	
	function evt__cancelar()
	{
		$this->get_editor()->disparar_limpieza_memoria();
		$this->get_relacion()->resetear();
		$this->set_pantalla('seleccion');
	}
	
	function evt__procesar()
	{
		$this->dependencia('editor')->disparar_limpieza_memoria();
		$this->get_relacion()->sincronizar();
		$this->get_relacion()->resetear();
		$this->set_pantalla('seleccion');
	}

	//-------------------------------------------------------------------
	//-- DEPENDENCIAS
	//-------------------------------------------------------------------

	//-------- FILTRO ----

	function evt__filtro_personas__filtrar($datos)
	{
		$this->s__filtro = $datos;
	}

	function conf__filtro_personas($filtro)
	{
		if (isset($this->s__filtro)) {
			$filtro->set_datos($this->s__filtro);
		}
	}

	function evt__filtro_personas__cancelar()
	{
		unset($this->s__filtro);	
	}

	//-------- CUADRO ----

	function conf__cuadro_personas($cuadro)
	{
		if (isset($this->s__filtro)) {
			$datos = consultas::get_personas($this->s__filtro);
		} else {
			$datos = consultas::get_personas();
		}
		$cuadro->set_datos($datos);
	}

	function evt__cuadro_personas__seleccion($id)
	{
		$this->get_relacion()->cargar($id);
		$this->set_pantalla('edicion');
	}
	
}
?>