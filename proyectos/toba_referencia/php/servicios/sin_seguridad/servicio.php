<?php
class serv_sin_seguridad extends toba_servicio_web
{
	static function get_opciones()
	{
		return array(
			'seguro'			=> false,		//Explicitamente se hace publico el servicio
			'requestXOP'		=> true,
			'useMTOM'			=> true,
			'actions' => array(
							"http://siu.edu.ar/toba_referencia/serv_pruebas/aplanar_array"			=> "aplanar_array",
							"http://siu.edu.ar/toba_referencia/serv_pruebas/persona_alta"			=> "persona_alta",
							"http://siu.edu.ar/toba_referencia/serv_pruebas/persona_set_deportes"	=> "persona_set_deportes",		
							"http://siu.edu.ar/toba_referencia/serv_pruebas/persona_set_juegos"		=> "persona_set_juegos",		
							"http://siu.edu.ar/toba_referencia/serv_pruebas/enviar_excepcion"		=> "enviar_excepcion",
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
	function op__test(toba_servicio_web_mensaje $mensaje)
	{
		$xml = new SimpleXMLElement($mensaje->get_payload());
		$texto = xml_encode((string) $xml->texto);
		$payload = <<<XML
<ns1:eco xmlns:ns1="http://siu.edu.ar/toba_referencia/pruebas">
	<texto>Respuesta: $texto</texto>
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
	
	
	function op__enviar_excepcion(toba_servicio_web_mensaje $mensaje)
	{
		$datos = $mensaje->get_array();
		throw new toba_error_servicio_web($datos['mensaje'], $datos['codigo'], "Excepcion de prueba enviada");
	}

}

?>