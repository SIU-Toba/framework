<?php
/*
*	
*/
class toba_molde_cuadro_col
{
	private $datos;

	function __construct($identificador, $estilo)
	{
		$this->datos['clave'] = $identificador;
		$this->datos['estilo'] = $estilo;
	}

	//---------------------------------------------------
	//-- API de construccion
	//---------------------------------------------------

	function set_etiqueta($etiqueta)
	{
		$this->datos['titulo'] = $etiqueta;
	}
	
	function set_estilo($estilo)
	{
		$this->datos['estilo'] = $estilo;
	}
	
	function set_formato($formato)
	{
		$this->datos['formateo'] = $formato;
	}
	
	function set_orden($orden)
	{
		$this->datos['orden'] = $orden;
	}

	//---------------------------------------------------
	
	function get_datos()
	{
		return $this->datos;	
	}
}
?>