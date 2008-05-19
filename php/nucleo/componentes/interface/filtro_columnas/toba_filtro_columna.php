<?php

abstract class toba_filtro_columna
{
	protected $_datos;
	protected $_ef;

	function __construct($datos) 
	{
		$this->_datos = $datos;
		$this->ini();
	}
	
	function ini()
	{
		
	}
	

	function get_id_form()
	{
		return '';
	}
	
	function get_tab_index()
	{
		return 0;
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

	abstract function tiene_condicion();	
	abstract function get_html_condicion();
	abstract function get_html_valor();
	
}

?>