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
	function request(toba_servicio_web_mensaje $mensaje, $usar_configuraciones = true)
	{
		try {
			//Antes de hacer el request, mando a firmar el mensaje con la clave privada.
			if ($usar_configuraciones) {
				$this->configurar($mensaje);
			}
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

	/**.
	 *  Dispara el firmado/encriptacion del mensaje en base a la configuracion actual 
	 *  @ignore
	 */
	protected function configurar(toba_servicio_web_mensaje $mensaje)
	{
		$clave = null;
		$id_servicio = $this->id_servicio;
		$proyecto = toba::proyecto()->get_id();
		$directorio = toba_instancia::get_path_instalacion_proyecto($proyecto). "/servicios_cli/$id_servicio";		//Directorio perteneciente al servicio
		if (! file_exists($directorio) || ! file_exists($directorio . '/servicio.ini')) {
			//No hay configuracion, no se firma ni encripta
			return;
		}
		$ini_conf = new toba_ini($directorio . '/servicio.ini');
		if ($ini_conf->existe_entrada('headers')) {
			$headers = $ini_conf->get('headers');
			ksort($headers);
			$mensaje->set_headers($headers);
		}
		if ($ini_conf->existe_entrada('RSA')) {
			$clave = $directorio. '/' . $ini_conf->get('RSA', 'privada');			
			$mensaje->firmar_mensaje($clave);
		}
	}

	/**
	 * Obtiene los headers por defecto que se deben agregar al mensaje
	 * Los mismos estan fijados en el archivo de configuracion para el servicio
	 * @return array(WSHeader) 
	 */
	protected function get_headers_configuracion()
	{
		$headers = array();
		$id_servicio = $this->id_servicio;
		$proyecto = toba::proyecto()->get_id();
		$directorio = toba_instancia::get_path_instalacion_proyecto($proyecto). "/servicios/$id_servicio";		//Directorio perteneciente al servicio
		
		//Obtener el archivo con la configuracion de headers
		$archivo = $directorio . '/headers.ini';
		if (toba_manejador_archivos::existe_archivo_en_path($archivo)) {
			$ini_headers = new toba_ini($archivo);
			$datos = $ini_headers->get('headers', null, null);
			if (! is_null($datos)) {									//Agregar un headers por cada valor disponible
				foreach($datos as $klave => $valor) {
					$headers[] = new WSHeader(array('name' => $klave, 'data' => $valor));					
				}
			}
		}
		return $headers;
	}

	/**
	 * Devuelve la ruta al archivo que contiene la clave publica
	 * @return string 
	 */
	function get_clave_publica()
	{
		$clave = null;
		$id_servicio = $this->id_servicio;
		$proyecto = toba::proyecto()->get_id();
		$directorio = toba_instancia::get_path_instalacion_proyecto($proyecto). "/servicios/$id_servicio";		//Directorio perteneciente al servicio

		$ini_conf = new toba_ini($directorio . '/servicio.ini');
		if (! is_null($ini_conf) &&  $ini_conf->existe_entrada('RSA')) {
			$aux = $ini_conf->get_datos_entrada('RSA');			
			$clave = $directorio .  '/' . $aux['public'];
		}		
		return $clave;
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
		$directorio = toba_instancia::get_path_instalacion_proyecto($proyecto). "/servicios/$servicio";		//Directorio perteneciente al servicio
		$ini_conf = new toba_ini($directorio. "/servicio.ini");
		//Busco los datos para los certificados en el archivo perteneciente al servicio
		if (! is_null($ini_conf) && $ini_conf->existe_entrada('cliente_certificado')) {
			$config = $ini_conf->get_datos_entrada('cliente_certificado');
			
			//Cargo las claves y armo el objeto WSF
			$clave_cliente = ws_get_key_from_file($directorio . '/'. $config['clave_cliente']);
			$cert_cliente = ws_get_cert_from_file($directorio . '/'.$config['cert_cliente']);
			$cert_server = ws_get_cert_from_file($directorio . '/'.$config['cert_server']);		
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