<?php
define("apex_wddx_usuario","wddx_usu");
define("apex_wddx_instancia","wddx_i");
define("apex_wddx_momento","wddx_mom");
define("apex_wddx_paquete","pq");
define("apex_wddx_timeout","30");

class comunicador
{
	var $ip;
	var $puerto;
	var $punto_acceso;
	var $response_headers;
	var $response_body;
	
	function comunicador($ip, $puerto, $punto_acceso)
	{
		$this->establecer_instancia($ip, $puerto, $punto_acceso);
	}
//-------------------------------------------------------------------------
	
	function establecer_instancia($ip, $puerto, $punto_acceso)
	{
		$this->ip = $ip;
		$this->puerto = $puerto;
		$this->punto_acceso = $punto_acceso;
	}
//-------------------------------------------------------------------------

	function empaquetar($datos, $campos_toba=true)
	//Genera un paquete WDDX
	{
		if(is_array($datos)){
			//Agrego HEADERS standart que utiliza el TOBA para los envios
			if($campos_toba){
				if( apex_solicitud_tipo == "wddx"){
					$datos[apex_wddx_usuario] = apex_pa_usuario_anonimo;
				}elseif( apex_solicitud_tipo == "browser" ){
					global $solicitud;
					$datos[apex_wddx_usuario] = $solicitud->hilo->obtener_usuario();
				}
			$datos[apex_wddx_momento] = time();
			$datos[apex_wddx_instancia] = $_SERVER["SERVER_ADDR"];
			}
			return base64_encode(wddx_serialize_vars("datos"));
			//return urlencode(wddx_serialize_vars("datos"));
		}else{
			return null;
		}
	}
//-------------------------------------------------------------------------
	
	function desempaquetar($paquete)
	//Genera un array en base a un paquete WDDX
	{
		$temp = wddx_deserialize(base64_decode($paquete));
		//$temp = wddx_deserialize(urldecode($paquete));
		return $temp['datos'];
	}
//-------------------------------------------------------------------------

	function transmitir($datos, $item=null, $campos_toba=true, $debug=false)
	//Implementacion de una comunicacion SIMPLE
	//(request-response) via WDDX
	{
		//Abro el socket
		$sock = fsockopen($this->ip, $this->puerto, $errno, $errstr, apex_wddx_timeout);
		if (!$sock) return 0;
		
		//Preparo datos
		$datos = "pq=" . $this->empaquetar($datos, $campos_toba);
		
		//Preparo la llamada al ITEM que correponda
		if(isset($item)){
			$punto_acceso = $this->punto_acceso . "?" . apex_hilo_qs_item . "=" . 
								$item[0] . apex_qs_separador . $item[1];
		}else{
			$punto_acceso = $this->punto_acceso;
		}

		//-[1]- HTTP-REQUEST

		if($debug){
			echo $this->ip . ":" . $this->puerto . $punto_acceso . "<br>";
			echo $datos;
		}

		fputs($sock, "POST " . $punto_acceso ." HTTP/1.0\r\n");
		//fputs($sock, "Host: secure.example.com\r\n");
		fputs($sock, "Content-type: application/x-www-form-urlencoded\r\n");
		fputs($sock, "Content-length: " . strlen($datos) . "\r\n");
		fputs($sock, "Accept: */*\r\n");
		fputs($sock, "\r\n");
		fputs($sock, "$datos\r\n");
		fputs($sock, "\r\n");

		//-[2]- HTTP-RESPONSE

		// Recibo HEADERS
		$this->response_headers = array();
		while ($str = trim(fgets($sock, 4096))){
		  $this->response_headers[] =$str;
		}
		// Recibo BODY		  
		$this->response_body = array();
		while (!feof($sock)){
			$this->response_body[] = fgets($sock, 4096);
		}
		fclose($sock);
		//El STATUS del HTTP response es OK???
		$x = explode(" ",$this->response_headers[0]);
		if( $x[1] == "200"){
			return true;
		}else{
			return false;
		}
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