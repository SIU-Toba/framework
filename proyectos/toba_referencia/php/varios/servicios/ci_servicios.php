<?php
class ci_servicios extends toba_ci
{
	protected $s__echo;
	protected $s__adjunto;
	protected $adjunto_respuesta;
	
	//-----------------------------------------------------------------------------
	//---- Eco --------------------------------------------------------------------
	//------------------------------------------------------------------------------

	function conf__form_echo(toba_ei_formulario $form)
	{
		if (isset($this->s__echo)) {
			$form->set_datos($this->s__echo);
		}
	}

	function evt__form_echo__enviar($datos)
	{
		$this->s__echo = $datos;

		$payload = <<<XML
<ns1:eco xmlns:ns1="http://siu.edu.ar/toba_referencia/pruebas">
	<texto>{$datos['texto']}</texto>
</ns1:eco>
XML;
		
	    // Set up security options
	    $security_options = array("useUsernameToken" => TRUE );
	    $policy = new WSPolicy(array("security" => $security_options));
	    $security_token = new WSSecurityToken(array("user" => "toba",
	                                                "password" => "toba"));
   
	    // Create client with options
    	$opciones = array("policy" => $policy, "securityToken" => $security_token);
		$servicio = toba::servicio_web('pruebas', $opciones);
		$respuesta = $servicio->request($payload);
		toba::notificacion()->info($respuesta->str);
	}

	//-----------------------------------------------------------------------------
	//---- Adjuntos ----------------------------------------------------------------
	//------------------------------------------------------------------------------	
	
	function conf__form_adjunto(toba_ei_formulario $form)
	{
		if (isset($this->s__adjunto)) {
			$datos = array();
			$datos['adjunto'] = $this->s__adjunto['archivo'];
			$datos['texto'] = $this->s__adjunto['texto'];
			$img = toba::proyecto()->get_www_temp($this->s__adjunto['archivo']);			
			$datos['imagen_enviada'] = "<img src='{$img['url']}' />";
			if (isset($this->adjunto_respuesta)) {			
				$datos['imagen_recibida'] = "<img src='{$this->adjunto_respuesta['url']}' />";
			}
			$form->set_datos($datos);
		}
	}

	function evt__form_adjunto__enviar($datos)
	{
		//--1 -Guarda el archivo en un temporal
		if (isset($datos['adjunto'])) {
			$this->s__adjunto['archivo'] = $datos['adjunto']['name'];
			$img = toba::proyecto()->get_www_temp($this->s__adjunto['archivo']);
			// Mover los archivos subidos al servidor del directorio temporal PHP a uno propio.
			move_uploaded_file($datos['adjunto']['tmp_name'], $img['path']);
		}
		$this->s__adjunto['texto'] = $datos['texto'];
		
		//--2 -Arma el mensaje 
		$payload = <<<XML
<ns1:upload xmlns:ns1="http://siu.edu.ar/toba_referencia/pruebas">
	<texto>{$this->s__adjunto['texto']}</texto>
    <ns1:fileName>{$this->s__adjunto['archivo']}</ns1:fileName>
	<ns1:image xmlmime:contentType="image/png" xmlns:xmlmime="http://www.w3.org/2004/06/xmlmime">
    	<xop:Include xmlns:xop="http://www.w3.org/2004/08/xop/include" href="cid:myid1"></xop:Include>
	</ns1:image>
</ns1:upload>
XML;
		//--3 -Pide el servicio
	    $servicio = toba::servicio_web('pruebas');	
		$imagen = toba::proyecto()->get_www_temp($this->s__adjunto['archivo']);
	    $imagen_contenido = file_get_contents($imagen['path']);
	    $opciones = array('attachments' => array('myid1' => $imagen_contenido));
		$respuesta = $servicio->request($payload, $opciones);
		
		
		//--4 - Guarda la respuesta en un temporal
		$this->adjunto_respuesta = toba::proyecto()->get_www_temp("salida.png");
		file_put_contents($this->adjunto_respuesta['path'], current($respuesta->attachments));
	}	
	
}

?>
