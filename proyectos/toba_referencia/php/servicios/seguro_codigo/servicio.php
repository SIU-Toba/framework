<?php

class servicio extends toba_servicio_web
{
	function inicializar($parametros=array())
	{
		//Agrego la clave publica dinamicamente
		$carpeta = dirname(__FILE__);
		
		//Asocia un ID (agronomia=..) a un certificado de cliente dado 
		$this->agregar_mapeo_firmas($carpeta.'/cliente.crt', 
								array(
									'dependencia' => 'agronomia',
									'otro' => 'test'
								) 
							);
	}

	static function get_opciones()
	{
		$carpeta = dirname(__FILE__);
		
		//Agrego los certificados manualmente
		$cert_cliente = ws_get_cert_from_file($carpeta."/cliente.crt");
		$cert_server = ws_get_cert_from_file($carpeta.'/servidor.crt');
		$clave_privada = ws_get_cert_from_file($carpeta."/servidor.pkey");
		$seguridad = array(
					"sign" => true,
					"encrypt" => true,
					"algorithmSuite" => "Basic256Rsa15",
					"securityTokenReference" => "IssuerSerial");

		$policy = new WSPolicy(array("security"=> $seguridad));
		$security = new WSSecurityToken(array(
											"privateKey" => $clave_privada,
											"certificate" => $cert_server)
		);

		return array(
			"seguro"			=> true,			//Esta opción fuerza a que el mensaje tiene que estar firmado/encriptado
            "policy" 			=> $policy,
            "securityToken"		=> $security,
             'actions' => array(
					"http://siu.edu.ar/toba_referencia/serv_pruebas/test" => "test",
			),
		);
	}


	/**
	 * Responde exactamente con la misma cadena enviada
	 * @param string $texto texto a repetir
	 * @return string $texto texto repetido
	 */
	function op__test(toba_servicio_web_mensaje $mensaje)
	{
		$xml = new SimpleXMLElement($mensaje->get_payload());
		$texto = xml_encode(xml_decode($xml->texto));
		$dependencia = xml_encode($this->get_id_cliente('dependencia'));		
		$payload = <<<XML
<ns1:test xmlns:ns1="http://siu.edu.ar/toba_referencia/pruebas">
	<texto>Texto: $texto 
	Dependencia: $dependencia</texto>
</ns1:test>
XML;
		return new toba_servicio_web_mensaje($payload);
	}

}

?>