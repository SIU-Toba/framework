<?php

class excepcion_toba extends Exception
{
	function __construct($mensaje)
	{
		parent::__construct($mensaje);
	}

	function get_log_info()
	{
		$temp["mensaje"]=parent::getMessage();
		$temp["codigo"]=parent::getCode();
		$temp["archivo"]=parent::getFile();
		$temp["linea"]=parent::getLine();
		//$temp["trace_s"]=parent::getTraceAsString();
		$temp["trace"]=parent::getTrace();
		$x = "EXCEPCION!\n" . var_export( $temp, true );
		return $x;
	}
}

?>