<?php
/**
 * @package Componentes
 * @subpackage Negocio
 */
abstract class toba_servicio_web extends toba_componente
{
	protected static $opciones = array();	
	protected static $ini;
	protected static $mapeo_firmas = array();
	
	protected $mensaje_entrada;
	protected $headers_entrada;
	protected $id_cliente;
	
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
	 * @ignore Metodo interno que llama la solicitud web para obtener las configuraciones del servicio 
	 */
	public static function _get_opciones($id, $clase)
	{
		if (! isset(self::$ini)) {
			$proyecto = toba::proyecto()->get_id();
			$directorio = toba_instancia::get_path_instalacion_proyecto($proyecto). "/servicios_serv/".$id;		//Directorio perteneciente al servicio
			if (file_exists($directorio.'/servicio.ini')) {
				self::$ini = new toba_ini($directorio.'/servicio.ini');
			}
		}
		$seguro = false;
		if (isset(self::$ini)) {
			chdir($directorio);
			if (self::$ini->existe_entrada('conexion')) {
				self::$opciones = self::$ini->get_datos_entrada('conexion');
			}
			if (self::$ini->existe_entrada('certificado')) {
				$seguridad = array(
						"sign" => true,
						"encrypt" => true,
						"algorithmSuite" => "Basic256Rsa15",
						"securityTokenReference" => "IssuerSerial");
				$policy = new WSPolicy(array("security"=> $seguridad));				
				
				//Agrego los certificados manualmente
				if (! file_exists(self::$ini->get("certificado", "clave_servidor"))) {
					throw new toba_error("El archivo ".self::$ini->get("certificado", "clave_servidor")." no existe");
				}				
				$clave_privada = ws_get_cert_from_file(self::$ini->get("certificado", "clave_servidor"));

				if (! file_exists(self::$ini->get("certificado", "cert_servidor"))) {
					throw new toba_error("El archivo ".self::$ini->get("certificado", "cert_servidor")." no existe");
				}
				$certificado_servidor = ws_get_cert_from_file(self::$ini->get("certificado", "cert_servidor"));			
				
				$certificados = array(
							"privateKey" => $clave_privada,
							"certificate" => $certificado_servidor);
				$security = new WSSecurityToken($certificados);
				self::$opciones['policy'] = $policy;
				self::$opciones['securityToken'] = $security;
				$seguro = true;
			}
			//Averiguo los IDs de firmas definidos
			foreach (self::$ini->get_entradas() as $entrada => $valores) {
				if (strpos($entrada, '=')) {
					if (file_exists($valores['archivo'])) {
						$pares = array();
						foreach(explode(",", $entrada) as $par) {
							list($clave, $valor) = explode('=', trim($par));
							$pares[$clave] = $valor;							
						}
						self::agregar_mapeo_firmas(realpath($valores['archivo']), $pares, $valores['fingerprint']);
					} else {
						throw new toba_error("El archivo {$valores['archivo']} no existe");
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
		//trac/toba/wiki/Referencia/ServiciosWeb/Seguridad		
		if (!isset(self::$opciones['securityToken']) && self::servicio_con_firma()) {
			if (toba::instalacion()->es_produccion()) {
				throw new toba_error_seguridad("El servicio web esta configurado para requerir firma, sin embargo no se esta encriptando/firmando la conexion");
			} else {
				throw new toba_error_servicio_web("El servicio web esta configurado para requerir firma, sin embargo no se <a target='_blank' href='http://repositorio.siu.edu.ar/trac/toba/wiki/Referencia/ServiciosWeb/Seguridad#configuracion'>configuro correctamente</a> el servicio importando los certificados de los clientes.");
			}
		}
		//Elimina el guion bajo inicial y llama al metodo op__X
		if (substr($nombre, 0, 1) != '_') {
	       throw new BadMethodCallException('Call to undefined method ' . __CLASS__ . '::' . $nombre);
		}
		$metodo = substr($nombre, 1);
		$this->mensaje_entrada = new toba_servicio_web_mensaje($argumentos[0]);

		try {
			if ($this->servicio_con_firma()) {
				$this->validar_certificado_cliente();
			}
				
			$mensaje_salida = $this->$metodo($this->mensaje_entrada);
			if (isset($mensaje_salida)) {
				return $mensaje_salida->wsf();
			} else {
				return;
			}
		} catch (toba_error_servicio_web $e) {
			toba::logger()->error($e->get_mensaje(). $e->get_mensaje_log());
			throw new WSFault("Sender", utf8_e_seguro($e->get_mensaje()), null, $e->get_codigo());
		}
	}
	
	function validar_certificado_cliente()
	{
		$cert = $this->get_certificado_cliente();
		$cert_decodificado = base64_decode($cert);
		$fingerprint = sha1($cert_decodificado);
		
		//Verifica si existe fingerprint
		toba::logger()->debug("Fingerprint recibida: $fingerprint");
		if (! isset(self::$mapeo_firmas[$fingerprint])) {
			throw new toba_error_servicio_web('El mensaje no es válido o no fue posible procesar su firma correctamente');
		}
		//Valida el certificado completo
		if (self::decodificar_certificado(self::$mapeo_firmas[$fingerprint]['archivo']) !== $cert_decodificado) {
			throw new toba_error_seguridad('Error verificando firma del mensaje, tiene mismo fingerprint pero difiere en el contenido');
		}
		$this->id_cliente = self::$mapeo_firmas[$fingerprint]['id'];
		toba::logger()->debug("ID Cliente: ".print_r($this->id_cliente, true));
	}
	
	/**
	 * 	 Retorna el certificado utilizado por el cliente para la firma del mensaje actual
	 */
	function get_certificado_cliente()
	{
		$headers = $this->get_headers();
		if (! isset($headers['Security'])) {
			 throw new toba_error_servicio_web("El mensaje no esta firmado correctamente");
		}
		$xml = $headers['Security'];
		$namespaces = $xml->getNamespaces();
		return (string) $xml->children($namespaces['wsse'])->BinarySecurityToken;
	}

	function get_id_cliente($parametro=null)
	{
		if (! isset($this->id_cliente)) {
			return null;
		}	
		if (isset($parametro)) {
			return $this->id_cliente[$parametro];
		} else {
			return $this->id_cliente;
		}
	}

	
	/**
	 * Devuelve un arreglo con los datos de los headers
	 * @return array
	 */
	function get_headers()
	{
		if (! isset($this->headers_entrada)) {
			$headers = array();
			$datos = $this->mensaje_entrada->wsf()->outputHeaders;
			if (isset($datos)) {
				$pila = array();
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
						$value = $elemento;				
						$headers[$name] = $value;
					}
				}
			}
			$this->headers_entrada = $headers;
		}
		return $this->headers_entrada;
	}

	protected static function servicio_con_firma()
	{
		if (! empty(self::$mapeo_firmas)) {
			return true;
		} 
		return isset(self::$opciones['seguro']) ? self::$opciones['seguro'] : true;
	}		

	static function agregar_mapeo_firmas($archivo, $id, $fingerprint = null) {
		if (! isset($fingerprint)) {
			$fingerprint = sha1(self::decodificar_certificado($archivo));
		}
		self::$mapeo_firmas[$fingerprint] = array('id' => $id, 'archivo' => $archivo);
	}
	
	static function decodificar_certificado($archivo) {
		if (! file_exists($archivo)) {
			throw new toba_error("El certificado $archivo no existe");
		}
		$resource = openssl_x509_read(file_get_contents($archivo));
		$output = null;
		$result = openssl_x509_export($resource, $output);
		if($result !== false) {
			$output = str_replace('-----BEGIN CERTIFICATE-----', '', $output);
			$output = str_replace('-----END CERTIFICATE-----', '', $output);
			return base64_decode($output);
		} else {
			throw new toba_error("El archivo $archivo no es un certificado valido");
		}		
	}
}
?>