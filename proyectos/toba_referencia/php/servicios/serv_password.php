<?php
class serv_password extends toba_servicio_web
{
	
	function get_opciones()
	{						
		$seguridad = array("encrypt" => true,
					"algorithmSuite" => "Basic256Rsa15",
					"securityTokenReference" => "IssuerSerial");
 
		$policy = new WSPolicy(array("security"=> $seguridad));			
		/*$security = new WSSecurityToken(array(
											"user" => "toba",
											"password" => "toba",
											'passwordType' => 'Digest',
											"ttl" => 300
											)
										);		*/				
		$archivos_ssl = toba::instalacion()->get_configuracion_certificados_ssl();			//Exigido por WSF 2.1.0 con certificados
		if (empty($archivos_ssl)) {
			throw new toba_error_def('Falta informacion de certificados ssl, ejecute comando conf_ssl_server');
		}
		$certificado_cliente = ws_get_cert_from_file($archivos_ssl['cert_cliente']);
		$clave_privada = ws_get_cert_from_file($archivos_ssl['clave_server']);		
		$security = new WSSecurityToken(array(
											"privateKey" => $clave_privada,
											"receiverCertificate" => $certificado_cliente)
										);		
		
		return array(
			"reliable"			=> true,		
            "policy" 			=> $policy,
            "securityToken"		=> $security,
             'actions' => array(
					"http://siu.edu.ar/toba_referencia/serv_pruebas/aplanar_array"			=> "aplanar_array",
					"http://siu.edu.ar/toba_referencia/serv_pruebas/persona_alta"			=> "persona_alta",
					"http://siu.edu.ar/toba_referencia/serv_pruebas/persona_set_deportes"	=> "persona_set_deportes",		
					"http://siu.edu.ar/toba_referencia/serv_pruebas/persona_set_juegos"		=> "persona_set_juegos",		
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
	function op__aplanar_array(toba_servicio_web_mensaje $mensaje) 
	{
		//-1-- Toma el arreglo y extrae los numeros
		$arreglo = $mensaje->get_array();
		$salida = array();
		$continuar = true;
		$i = 0;
		do {
			$salida[] = $arreglo['valor'];
			if (isset($arreglo['hijo'])) {
				$arreglo = $arreglo['hijo'];
			} else {
				$continuar = false;
			}	
			$i++;		
		} while($continuar);
		

		//-2- Envia el arreglo resultante
	    return new toba_servicio_web_mensaje($salida);
	}
	
	
	function op__persona_alta(toba_servicio_web_mensaje $mensaje) 
	{
		//-- Inserta la persona
		$datos = $mensaje->get_array();
		$nombre = quote($datos['nombre']);
		$sql = "INSERT INTO ref_persona (nombre) VALUES ($nombre)";
		toba::db()->ejecutar($sql);
		$id = array('id' => toba::db()->recuperar_secuencia('ref_persona_id_seq'));
		toba::logger()->debug("Creada persona ".$id['id']);		
		$salida = new toba_servicio_web_mensaje($id);
		return $salida;
	}	
	
	function op__persona_set_deportes(toba_servicio_web_mensaje $mensaje) 
	{
		$datos = $mensaje->get_array();
		$sql = "INSERT INTO ref_persona_deportes(persona, deporte)
    				VALUES (:persona, :deporte)";
		$sentencia = toba::db()->sentencia_preparar($sql);		
		foreach ($datos['deportes'] as $deporte) {
			toba::db()->sentencia_ejecutar($sentencia, array('persona' => $datos['id'], 'deporte' => $deporte));
			toba::logger()->debug("Creada deporte $deporte para persona ".$datos['id']);			
		}
		return;
	}

	function op__persona_set_juegos(toba_servicio_web_mensaje $mensaje) 
	{
		$datos = $mensaje->get_array();
		foreach ($datos['juegos'] as $juego) {
			$sql = "INSERT INTO ref_persona_juegos(persona, juego)
	    				VALUES (:persona, :juego)";
			$sentencia = toba::db()->sentencia_preparar($sql);
			toba::db()->sentencia_ejecutar($sentencia, array('persona' => $datos['id'], 'juego' => $juego));
			toba::logger()->debug("Creada Juego $juego para persona ".$datos['id']);			
		}
		return;
	}	
}

?>