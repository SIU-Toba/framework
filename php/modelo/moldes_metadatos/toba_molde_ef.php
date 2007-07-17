<?php
/*
*	
*/
class toba_molde_ef
{
	private $datos;

	function __construct($identificador, $tipo)
	{
		$this->datos['identificador'] = $identificador;
		$this->datos['elemento_formulario'] = $tipo;
		$this->datos['etiqueta'] = $identificador;
		$this->datos['columnas'] = $identificador;
	}

	function get_identificador()
	{
		return $this->datos['identificador'];
	}

	//---------------------------------------------------
	//-- API de construccion
	//---------------------------------------------------

	function set_etiqueta($etiqueta)
	{
		$this->datos['etiqueta'] = $etiqueta;
	}
	
	function set_orden($orden)
	{
		$this->datos['orden'] = $orden;
	}

	function set_columnas($columnas)
	{
		if(!is_array($columnas)){
			throw new error_toba('Las columnas deben definirse mediante un array');	
		}else{
			$columnas = implode(', ',$columnas);
		}
		$this->datos['columnas'] = $columnas;
	}

	function set_propiedad($nombre, $valor)
	{
		$this->datos[$nombre] = $valor;
	}
		
	//---------------------------------------------------
	
	function get_datos()
	{
		return $this->datos;	
	}
}
?>