<?php
define("apex_soap_usuario","soap_usu");
define("apex_soap_instancia","soap_i");
define("apex_soap_momento","soap_mom");
define("apex_soap_paquete","pq");
define("apex_soap_timeout","30");

class comunicador_soap
{
	var $wsdl_url;
	var $ip;
	var $puerto;
	var $punto_acceso;
	var $response_headers;
	var $response_body;
	
	function comunicador_soap($wsdl_url, $punto_acceso)
	{
		$this->establecer_instancia($wsdl_url, $punto_acceso);
	}
//-------------------------------------------------------------------------
	
	function establecer_instancia($wsdl_url, $punto_acceso)
	{
		$this->wsdl_url = $wsdl_url;
		//$this->ip = $ip;
		//$this->puerto = $puerto;
		$this->punto_acceso = $punto_acceso;
	}
//-------------------------------------------------------------------------

	function empaquetar($datos, $campos_toba=true)
	//Genera un paquete soap
	{
		if(is_array($datos)){
			//Agrego HEADERS standart que utiliza el TOBA para los envios
			if($campos_toba){
				if( apex_solicitud_tipo == "soap"){
					$datos[apex_soap_usuario] = apex_pa_usuario_anonimo;
				}elseif( apex_solicitud_tipo == "browser" ){
					global $solicitud;
					$datos[apex_soap_usuario] = $solicitud->hilo->obtener_usuario();
				}
			$datos[apex_soap_momento] = time();
			$datos[apex_soap_instancia] = $_SERVER["SERVER_ADDR"];
			}
			return base64_encode(soap_serialize_vars("datos"));
			//return urlencode(soap_serialize_vars("datos"));
		}else{
			return null;
		}
	}
//-------------------------------------------------------------------------
	
	function desempaquetar($paquete)
	//Genera un array en base a un paquete soap
	{
		$temp = soap_deserialize(base64_decode($paquete));
		//$temp = soap_deserialize(urldecode($paquete));
		return $temp['datos'];
	}
//-------------------------------------------------------------------------

	function transmitir($datos, $item=null, $campos_toba=true, $debug=false)
	//Implementacion de una comunicacion SIMPLE
	//(request-response) via soap
	{
		
		
		
		$wsdl_url = 'http://localhost:8080/wsdl/hello_server.php?wsdl';
		$client = new SoapClient($wsdl_url);
		
		$temp = $client->sayHello($datos);
		
		
		
	}
//-------------------------------------------------------------------------

	function obtener_headers()
	{
		return $this->response_headers;
	}
//-------------------------------------------------------------------------
	
	function obtener_body()
	{
		return 	$this->response_body;
	}
//-------------------------------------------------------------------------

	function obtener_datos()
	{
		$contenido = implode("",$this->response_body);
		return $this->desempaquetar($contenido);
	}
//-------------------------------------------------------------------------

}
?>