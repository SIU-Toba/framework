<?php
class ci_cliente extends toba_ci
{
	protected $s__echo;
	protected $s__datos_password;
	protected $s__adjunto;
	protected $adjunto_respuesta;
	protected $datos_persona;
	protected $path_servicio = "certificado/servicio.php";
	
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
	
	function evt__form__enviar($datos)
	{
		//--1- Arma el mensaje		
		$this->s__echo = $datos;
		$payload = <<<XML
<ns1:eco xmlns:ns1="http://siu.edu.ar/toba_referencia/serv_pruebas">
	<texto>{$datos['clave']}{$datos['valor']}</texto>
</ns1:eco>
XML;
		$opciones = array('action' => 'http://siu.edu.ar/toba_referencia/serv_pruebas/eco');
		$mensaje = new toba_servicio_web_mensaje($payload, $opciones);
		
		
		//--2- Arma el servicio indicando certificado del server y clave privada del cliente
		$carpeta = dirname(__FILE__);
		$cert_server = ws_get_cert_from_file($carpeta.'/servidor.crt');
		$clave_privada = ws_get_key_from_file($carpeta."/cliente.pkey");
		$cert_cliente = ws_get_cert_from_file($carpeta."/cliente.crt");
    
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
    	    		'to' => 'http://localhost/'.toba_recurso::url_proyecto().'/servicios.php/serv_certificado',    	
    				'policy' => $policy, 
    				'securityToken' => $security_token
    	);		
		$servicio = toba::servicio_web('certificado', $opciones);
	
		//-- 3 - Muestra la respuesta		
		$respuesta = $servicio->request($mensaje);
		toba::notificacion()->info($respuesta->get_payload());		
	}
		
	
	//-----------------------------------------------------------------------------
	//---- Utilidades  -----------------------------------------------------------
	//------------------------------------------------------------------------------
	
	function post_configurar()
	{
		parent::post_configurar();
		$img = toba_recurso::imagen_toba('nucleo/php.gif', true);
		$cliente = 'servicios/certificado/ci_cliente.php';
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