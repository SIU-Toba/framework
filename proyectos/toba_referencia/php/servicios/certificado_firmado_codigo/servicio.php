<?php

class servicio extends toba_servicio_web
{
	function inicializar()
	{
		//Agrego la clave publica dinamicamente
		$carpeta = dirname(__FILE__);
		$this->agregar_mapeo_headers("dependencia=agronomia,otro=test", $carpeta.'/headers_cliente.public');
	}

	function get_opciones()
	{
		$carpeta = dirname(__FILE__);
		
		//Agrego los certificados manualmente
		$certificado_cliente = ws_get_cert_from_file($carpeta."/cliente.crt");
		$clave_privada = ws_get_cert_from_file($carpeta."/servidor.pkey");
		$seguridad = array("encrypt" => true,
					"algorithmSuite" => "Basic256Rsa15",
					"securityTokenReference" => "IssuerSerial");

		$policy = new WSPolicy(array("security"=> $seguridad));
		$security = new WSSecurityToken(array(
											"privateKey" => $clave_privada,
											"receiverCertificate" => $certificado_cliente)
		);

		return array(
			"firmado"			=> true,			//Esta opción fuerza a que el mensaje tiene que estar firmado con RSA
            "policy" 			=> $policy,
            "securityToken"		=> $security,
             'actions' => array(
					"http://siu.edu.ar/toba_referencia/serv_pruebas/eco" => "eco",
			),
		);
	}


	/**
	 * Responde exactamente con la misma cadena enviada
	 * @param string $texto texto a repetir
	 * @return string $texto texto repetido
	 */
	function op__eco(toba_servicio_web_mensaje $mensaje, $headers)
	{
		$xml = new SimpleXMLElement($mensaje->get_payload());
		$texto = (string) $xml->texto;
		$dependencia = $headers['dependencia'];		
		$payload = <<<XML
<ns1:eco xmlns:ns1="http://siu.edu.ar/toba_referencia/pruebas">
	<texto>Texto: $texto 
	Dependencia: $dependencia</texto>
</ns1:eco>
XML;
		return new toba_servicio_web_mensaje($payload);
	}

}

?>