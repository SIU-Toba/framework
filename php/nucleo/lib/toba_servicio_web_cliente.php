<?php

class toba_servicio_web_cliente
{
	protected $wsf;
	protected $opciones;
	protected $id_servicio;
	
	/**
	 * @return toba_servicios_web
	 */
	static function conectar($id_servicio, $opciones=array())
	{
		$info = toba::proyecto()->get_info_servicios_web_acc($id_servicio);
		$opciones_ini = $info['parametros'];
		
		$proyecto = toba::proyecto()->get_id();
		$directorio = toba_instancia::get_path_instalacion_proyecto($proyecto). "/servicios_cli/$id_servicio";		//Directorio perteneciente al servicio
		if (file_exists($directorio.'/servicio.ini')) {
			$ini = new toba_ini($directorio.'/servicio.ini');
			if ($ini->existe_entrada('conexion')) {
				$opciones_ini = array_merge($opciones_ini, $ini->get_datos_entrada('conexion'));
			}
		}					

		//--2- Arma el servicio indicando certificado del server y clave privada del cliente
		$security_token = self::get_ws_token(toba::proyecto()->get_id(), $id_servicio);
		if (isset($security_token)) {
			$seguridad = array(
					"sign" => true,
					"encrypt" => true,
					"algorithmSuite" => "Basic256Rsa15",
					"securityTokenReference" => "IssuerSerial");
			
			$policy = new WSPolicy(array("security" => $seguridad));
			$opciones_ini['policy'] = $policy;
			$opciones_ini['securityToken'] = $security_token;
		}
		
		//Convierte todos los '1' de texto en true
		foreach (array_keys($opciones_ini) as $id_opcion) {
			if ($opciones_ini[$id_opcion] === '1' || $opciones_ini[$id_opcion] === 1) {
				$opciones_ini[$id_opcion] = true;
			}
		}		

		//-- Mezcla con las opciones recibidas y crea el objeto
		$opciones = array_merge($opciones_ini, $opciones);
		if (! isset($opciones['to'])) {
			throw new toba_error_def("Debe indicar la URL destino en el campo 'to'");			
		}		
		if (isset($opciones['firmado']) && $opciones['firmado'] && ! isset($seguridad)) {
			throw new toba_error("El servicio web esta configurado para requerir firma, sin embargo no se esta encriptando/firmando la conexion. 
					Chequee la documentación para configurar los certificados");
		}
		toba::logger()->debug("Invocando servicio $id_servicio. Opciones:<br>". var_export($opciones, true));
		$servicio = new toba_servicio_web_cliente($opciones, $id_servicio);
		return $servicio;
	}
	
	function __construct($opciones, $id_servicio) 
	{
		$this->opciones = $opciones;
		$this->id_servicio = $id_servicio;
		$this->wsf = new WSClient($this->opciones);
	}
	
	/**
	 * @return WSClient
	 */
	function wsf()
	{
		return $this->wsf;	
	}
	
	/**
	 * Envia un mensaje al servicio web y espera la respuesta
	 * @param toba_servicio_web_mensaje $mensaje
	 * @return toba_servicio_web_mensaje
	 */
	function request(toba_servicio_web_mensaje $mensaje)
	{
		try {
			$message = $this->wsf->request($mensaje->wsf());
			return new toba_servicio_web_mensaje($message);
		} catch (WSFault $fault) {
			throw new toba_error_comunicacion($fault->__toString(), $this->opciones, $this->wsf->getLastResponseHeaders());
		} catch (Exception $e) {
			throw new toba_error_comunicacion($e->getMessage(), $this->opciones, $this->wsf->getLastResponseHeaders());			
		}
	}
	
	function send(toba_servicio_web_mensaje $mensaje)
	{
		try {
			$this->wsf->send($mensaje->wsf());
		} catch (WSFault $fault) {
			throw new toba_error_comunicacion($fault->__toString(), $this->opciones, $this->wsf->getLastResponseHeaders());
		} catch (Exception $e) {
			throw new toba_error_comunicacion($e->getMessage(), $this->opciones, $this->wsf->getLastResponseHeaders());			
		}
	}
	
	/**
	 * Devuelve un objeto WSF con la configuracion de certificados ssl existente o null
	 * @param string $proyecto
	 * @param string $servicio
	 * @return WSSecurityToken 
	 */
	static function get_ws_token($proyecto, $servicio)
	{
		$security_token = null;
		$directorio = toba_instancia::get_path_instalacion_proyecto($proyecto). "/servicios_cli/$servicio";		//Directorio perteneciente al servicio
		$ini_conf = new toba_ini($directorio. "/servicio.ini");

		//Busco los datos para los certificados en el archivo perteneciente al servicio
		if (! is_null($ini_conf) && $ini_conf->existe_entrada('certificado')) {
			chdir($directorio);
			$config = $ini_conf->get_datos_entrada('certificado');
			
			//Cargo las claves y armo el objeto WSF
			if (! file_exists($config['clave_cliente'])) {
				throw new toba_error("El archivo ".$config['clave_cliente']." no existe");
			}			
			$clave_cliente = ws_get_key_from_file($config['clave_cliente']);
			
			if (! file_exists($config['cert_cliente'])) {
				throw new toba_error("El archivo ".$config['cert_cliente']." no existe");
			}			
			$cert_cliente = ws_get_cert_from_file($config['cert_cliente']);
			
			if (! file_exists($config['cert_server'])) {
				throw new toba_error("El archivo ".$config['cert_server']." no existe");
			}			
			$cert_server = ws_get_cert_from_file($config['cert_server']);
					
			$security_token = new WSSecurityToken(array("privateKey" => $clave_cliente,		
												"receiverCertificate" => $cert_server,	
												"certificate" 		=> $cert_cliente	
												)
						);		
		}
		return $security_token;
	}
}
?>