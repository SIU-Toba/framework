<?php
class serv_password extends toba_servicio_web
{
	
	function get_opciones()
	{
		$policy = new WSPolicy(array(
								"security" => array(
									"useUsernameToken" => true,
									"includeTimestamp" => true
									)	
								)
							);
		$security = new WSSecurityToken(array(
											"user" => "toba",
											"password" => "toba",
											"ttl" => 300
											)
										);		

		return array(
            "policy" 			=> $policy,
            "securityToken"		=> $security,
             'actions' => array(
					"http://siu.edu.ar/toba_referencia/serv_pruebas/eco"		=> "eco",
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
	function op__eco(toba_servicio_web_mensaje $mensaje) {
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

}

?>