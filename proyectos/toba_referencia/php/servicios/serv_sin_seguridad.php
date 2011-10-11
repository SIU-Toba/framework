<?php
class serv_sin_seguridad extends toba_servicio_web
{
	
	function get_opciones()
	{
		return array(
			'requestXOP'		=> true,
			'useMTOM'			=> true,
		);
	}

	/**
	 * Responde exactamente con la misma cadena enviada
	 * @param string $texto texto a repetir
	 * (maps to the xs:string XML schema type )
	 * @return string $texto total price
	 *(maps to the xs:string XML schema type )
	 */		
	function op__eco(toba_servicio_web_mensaje $mensaje, $headers) 
	{
		$xml = new SimpleXMLElement($mensaje->get_payload());
		$texto = (string) $xml->texto .' - ' .$headers['tag_name'];
		$payload = <<<XML
<ns1:eco xmlns:ns1="http://siu.edu.ar/toba_referencia/pruebas">
	<texto>$texto</texto>
</ns1:eco>
XML;
		return new toba_servicio_web_mensaje($payload);
	}

	/**
	 * Toma una imagen y un texto y los une y retorna
	 * @param string $texto Texto a insertar en la imagen
	 * (maps to the xs:string XML schema type )
	 */		
	function op__upload(toba_servicio_web_mensaje $mensaje) 
	{
		//--1- Controlar entrada
		if (count($mensaje->wsf()->attachments) == 0) {
			throw new WSFault("Sender", "No se encontro la imagen adjunta");
		}
		if (count($mensaje->wsf()->attachments) > 1) {
			throw new WSFault("Sender", "Sólo se acepta una única imagen como parámetro de entrada");
		}
		$xml = new SimpleXMLElement($mensaje->get_payload());
		$texto = (string) $xml->texto;

		//--2- Hacer un procesamiento a la imagen
		$imagen = imagecreatefromstring(current($mensaje->wsf()->attachments));		
		$textcolor = imagecolorallocate($imagen, 0, 0, 0);		
		imagestring($imagen, 5, 2, 2, $texto, $textcolor);
		ob_start();
		imagepng($imagen);		
		$salida = ob_get_contents();
		ob_end_clean();		

		//--3- Retorna la imagen
		$payload = <<<XML
<ns1:upload xmlns:ns1="http://siu.edu.ar/toba_referencia/pruebas">
    <ns1:fileName>salida.png</ns1:fileName>
    <ns1:image xmlmime:contentType="image/png" xmlns:xmlmime="http://www.w3.org/2004/06/xmlmime">
    	<xop:Include xmlns:xop="http://www.w3.org/2004/08/xop/include" href="cid:imagen"></xop:Include>
    </ns1:image>
</ns1:upload>
XML;
		$opciones = array('attachments' => array('imagen' => $salida));
		return new toba_servicio_web_mensaje($payload, $opciones);
	}
	
}

?>