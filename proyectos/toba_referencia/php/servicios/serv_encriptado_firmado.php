<?php

class serv_encriptado_firmado extends toba_servicio_web
{

	function get_opciones()
	{
		$carpeta = dirname(__FILE__);
		$certificado_cliente = ws_get_cert_from_file($carpeta."/cert_cliente.cert");
		$clave_privada = ws_get_cert_from_file($carpeta."/clave_server.pem");		
		$seguridad = array("encrypt" => true,
					"algorithmSuite" => "Basic256Rsa15",
					"securityTokenReference" => "IssuerSerial");
 
		$policy = new WSPolicy(array("security"=> $seguridad));
		$security = new WSSecurityToken(array(
											"privateKey" => $clave_privada,
											"receiverCertificate" => $certificado_cliente)
										);		

		return array(
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
	 * (maps to the xs:string XML schema type )
	 * @return string $texto total price
	 *(maps to the xs:string XML schema type )
	 */		
	function op__eco(toba_servicio_web_mensaje $mensaje) 
	{
		$xml = new SimpleXMLElement($mensaje->get_payload());
	    $texto = (string) $xml->texto;
		$payload = <<<XML
<ns1:eco xmlns:ns1="http://siu.edu.ar/toba_referencia/pruebas">
	<texto>$texto</texto>
</ns1:eco>
XML;
		return new toba_servicio_web_mensaje($payload);
	}	
	
	
}

?>