<?php

class excepcion_toba extends Exception
{
	function __construct($mensaje)
	{
		parent::__construct($mensaje);
	}

	function obtener_resumen()
	{
		$temp["mensaje"]=parent::getMessage();
		$temp["codigo"]=parent::getCode();
		$temp["archivo"]=parent::getFile();
		$temp["linea"]=parent::getLine();
		//$temp["trace_s"]=parent::getTraceAsString();
		$temp["trace"]=parent::getTrace();
		return $temp;
	}
}

?>