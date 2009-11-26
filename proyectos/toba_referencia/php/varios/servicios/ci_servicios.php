<?php
class ci_servicios extends toba_ci
{
	protected $s__echo;
	protected $s__datos_password;
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
<ns1:eco xmlns:ns1="http://siu.edu.ar/toba_referencia/serv_pruebas">
	<texto>{$datos['texto']}</texto>
</ns1:eco>
XML;
		$servicio = toba::servicio_web('sin_seguridad');
		$respuesta = $servicio->request(new toba_servicio_web_mensaje($payload));
		toba::notificacion()->info($respuesta->get_payload());
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
<ns1:upload xmlns:ns1="http://siu.edu.ar/toba_referencia/serv_pruebas">
	<texto>{$this->s__adjunto['texto']}</texto>
    <ns1:fileName>{$this->s__adjunto['archivo']}</ns1:fileName>
	<ns1:image xmlmime:contentType="image/png" xmlns:xmlmime="http://www.w3.org/2004/06/xmlmime">
    	<xop:Include xmlns:xop="http://www.w3.org/2004/08/xop/include" href="cid:myid1"></xop:Include>
	</ns1:image>
</ns1:upload>
XML;
		//--3 -Pide el servicio
	    $servicio = toba::servicio_web('sin_seguridad');	
		$imagen = toba::proyecto()->get_www_temp($this->s__adjunto['archivo']);
	    $imagen_contenido = file_get_contents($imagen['path']);
	    $opciones = array('attachments' => array('myid1' => $imagen_contenido));
	    $mensaje = new toba_servicio_web_mensaje($payload, $opciones);
		$respuesta = $servicio->request($mensaje);
		
		//--4 - Guarda la respuesta en un temporal
		$this->adjunto_respuesta = toba::proyecto()->get_www_temp("salida.png");
		file_put_contents($this->adjunto_respuesta['path'], current($respuesta->wsf()->attachments));
	}	

	
	//-----------------------------------------------------------------------------
	//---- Envio/Recepción de datos usando seguridad con password ------------------
	//------------------------------------------------------------------------------

	function conf__form_datos_password(toba_ei_formulario $form)
	{
		if (isset($this->s__datos_password)) {
			$form->set_datos($this->s__datos_password);
		}
	}

	function evt__form_datos_password__enviar($datos)
	{
		//--1- Arma una arreglo multi-dimensional
		$this->s__datos_password = $datos;
		$arreglo = array();
		for ($i = $datos['dimensiones']; $i >0 ; $i--) {
			$arreglo = array('valor' => "Valor $i", 'hijo' => $arreglo);
			if (empty($arreglo['hijo'])) {
				unset($arreglo['hijo']);
			}
		}
	
		//--2- Opciones de seguridad
	    $policy = new WSPolicy(array("security" => array("useUsernameToken" => TRUE)));
	    $security_token = new WSSecurityToken(array("user" => $this->s__datos_password['usuario'],
	                                                "password" => $this->s__datos_password['password']));
    	$opciones = array("policy" => $policy, "securityToken" => $security_token);
    	
    	//--3- Construye el cliente
		$servicio = toba::servicio_web('seguridad_password', $opciones);
		
		//--4- Hace un request a la acción específica enviando el arreglo
		$opciones = array('action' => 'http://siu.edu.ar/toba_referencia/serv_pruebas/eco');
		$mensaje = new toba_servicio_web_mensaje($arreglo, $opciones);
		$respuesta = $servicio->request($mensaje);
		
		//--5- Mostramos lo enviados y el resultado
		$arreglo_resultado = $respuesta->get_array();
		$this->s__datos_password['arreglo'] = $this->formatear_valor(var_export($arreglo, true));
		$this->s__datos_password['payload'] = $this->formatear_valor($mensaje->get_payload());
		$this->s__datos_password['respuesta_payload'] = $this->formatear_valor($respuesta->get_payload());
		$this->s__datos_password['respuesta_arreglo'] = $this->formatear_valor(var_export($arreglo_resultado, true));
	}
	
	function formatear_valor($valor)
	{
		$estilo = 'style="background-color:white; border: 1px solid gray; padding: 5px;"';		
		return  "<pre $estilo>".htmlentities($valor).'</pre>';
	}
	
}

?>
