<?php

class ci_navegacion extends toba_ci
{
	protected $s__filtro;
	const umbral_registros_filtro_obligatorio = 50;

	//-------------------------------------------------------------------
	//--- Eventos GLOBALES
	//-------------------------------------------------------------------
	
	function evt__guardar()
	{
		$this->dep('datos')->sincronizar();
		$this->dep('datos')->resetear();
		$this->set_pantalla('seleccionar');
	}

	function evt__cancelar()
	{
		$this->dep('datos')->resetear();
		$this->set_pantalla('seleccionar');
	}

	function evt__agregar()
	{
		$this->set_pantalla('editar');
	}
	
	function evt__eliminar()
	{
		$this->dep('datos')->eliminar();
		$this->dep('datos')->resetear();
		$this->set_pantalla('seleccionar');
	}

	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	//---- filtro -------------------------------------------------------

	function evt__filtro__filtrar($datos)
	{
		$this->s__filtro = $datos;
	}

	function evt__filtro__cancelar()
	{
		unset($this->s__filtro);
	}

	function conf__filtro($componente)
	{
		if(isset($this->s__filtro)) {
			$componente->set_datos($this->s__filtro);
			$componente->colapsar();
		}
	}
	
	//---- cuadro -------------------------------------------------------

	function conf_evt__cuadro__eliminar(toba_evento_usuario $evt)
	{
		$usuario = $evt->get_parametros();
		if ($usuario == toba::usuario()->get_id()) {
			$evt->anular();	
		}
	}
	
	function conf__cuadro($componente)
	{
		if (isset($this->s__filtro)) {
			$componente->set_datos( consultas_instancia::get_lista_usuarios($this->s__filtro) );
		}else{
			$filtro_obligatorio = consultas_instancia::get_cantidad_usuarios() > self::umbral_registros_filtro_obligatorio;
			if ( ! $filtro_obligatorio ) {
				$componente->set_datos( consultas_instancia::get_lista_usuarios() );
			}
		}
	}
	
	function evt__cuadro__seleccion($id)
	{
		$this->dep('datos')->cargar($id);
		$this->set_pantalla('editar');
	}

	function evt__cuadro__eliminar($id)
	{
		$this->dep('datos')->cargar($id);
		$this->evt__eliminar();	
	}
}
?>