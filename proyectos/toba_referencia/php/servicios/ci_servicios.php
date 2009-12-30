<?php
class ci_servicios extends toba_ci
{
	protected $s__echo;
	protected $s__datos_password;
	protected $s__adjunto;
	protected $adjunto_respuesta;
	protected $path_servicio;
	protected $datos_persona;
	
	function ini()
	{
		if (! extension_loaded('wsf')) {
			toba::notificacion()->error("No se encuentra instalada la extensión wsf de php.".
			" <a href='http://toba.siu.edu.ar/trac/toba/wiki/Referencia/ServiciosWeb'>Ver documentación</a>");
		}
	}
	
	//-----------------------------------------------------------------------------
	//---- Eco --------------------------------------------------------------------
	//------------------------------------------------------------------------------

	function conf__form_echo(toba_ei_formulario $form)
	{
		$this->path_servicio = 'varios/servicios/serv_sin_seguridad.php';
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
		$opciones = array(
			'to' => 'http://localhost/'.toba_recurso::url_proyecto().'/servicios.php/serv_sin_seguridad'
		);
		$servicio = toba::servicio_web('sin_seguridad', $opciones);
		$respuesta = $servicio->request(new toba_servicio_web_mensaje($payload));
		toba::notificacion()->info($respuesta->get_payload());
	}

	//-----------------------------------------------------------------------------
	//---- Adjuntos ----------------------------------------------------------------
	//------------------------------------------------------------------------------	
	
	function conf__form_adjunto(toba_ei_formulario $form)
	{
		$this->path_servicio = 'varios/servicios/serv_sin_seguridad.php';		
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
		$opciones = array(
			'to' => 'http://localhost/'.toba_recurso::url_proyecto().'/servicios.php/serv_sin_seguridad'
		);		
	    $servicio = toba::servicio_web('sin_seguridad', $opciones);	
		$imagen = toba::proyecto()->get_www_temp($this->s__adjunto['archivo']);
	    $imagen_contenido = file_get_contents($imagen['path']);
	    $opciones = array('attachments' => array('myid1' => $imagen_contenido));
	    $mensaje = new toba_servicio_web_mensaje($payload, $opciones);
		$respuesta = $servicio->request($mensaje);
		
		//--4 - Guarda la respuesta en un temporal
		$this->adjunto_respuesta = toba::proyecto()->get_www_temp('salida.png');
		file_put_contents($this->adjunto_respuesta['path'], current($respuesta->wsf()->attachments));
	}	

	
	//-----------------------------------------------------------------------------
	//---- Envio/Recepción de datos usando seguridad con password ------------------
	//------------------------------------------------------------------------------

	function conf__form_datos_password(toba_ei_formulario $form)
	{
		$this->path_servicio = 'varios/servicios/serv_password.php';		
		if (isset($this->s__datos_password)) {
			$form->set_datos($this->s__datos_password);
		}
	}

	function evt__form_datos_password__enviar($datos)
	{
		//--1- Arma una arreglo multi-dimensional
		$this->s__datos_password = $datos;
		$arreglo = array();
		for ($i = $datos['dimensiones']; $i > 0 ; $i--) {
			$arreglo = array('valor' => "Valor $i", 'hijo' => $arreglo);
			if (empty($arreglo['hijo'])) {
				unset($arreglo['hijo']);
			}
		}
	
		//--2- Opciones de seguridad
	    $policy = new WSPolicy(array('security' => array('useUsernameToken' => true)));
	    $security_token = new WSSecurityToken(array('user' => $this->s__datos_password['usuario'],
	                                                'password' => $this->s__datos_password['password']));
    	$opciones = array(
    					'to' => 'http://localhost/'.toba_recurso::url_proyecto().'/servicios.php/serv_password', 
    					'policy' => $policy, 
    					'securityToken' => $security_token
    	);
    	
    	//--3- Construye el cliente
		$servicio = toba::servicio_web('seguridad_password', $opciones);
		
		//--4- Hace un request a la acción específica enviando el arreglo
		$opciones = array('action' => 'http://siu.edu.ar/toba_referencia/serv_pruebas/aplanar_array');
		$mensaje = new toba_servicio_web_mensaje($arreglo, $opciones);
		$respuesta = $servicio->request($mensaje);
		
		//--5- Mostramos lo enviados y el resultado
		$arreglo_resultado = $respuesta->get_array();
		$this->s__datos_password['arreglo'] = $this->formatear_valor(var_export($arreglo, true));
		$this->s__datos_password['payload'] = $this->formatear_valor($mensaje->get_payload());
		$this->s__datos_password['respuesta_payload'] = $this->formatear_valor($respuesta->get_payload());
		$this->s__datos_password['respuesta_arreglo'] = $this->formatear_valor(var_export($arreglo_resultado, true));
	}


	
	
	//--------------------------------------------------------------
	//---- Encriptacion y firmado del mensaje     ------------------
	//--------------------------------------------------------------

	function conf__form_echo_seguro(toba_ei_formulario $form)
	{
		$this->path_servicio = 'varios/servicios/serv_encriptado_firmado.php';		
		if (isset($this->s__echo)) {
			$form->set_datos($this->s__echo);
		}
	}

