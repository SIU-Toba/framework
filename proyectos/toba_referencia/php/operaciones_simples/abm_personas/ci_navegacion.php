<?php
require_once('nucleo/componentes/interface/objeto_ci.php');
require_once('operaciones_simples/consultas.php'); 

//----------------------------------------------------------------
class ci_navegacion extends objeto_ci
{
	protected $pantalla = "seleccion";
	protected $filtro;
	protected $seleccion;
	
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "pantalla";
		$propiedades[] = "filtro";
		$propiedades[] = "seleccion";
		return $propiedades;
	}

	function get_relacion()
	{
		return $this->dependencia('datos');
	}

	function get_editor()
	{
		return $this->dependencia("editor");
	}

	function get_pantalla_actual()
	{
		return $this->pantalla;
	}

	function evt__agregar()
	{
		$this->pantalla = 'edicion';
	}
	
	function evt__eliminar()
	{
		$this->get_relacion()->eliminar();
		$this->pantalla = 'seleccion';
	}
	
	function evt__cancelar()
	{
		$this->get_editor()->disparar_limpieza_memoria();
		$this->get_relacion()->resetear();
		$this->pantalla = 'seleccion';
	}
	
	function evt__procesar()
	{
		$this->dependencia('editor')->disparar_limpieza_memoria();
		$this->get_relacion()->sincronizar();
		$this->get_relacion()->resetear();
		$this->pantalla = 'seleccion';
	}

	//-------------------------------------------------------------------
	//-- DEPENDENCIAS
	//-------------------------------------------------------------------

	//-------- FILTRO ----

	function evt__filtro_personas__filtrar($datos)
	{
		$this->filtro = $datos;
	}

	function conf__filtro_personas()
	{
		if(isset($this->filtro)){
			return $this->filtro;
		}
	}

	function evt__filtro_personas__cancelar(){
		unset($this->filtro);	
	}

	//-------- CUADRO ----

	function conf__cuadro_personas()
	{
		if(isset($this->filtro)){
			return consultas::get_personas($this->filtro);
		}else{
			return consultas::get_personas();
		}
	}

	function evt__cuadro_personas__seleccion($id)
	{
		$this->seleccion = $id;
		$this->get_relacion()->cargar($this->seleccion);
		$this->pantalla = 'edicion';
	}
	
	function evt__cuadro_personas__eliminar($seleccion)
	{
		$this->get_relacion()->cargar($seleccion);
		$this->get_relacion()->eliminar();
	}
	//-------------------------------------------------------------------
}
?>