<?php

abstract class toba_filtro_columna
{
	protected $_datos;
	protected $_ef;
	protected $_padre;
	protected $_id_form_cond;
	protected $_estado = null;	

	function __construct($datos, $padre) 
	{
		$this->_datos = $datos;
		$this->_padre = $padre;
		$this->_id_form_cond = "col_" . $this->_padre->get_id_form() . $this->_datos['nombre'];		
		$this->ini();
	}
	
	function ini()
	{
		
	}

	function get_id_form()
	{
		return $this->_padre->get_id_form();
	}
	
	function get_tab_index()
	{
		return $this->_padre->get_tab_index();
	}
	
	function es_visible()
	{
		return $this->_datos['inicial'];
	}
	
	function get_nombre()
	{
		return $this->_datos['nombre'];
	}
	
	function get_etiqueta()
	{
		return $this->_datos['etiqueta'];
	}

	function get_consumo_javascript()
	{
		return $this->_ef->get_consumo_javascript();
	}
	
	function crear_objeto_js()
	{
		return $this->_ef->crear_objeto_js();
	}
	
	function validar_estado()
	{
		return $this->_ef->validar_estado();
	}
	
	function resetear_estado()
	{
		$this->_ef->resetear_estado();
		$this->estado = null;
	}
	
	function get_estado()
	{
		return $this->_estado;
	}
	
	function set_estado($estado)
	{
		$this->estado = $estado;
	}

	abstract function tiene_condicion();	
	abstract function get_html_condicion();
	abstract function get_html_valor();
	abstract function cargar_estado_post();
	
}

?>