	function evt__form_echo_seguro__enviar($datos)
	{
		//--1- Arma el mensaje		
		$this->s__echo = $datos;
		$payload = <<<XML
<ns1:eco xmlns:ns1="http://siu.edu.ar/toba_referencia/serv_pruebas">
	<texto>{$datos['texto']}</texto>
</ns1:eco>
XML;
		$opciones = array('action' => 'http://siu.edu.ar/toba_referencia/serv_pruebas/eco');
		$mensaje = new toba_servicio_web_mensaje($payload, $opciones);
		
		
		//--2- Arma el servicio indicando certificado del server y clave privada del cliente
		$carpeta = dirname(__FILE__);
		$cert_server = ws_get_cert_from_file($carpeta.'/cert_server.cert');
		$clave_privada = ws_get_key_from_file($carpeta."/clave_cliente.pem");
		$cert_cliente = ws_get_cert_from_file($carpeta."/cert_cliente.cert");
    
		$seguridad = array("encrypt" => true,
                       "algorithmSuite" => "Basic256Rsa15",
                       "securityTokenReference" => "IssuerSerial");
    
		$policy = new WSPolicy(array("security" => $seguridad));
		$security_token = new WSSecurityToken(array("privateKey" => $clave_privada,	//Encriptación
											"receiverCertificate" => $cert_server,	//Encriptación
											"certificate" 		=> $cert_cliente,	//Firmado
											)
						);		
    	$opciones = array(
    	    		'to' => 'http://localhost/'.toba_recurso::url_proyecto().'/servicios.php/serv_encriptado_firmado',    	
    				'policy' => $policy, 
    				'securityToken' => $security_token
    	);		
		$servicio = toba::servicio_web('encriptado_firmado', $opciones);
	
		//-- 3 - Muestra la respuesta		
		$respuesta = $servicio->request($mensaje);
		toba::notificacion()->info($respuesta->get_payload());		
	}
		
	//--------------------------------------------------------------
	//---- Secuencia de mensajes     -------------------------------
	//--------------------------------------------------------------	
	
	function conf__form_secuencia(toba_ei_formulario $form)
	{
		$this->path_servicio = 'varios/servicios/serv_password.php';
		if (isset($this->datos_persona)) {
			$form->set_datos($this->datos_persona);
		}
	}
	
	function evt__form_secuencia__enviar($datos)
	{
		$this->datos_persona = $datos;
		
		//--1- Construye el cliente usando seguridad por password
	    $policy = new WSPolicy(array('security' => array('useUsernameToken' => true)));
	    $security_token = new WSSecurityToken(array('user' => 'toba', 'password' => 'toba'));
    	$opciones = array(
    		    		'to' => 'http://localhost/'.toba_recurso::url_proyecto().'/servicios.php/serv_password',    	
    					'policy' => $policy, 
    					'securityToken' => $security_token
    	);
    	$servicio = toba::servicio_web('seguridad_password', $opciones);
    	
    	//--2- Da de alta la persona
    	$opciones = array('action' => 'http://siu.edu.ar/toba_referencia/serv_pruebas/persona_alta');
    	$mensaje = new toba_servicio_web_mensaje($this->datos_persona, $opciones);
    	$respuesta = $servicio->request($mensaje);
		$id_rs = $respuesta->get_array();
    	$this->datos_persona['id'] = $id_rs['id'];		

    	//--3- Le setea los juegos (utiliza send en lugar de request ya que no espera respuesta)
    	$opciones = array('action' => 'http://siu.edu.ar/toba_referencia/serv_pruebas/persona_set_juegos');
    	$mensaje = new toba_servicio_web_mensaje($this->datos_persona, $opciones);
    	$servicio->send($mensaje);    

    	//--4- Le setea los deportes (utiliza send en lugar de request ya que no espera respuesta)
    	$opciones = array('action' => 'http://siu.edu.ar/toba_referencia/serv_pruebas/persona_set_deportes',
    						'lastMessage' => true);
    	$mensaje = new toba_servicio_web_mensaje($this->datos_persona, $opciones);
    	$servicio->send($mensaje);   	
    	
    	//--5- Muestra la respuesta
    	toba::notificacion()->info("Creada persona número ".$this->datos_persona['id']);    	
	}	
		
	
	//-----------------------------------------------------------------------------
	//---- Utilidades  -----------------------------------------------------------
	//------------------------------------------------------------------------------
	
	function post_configurar()
	{
		parent::post_configurar();
		$img = toba_recurso::imagen_toba('nucleo/php.gif', true);
		$cliente = 'varios/servicios/ci_servicios.php';
		$url_cliente = toba::vinculador()->get_url('toba_editor', '30000014', array('archivo' => $cliente), array('prefijo'=>toba_editor::get_punto_acceso_editor()));		
		$url_servicio = toba::vinculador()->get_url('toba_editor', '30000014', array('archivo' => $this->path_servicio), array('prefijo'=>toba_editor::get_punto_acceso_editor()));
		$html = "<div style='float:right'><a target='logger' href='$url_cliente'>$img Ver .php del Cliente</a>";
		$html .= "<br><a target='logger' href='$url_servicio'>$img Ver .php del Servicio</a>";
		$url_ejemplos = 'http://labs.wso2.org/wsf/php/demo.php?name=Samples&demo=samples/index.html&src=samples';
		$html .= "<br>Ejemplos completos de WSF <a href='$url_ejemplos'>online</a></div>";
		$html .= $this->pantalla()->get_descripcion();		
		$this->pantalla()->set_descripcion($html);
	}
	
	function formatear_valor($valor)
	{
		$estilo = 'style="background-color: white; border: 1px solid gray; padding: 5px;"';		
		return  "<pre $estilo>".htmlentities($valor).'</pre>';
	}
	
	
		
	
}

?>
