<?php
/*
*	
*/
class toba_molde_datos_tabla_col
{
	private $datos;

	function __construct($identificador, $tipo)
	{
		$this->datos['columna'] = $identificador;
		$this->datos['tipo'] = $tipo;
	}

	//---------------------------------------------------
	//-- API de construccion
	//---------------------------------------------------

	function set_secuencia($secuencia)
	{
		$this->datos['secuencia'] = $secuencia;
	}

	function pk()
	{
		$this->datos['pk'] = 1;
	}
	
	function externa()
	{
		$this->datos['externa'] = 1;
	}

	//---------------------------------------------------
	
	function get_datos()
	{
		return $this->datos;	
	}
}
?>