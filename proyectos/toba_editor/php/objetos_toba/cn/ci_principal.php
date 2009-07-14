<?php
require_once('objetos_toba/ci_editores_toba.php');

class ci_principal extends ci_editores_toba
{
	protected $clase_actual = 'toba_cn';

	// *******************************************************************
	// *******************  tab DEPENDENCIAS  ****************************
	// *******************************************************************
	/*
		Metodos necesarios para que el CI de eventos funcione
	*/
	function evt__1__salida()
	{
		$this->dep('dependencias')->limpiar_seleccion();
	}

	function get_dbr_dependencias()
	{
		return $this->get_entidad()->tabla('dependencias');
	}	

	// *******************************************************************
	// *******************  tab CONSUMO  ****************************
	// *******************************************************************
	
	function get_lista_objetos_toba()
	{
		$datos = toba_info_editores::get_lista_objetos_toba(',toba_cn');
		// Elimino de la lista el objeto actual
		$objeto_editado = $this->get_entidad()->tabla('base')->get_columna('objeto');
		foreach(array_keys($datos) as $id) {
			if ($datos[$id]['objeto'] == $objeto_editado) {
				unset($datos[$id]);
			}
		}
		return $datos;
	}
	
	function get_tabla_consumo()
	{
		return $this->get_entidad()->tabla('consumo');	
	}
	
	//-------------------------------------------------------------
	//-- Formulario
	//-------------------------------------------------------------

	function evt__form_consumo__alta($datos)
	{
		$this->get_tabla_consumo()->nueva_fila($datos);
	}
	
	function evt__form_consumo__baja()
	{
		$this->get_tabla_consumo()->eliminar_fila($this->get_tabla_consumo()->get_cursor());
		$this->evt__form_consumo__cancelar();
	}
	
	function evt__form_consumo__modificacion($datos)
	{
		$this->get_tabla_consumo()->set($datos);
		$this->evt__form_consumo__cancelar();
	}
	
	function conf__form_consumo()
	{
		if ($this->get_tabla_consumo()->hay_cursor()) {
			return $this->get_tabla_consumo()->get();
		}
	}

	function evt__form_consumo__cancelar()
	{
		$this->get_tabla_consumo()->resetear_cursor();
	}

	//-------------------------------------------------------------
	//-- Cuadro
	//-------------------------------------------------------------

	function evt__cuadro_consumo__seleccion($id)
	{
		$this->get_tabla_consumo()->set_cursor($id);
	}

	function conf__cuadro_consumo()
	{
		return $this->get_tabla_consumo()->get_filas();
	}
}