<?php

//Pruebas WDDX

//--------------- PAQUETE ----------------

	$mensaje["salida"]= time();
	$mensaje["usuario"] = $this->hilo->obtener_usuario();
	$mensaje["texto"] = "Texto del mensaje";
	
	ei_arbol($mensaje);

	$paquete = urlencode(wddx_serialize_vars("mensaje"));
	//$paquete = base64_encode(wddx_serialize_vars("mensaje"));
	
/*
	ei_texto($paquete);
	$mensaje2 = wddx_deserialize(base64_decode($paquete));
	ei_arbol($mensaje2);
*/


//--------------- ENVIO de INFORMACION ----------------

//	$instancia = "168.83.60.212";
//	$puerto = 80;
	$instancia = "192.168.0.10";
	$puerto = 3333;
//	$puerto = 8189;
	$acceso = "/toba/wddx.php";


	$sock = fsockopen($instancia, $puerto, $errno, $errstr, 30);
if (!$sock) die("$errstr ($errno)\n");

$data = "pq=" . $paquete;
//$data = "pq=hola&lkjsglkjsg=lkngslkjsg&ddd=nkjfjlkf";

echo $data;

fputs($sock, "POST $acceso HTTP/1.0\r\n");
//fputs($sock, "Host: secure.example.com\r\n");
fputs($sock, "Content-type: application/x-www-form-urlencoded\r\n");
fputs($sock, "Content-length: " . strlen($data) . "\r\n");
fputs($sock, "Accept: */*\r\n");
fputs($sock, "\r\n");
fputs($sock, "$data\r\n");
fputs($sock, "\r\n");

//--------------- RECEPCION de INFORMACION ----------------

$headers = "";
while ($str = trim(fgets($sock, 4096)))
  $headers .= "$str\n";

echo ei_mensaje(formato_salto_linea_html($headers));

print "\n";

$body = "";
while (!feof($sock))
  $body .= fgets($sock, 4096);

	echo ei_mensaje($body);
  
fclose($sock);

//---------------------------------------------------
?>