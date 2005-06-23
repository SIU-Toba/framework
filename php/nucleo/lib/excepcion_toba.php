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
* Excepción producida en tiempo de ejecución producidas por alguna interacción del usuario
*/
class excepcion_toba_usuario extends Exception
{

}

/**
* Excepción producida en tiempo de definición producidas por error del desarrollo
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