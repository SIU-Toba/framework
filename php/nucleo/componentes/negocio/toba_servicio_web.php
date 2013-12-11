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

	protected static function cargar_ini($id)
	{
		if (! isset(self::$ini)) {
			$modelo = toba_modelo_catalogo::instanciacion();	
			$modelo->set_db(toba::db());	
			$proyecto = $modelo->get_proyecto(toba::instancia()->get_id(), toba::proyecto()->get_id());
			self::$ini = toba_modelo_servicio_web::get_ini_server($proyecto, $id);
		}
	}
		
	public static function esta_activo($id)
	{	
		$activo = false;
		self::cargar_ini($id);
		if (isset(self::$ini) && self::$ini->existe_entrada('general', 'activo')) {
			$activo = (self::$ini->get('general', 'activo') == '1');
		}
		return $activo;
	}
	
	static function get_opciones()
	{
		return array();
	}
	
	/**
	 * @ignore Metodo interno que llama la solicitud web para obtener las configuraciones del servicio 
	 */
	public static function _get_opciones($id, $clase)
	{
		$seguro = false;
		self::cargar_ini($id);	
		$directorio = toba_instancia::get_path_instalacion_proyecto(toba::proyecto()->get_id()). "/servicios_serv/$id";	
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
		$srv_name = $this->_solicitud->get_id_operacion();
		if (! self::esta_activo($srv_name)) {	
			toba::logger_ws()->debug('Se intento acceder a un servicio web inactivo: ' . $srv_name);
			toba::logger_ws()->set_checkpoint();
			throw new WSFault('Receiver', ' El servicio no esta activo'); 
		}
			
		//trac/toba/wiki/Referencia/ServiciosWeb/Seguridad		
		if (!isset(self::$opciones['securityToken']) && self::servicio_con_firma()) {
			if (toba::instalacion()->es_produccion()) {
				throw new toba_error_seguridad("El servicio web esta configurado para requerir firma, sin embargo no se esta encriptando/firmando la conexion");
			} else {
				throw new toba_error_servicio_web("El servicio web esta configurado para requerir firma, sin embargo no se <a target='_blank' href='http://repositorio.siu.edu.ar/trac/toba/wiki/Referencia/ServiciosWeb/Seguridad#configuracion'>configuro correctamente</a> el servicio importando los certificados de los clientes.");
			}
		}
		//Elimina el guion bajo inicial y llama al metodo op__X
		toba::logger_ws()->debug("Metodo invocado: $nombre");		
		toba::logger_ws()->debug('Argumentos recibidos');
		toba::logger_ws()->var_dump($argumentos);
		toba::logger_ws()->set_checkpoint();
		if (substr($nombre, 0, 1) != '_') {
			throw new BadMethodCallException('Call to undefined method ' . __CLASS__ . '::' . $nombre);
		}
		$metodo = substr($nombre, 1);
		toba::solicitud()->set_metodo_invocado($metodo);
		$this->mensaje_entrada = new toba_servicio_web_mensaje($argumentos[0]);

		try {
			if ($this->servicio_con_firma()) {
				$this->validar_certificado_cliente();
			}
			$mensaje_salida = $this->$metodo($this->mensaje_entrada);
			if (isset($mensaje_salida)) {
				$mensaje = $mensaje_salida->wsf();
				return $mensaje;
			} else {
				toba::logger_ws()->debug("Respuesta de retorno vacía");
				toba::logger_ws()->set_checkpoint();				
				return;
			}
		} catch (toba_error_servicio_web $e) {
			toba::logger_ws()->error("Excepción lanzada: ".$e->get_mensaje(). ". Más detalles: " . $e->get_mensaje_log());
			toba::logger_ws()->set_checkpoint();			
			toba::logger()->error("Excepción lanzada: ".$e->get_mensaje(). ". Más detalles: " . $e->get_mensaje_log());
			toba::solicitud()->registrar();
			throw new WSFault("Sender", utf8_e_seguro($e->get_mensaje()), null, $e->get_codigo());
		}
	}
	
	function validar_certificado_cliente()
	{
		$cert = $this->get_certificado_cliente();
		$cert_decodificado = base64_decode($cert);
		$fingerprint = sha1($cert_decodificado);
		
		//Verifica si existe fingerprint
		toba::logger_ws()->debug("Fingerprint recibida: $fingerprint");
		toba::logger()->debug("Fingerprint recibida: $fingerprint");
		if (! isset(self::$mapeo_firmas[$fingerprint])) {
			throw new toba_error_servicio_web('El mensaje no es válido o no fue posible procesar su firma correctamente');
		}
		//Valida el certificado completo
		if (self::decodificar_certificado(self::$mapeo_firmas[$fingerprint]['archivo']) !== $cert_decodificado) {
			throw new toba_error_seguridad('Error verificando firma del mensaje, tiene mismo fingerprint pero difiere en el contenido');
		}
		$this->id_cliente = self::$mapeo_firmas[$fingerprint]['id'];
		toba::logger_ws()->debug("ID Cliente: ".print_r($this->id_cliente, true));
		toba::logger_ws()->set_checkpoint();
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
		return toba_firma_digital::certificado_decodificar(file_get_contents($archivo));
	}
	

	
	//-------------------------------------------------------------------------------------------------//
	//				OPERACION TEST							//
	//------------------------------------------------------------------------------------------------//
	/**
	 * Retorna la misma cadena enviada al servidor
	 * @param string $texto texto a repetir
	 * @return string $texto texto repetido
	 */
	function op__eco(toba_servicio_web_mensaje $mensaje)
	{
		toba::logger()->debug('Entro al servicio web');
		$xml = new SimpleXMLElement($mensaje->get_payload());
		toba::logger()->debug('El payload para testeo es: ' .$xml->texto);
		$texto = xml_encode(xml_decode($xml->texto));
		$payload = <<<XML
<ns1:eco xmlns:ns1="http://siu.edu.ar/toba/pruebas">
	<texto>$texto</texto>
</ns1:eco>
XML;
		return new toba_servicio_web_mensaje($payload);
	}

}
?>