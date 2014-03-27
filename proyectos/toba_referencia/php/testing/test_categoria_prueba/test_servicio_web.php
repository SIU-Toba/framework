<?php

class test_servicio_web extends toba_test
{
	static function get_descripcion()
	{
		return "Test de servicios web";
	}
	
	function test_servicio_eco()
	{

		$mensaje = xml_encode("Holá Mundo");
		$payload = <<<XML
<ns1:eco xmlns:ns1="http://siu.edu.ar/toba_referencia/serv_pruebas">
	<texto>$mensaje</texto>
</ns1:eco>
XML;
		$opciones = array(
			'to' => 'http://localhost/'.toba_recurso::url_proyecto($this->get_proyecto()).'/servicios.php/serv_sin_seguridad'
		);
		$servicio = toba::servicio_web('cli_sin_seguridad', $opciones);
		$mensaje = $servicio->request(new toba_servicio_web_mensaje($payload));
		$xml = new SimpleXMLElement($mensaje->get_payload());
		$this->AssertEqual(xml_decode((string) $xml->texto), "Respuesta: Holá Mundo");
	}

}

?>