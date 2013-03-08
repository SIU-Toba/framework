<?php

abstract class toba_elemento_transversal_info implements toba_meta_clase
{
	protected $_tipo_elemento;
	protected $_datos;
	protected $_id;
	protected $_molde;

	function __construct($datos, $tipo = null)
	{
		$this->_id = $datos;
		$this->_tipo_elemento = $tipo;
		$this->ini();
	}

	function ini()
	{
	}

	//-----------------------------------------------------------------------------------
	function get_nombre_instancia_abreviado()
	{
		return $this->_tipo_elemento;
	}

	function set_subclase($nombre, $archivo, $pm)
	{
		
	}

	//-----------------------------------------------------------------------------------	
	function get_clase_nombre()
	{
		return null;
	}

	function get_clase_archivo()
	{
		return null;
	}

	function get_subclase_nombre()
	{
		return null;
	}

	function get_subclase_archivo()
	{
		return null;
	}

	function get_molde_vacio()
	{
		$molde = new toba_codigo_clase( $this->get_subclase_nombre(), $this->get_clase_nombre() );
		return $molde;
	}

	function get_metaclase_subcomponente($subcomponente)
	{
	}

	function get_molde_subclase()
	{
		return $this->get_molde_vacio();
	}

	//---------------------------------------------------------------------
	//-- Preguntas sobre EVENTOS
	//---------------------------------------------------------------------

	function eventos_predefinidos()
	{
		return array();
	}

	static function get_eventos_internos(toba_datos_relacion $dr)
	{
		$eventos = array();
		return $eventos;
	}



}
?>
