<?php

//Pruebas SOAP

	require_once("nucleo/lib/comunicador_soap.php");

	$mensaje["texto1"] = "111 - Texto del mensaje";
	$mensaje["texto2"] = "222 - Texto del mensaje";
	$mensaje["texto3"] = "333 - Texto del mensaje";

	//$ip = "168.83.60.146";
	//$puerto = 3333;
	//$ip = "192.168.0.10";
	//$puerto = 3333;
	//$ip = "168.83.60.212";
	//$puerto = 8080;
	$punto_acceso = "/toba/soap.php";


	$instancia =& new comunicador_soap($wsdl_url, $punto_acceso);
	
	$item = array("toba","/red/echo");//Item al que quiero enviar informacion
	//$item = array("toba","/red/hola");//Item al que quiero enviar informacion
	
	if( $instancia->transmitir($mensaje, $item) ){
		//ei_arbol($instancia->obtener_headers(),"RESPONSE - HEADERS");
		//ei_arbol($instancia->obtener_body(),"RESPONSE - BODY");
		ei_arbol( $instancia->obtener_datos(),"RESPONSE - DATOS");
	}else{
		echo "No se pudo enviar el MENSAJE";
		ei_arbol($instancia->obtener_headers(),"RESPONSE - HEADERS");
	}

//---------------------------------------------------
?>