<?php

class servicio extends toba_servicio_web
{
	/**
	 * Responde exactamente con la misma cadena enviada
	 * @param string $texto texto a repetir
	 * @return string $texto texto repetido
	 */
	function op__eco(toba_servicio_web_mensaje $mensaje, $headers)
	{
		$xml = new SimpleXMLElement($mensaje->get_payload());
		$texto = (string) $xml->texto;
		if (isset($headers['dependencia'])) {
			$dependencia = $headers['dependencia'];
		} else {
			$dependencia = "No presente";
		}		
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