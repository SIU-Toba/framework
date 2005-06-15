<?php

/**
* Error interno de toba
*/
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

/**
* Excepci�n producida en tiempo de ejecuci�n producidas por alguna interacci�n del usuario
*/
class excepcion_toba_usuario extends Exception
{

}

/**
* Excepci�n producida en tiempo de definici�n producidas por error del desarrollo
*/
class excepcion_toba_def extends Exception
{
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