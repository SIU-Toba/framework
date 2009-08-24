<?php
require_once('ci_abm_basico.php');

class ci_mensajes extends ci_abm_basico
{
	protected $s__filtro;
	
	function get_datos_listado()
	{
		$clausulas = array();
		if (isset($this->s__filtro)) {
			$clausulas = $this->dep('filtro')->get_sql_clausulas();
		}
		$clausulas[] = 'proyecto = '. toba::db()->quote(toba_contexto_info::get_proyecto());
		return toba_info_editores::get_mensajes_filtrados($clausulas);
	}

	function evt__agregar()
	{
		$this->set_pantalla('edicion');
	}


	function evt__cuadro__seleccion($seleccion)
	{
		$this->dependencia('datos')->cargar($seleccion);
		$this->set_pantalla('edicion');
	}

	//---- form -------------------------------------------------------

	function evt__formulario__alta($datos)
	{
		parent::evt__formulario__alta($datos);
		$this->set_pantalla('seleccion');
	}

	function evt__formulario__baja()
	{
		parent::evt__formulario__baja();
		$this->set_pantalla('seleccion');
	}

	function evt__formulario__modificacion($datos)
	{
		parent::evt__formulario__modificacion($datos);
		$this->set_pantalla('seleccion');
	}

	function evt__formulario__cancelar()
	{
		$this->dependencia('datos')->resetear();
		$this->set_pantalla('seleccion');
	}
	
	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro(toba_ei_filtro $filtro)
	{
		if (isset($this->s__filtro)) {
			$filtro->set_datos($this->s__filtro);
		}
	}

	function evt__filtro__filtrar($datos)
	{
		$this->s__filtro = $datos;
	}

	function evt__filtro__cancelar()
	{
		unset($this->s__filtro);
	}

}
?>