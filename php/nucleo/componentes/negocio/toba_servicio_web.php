<?php
/**
 * @package Componentes
 * @subpackage Negocio
 */
abstract class toba_servicio_web extends toba_componente
{
	protected static $opciones = array();	
	protected static $ini;
	protected static $mapeo_headers = array();

	final function __construct($id)
	{
		parent::__construct($id);
		// Cargo las dependencias
		foreach( $this->_lista_dependencias as $dep){
			$this->cargar_dependencia($dep);
			$this->_dependencias[$dep]->set_controlador($this, $dep);
			$this->dep($dep)->inicializar();
		}		
		
		$this->inicializar();
	}

	function get_opciones()
	{
		return array();
	}
	
	/**
	 * @ignore Metodo interno que llama la solicitud web para 
	 */
	public static function _get_opciones($id, $clase)
	{
		if (! isset(self::$ini)) {
			$proyecto = toba::proyecto()->get_id();
			$directorio = toba_instancia::get_path_instalacion_proyecto($proyecto). "/servicios_serv/".$id;		//Directorio perteneciente al servicio
			if (file_exists($directorio.'/clientes.ini')) {
				self::$ini = new toba_ini($directorio.'/clientes.ini');
			}
		}
		if (isset(self::$ini)) {
			chdir($directorio);
			if (self::$ini->existe_entrada('conexion')) {
				self::$opciones = self::$ini->get_datos_entrada('conexion');
			}
			if (self::$ini->existe_entrada('certificado')) {
				//Agrego los certificados manualmente
				if (! file_exists(self::$ini->get("certificado", "cert_cliente"))) {
					throw new toba_error("El archivo ".self::$ini->get("certificado", "cert_cliente")." no existe");
				}
				$certificado_cliente = ws_get_cert_from_file(self::$ini->get("certificado", "cert_cliente"));
				if (! file_exists(self::$ini->get("certificado", "clave_server"))) {
					throw new toba_error("El archivo ".self::$ini->get("certificado", "clave_server")." no existe");
				}				
				$clave_privada = ws_get_cert_from_file(self::$ini->get("certificado", "clave_server"));
				$seguridad = array("encrypt" => true,
												"algorithmSuite" => "Basic256Rsa15",
												"securityTokenReference" => "IssuerSerial");
				$policy = new WSPolicy(array("security"=> $seguridad));
				$security = new WSSecurityToken(array(
							"privateKey" => $clave_privada,
							"receiverCertificate" => $certificado_cliente)
				);
				self::$opciones['policy'] = $policy;
				self::$opciones['securityToken'] = $security;
			}
			//Averiguo los headers definidos
			foreach (self::$ini->get_extradas() as $entrada => $valor) {
				if (strpos($entrada, '=')) {
					if (file_exists($valor['archivo'])) {
						self::agregar_mapeo_headers($entrada, realpath($valor['archivo']));
					} else {
						throw new toba_error("El archivo {$valor['archivo']} no existe");
					}
				}
			}
		}
		self::$opciones = array_merge(self::$opciones, call_user_func(array($clase, 'get_opciones')));		
		return self::$opciones;
	}	
	
	/**
	 * Rutea WSF hacia la extensión
	 */
	function __call($nombre, $argumentos)
	{
		$metodo = substr($nombre, 1);
		$mensaje_entrada = new toba_servicio_web_mensaje($argumentos[0]);
		
		//Aca puedo obtener los headers para el metodo y tambien verificar que la firma es correcta
		$headers = $this->obtener_headers($mensaje_entrada);
		if ($this->servicio_con_firma()) {
			$this->verificar_firma($headers, $mensaje_entrada->get_payload());		
		}
		
		$mensaje_salida = $this->$metodo($mensaje_entrada, $headers);
		if (isset($mensaje_salida)) {
			return $mensaje_salida->wsf();
		} else {
			return;
		}
	}
	
	/**
	 * Devuelve un arreglo con los datos de los headers
	 * @param WSMessage $mensaje
	 * @return array
	 */
	function obtener_headers($mensaje)
	{
		$headers = array();
		$datos = $mensaje->wsf()->outputHeaders;
		if (isset($datos)) {
			foreach($datos as $encabezado) {		
				$pila[] = simplexml_load_string($encabezado->str);
			}
			while(! empty($pila)) {
				$elemento = array_shift($pila);
				foreach($elemento->children() as $hijo) {
					$pila[] = $hijo;				
				}			
				if ($elemento->count() == 0) {						//Si es una hoja obtengo el valor							
					$name = $elemento->getName();
					$value = (string) $elemento;				
					$headers[$name] = $value;
				}
			}
		}
		return $headers;
	}
	
	/**
	 * Verifica la firma del mensaje completo
	 * @param array $headers
	 * @param string $contenido_mensaje 
	 */
	protected function verificar_firma($headers, $contenido_mensaje )
	{
		//Recuperar la firma calculada en el cliente
		if (! empty($headers) && isset($headers['firma'] )) {
			$firma_original = base64_decode($headers['firma']);
			$extra_headers = array('firma', 'Security', 'Action', 'MessageID', 'To');
			foreach (array_keys($headers) as $id) {
				if (in_array($id, $extra_headers)) {
					unset($headers[$id]);
				}
			}
			$data = trim($contenido_mensaje. implode('',$headers));
			
			//Busco la clave publica
			$nombre = array();
			ksort($headers);
			foreach ($headers as $id => $valor) {
				$nombre[] = $id.'='.$valor;
			}
			$nombre = implode(',', $nombre);

			if (! isset(self::$mapeo_headers[$nombre])) {
				throw new toba_error("El mensaje no contiene headers definidos ('$nombre' no existe)");
			}
			$archivo = self::$mapeo_headers[$nombre];
			
			//Ahora verifico la firma
			$pub_key_id = openssl_pkey_get_public('file://'.$archivo);
			if ($pub_key_id === false) {
				throw new toba_error("No fue posible obtener una clave publica del archivo $pub_key_id");
			}
			toba::logger()->debug("Utilizando clave publica file://$archivo");
			if (openssl_verify($data, $firma_original, $pub_key_id) != 1) {
				throw new toba_error('El mensaje no es válido o no fue posible procesar su firma correctamente');
			}
		} else {
			throw new toba_error('El mensaje no viene firmado, se anula el pedido');
		}
	}	
	
	protected function servicio_con_firma()
	{
		if (! empty(self::$mapeo_headers)) {
			return true;
		} 
		return isset(self::$opciones['firmado']) ? self::$opciones['firmado'] : false;
	}		

	static function agregar_mapeo_headers($header, $archivo) {
		self::$mapeo_headers[$header] = $archivo;
	}
}
?>