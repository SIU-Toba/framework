<?php

class toba_servicio_web_mensaje
{
	const id_fila_generica = '_toba_fila_';
	
	protected $mensaje = null;
	protected $payload;
	protected $datos;
	protected $opciones;
	
	/**
	 * @param mixed $datos Puede ser el XML del payload o un arreglo
	 * @param array $opciones Arreglo opcional de opciones WSF
	 */	
	function __construct($datos, $opciones=array()) 
	{		
		$this->datos = $datos;
		$this->opciones = $opciones;		
	}
	
	/**
	 *  Crea el objeto WSMessage propiamente dicho y ademas determina que comprende el payload
	 * @ignore
	 */
	protected function instanciar_mensaje()
	{
		if (is_string($this->datos)) {
			$this->payload = $this->datos;
			$this->mensaje = new WSMessage($this->payload, $this->opciones);			
		} elseif (is_array($this->datos)) {
			$proyecto = toba::proyecto()->get_id();
			$this->payload = "<ns1:servicio xmlns:ns1=\"http://toba.siu.edu.ar/$proyecto\">\n";
			$this->payload .= self::array_a_payload($this->datos);
			$this->payload .= "</ns1:servicio>";			
			$this->mensaje = new WSMessage($this->payload, $this->opciones);
		} elseif (is_a($this->datos, 'WSMessage')) {
			$this->mensaje = $this->datos;
			$this->payload = $this->mensaje->str;
		}					
	}
			
	/**
	 * @return WSMessage
	 */
	function wsf()
	{
		if (! isset($this->mensaje)) {
			$this->instanciar_mensaje();
		}
		
		return $this->mensaje;	
	}
	
	/**
	 * @return string 
	 */
	function get_payload()
	{
		if (! isset($this->payload)) {
			$this->instanciar_mensaje();
		}
		return $this->payload;
	}
	
	/**
	 * Retorna el payload del mensaje como un array de N-dimensiones
	 */
	function get_array()
	{
		$xml = new SimpleXMLElement($this->get_payload());
		return self::payload_a_array($xml);
	}
	
	static function payload_a_array($nodo_xml)
	{
		$salida = array();
		foreach ($nodo_xml->children() as $hijo) {
			$nietos = $hijo->children();
			if (empty($nietos)) {
				$valor = toba_xml_tablas::decode((string) $hijo);
			} else {
				$valor = self::payload_a_array($hijo);
			}
			$clave = (string) $hijo->getName();
			if (substr($clave, 0, strlen(self::id_fila_generica)) === self::id_fila_generica) {
				$salida[] = $valor;
			} else {
				$salida[$clave] = $valor;
			}
			
		}		
		return $salida;
	}
	
	static function array_a_payload($array, $nivel=0, $clave_padre = '')
	{
		$salida = '';
		$tab = str_repeat("\t", $nivel);
		foreach ($array as $clave => $valor) {
			$empieza_con_numero = strlen($clave) > 0 && ctype_digit(substr($clave, 0, 1));
			if ($empieza_con_numero && is_string($clave)) {
				throw new toba_error_def("El arreglo contiene una clave asociativa ($clave) que comienza con un número, XML no acepta tags que comienzen con numeros");
			}
			if ($empieza_con_numero) {				
				$clave = self::id_fila_generica.$clave_padre;	//El xml no puede tener claves numericas, se define una clave unica para los posicionales
			}
			$clave = toba_xml_tablas::encode($clave);
			$salida .= "$tab<$clave>";
			if (is_array($valor)) {
				$salida .= "\n".self::array_a_payload($valor, $nivel + 1, $clave);
				$salida .= "$tab</$clave>\n";				
			} else {
				$valor = toba_xml_tablas::encode($valor);				
				$salida .= $valor;
				$salida .= "</$clave>\n";
			}

		}	
		return $salida;	
	}	
	
	/**
	 * Firma el mensaje completo usando Openssl
	 * @param string $clave_privada Ruta al archivo que posee la clave privada
	 */
	function firmar_mensaje($clave_privada)
	{
		//Aca concateno los headers y los datos para hacer el rsa firma
		$headers = $this->get_datos_headers($this->opciones['inputHeaders']);
		$mensaje = $this->datos;
		if (is_a($mensaje, 'WSMessage')) {		//Si es un objeto mensaje, pido la representacion en string
			$mensaje = $mensaje->str;
		}
		if (is_array($mensaje)) {
			$proyecto = toba::proyecto()->get_id();			
			$mensaje = "<ns1:servicio xmlns:ns1=\"http://toba.siu.edu.ar/$proyecto\">\n";
			$mensaje .= self::array_a_payload($this->datos);
			$mensaje .= "</ns1:servicio>";			
		}
		$cadena_a_firmar = trim($mensaje . implode('', $headers));
		$priv_key_id = openssl_get_privatekey('file://' .$clave_privada);
		if (! openssl_sign($cadena_a_firmar, $firma, $priv_key_id)) {
			throw new toba_error('No fue posible firmar el mensaje, se anula el envio');
		}
		
		//Si no falla entonces agrego la firma a los headers del mensaje
		$header_firma = new WSHeader(array('name' => 'firma' , 'data' => base64_encode($firma)));
		$this->opciones['inputHeaders'][] = $header_firma;
		toba::logger()->debug("Firmando mensaje del servicio_web");
		
		//Descomentar para ver los detalles de la firma		
		//toba::logger()->debug("Data: ".var_export($cadena_a_firmar, true));
		//toba::logger()->debug("Firma : ".var_export(base64_decode($firma), true));
		//toba::logger()->debug("Priv key: ".var_export($priv_key_id, true));
	}
	
	/**
	 * Devuelve los datos de los headers como un arreglo de datos
	 * @param array $headers Arreglo de objetos WSMessage
	 * @return array
	 */
	private function get_datos_headers($headers)
	{
		$datos = array();
		$aux_iterador = $headers;
		while (! empty($aux_iterador)) {
			$elem = array_shift($aux_iterador);
			if (is_a($elem->data, 'WSMessage')) {				//Si es otro mensaje, lo agrego a la cola
				$aux_iterador[] = $elem->data;
			} elseif (is_a($elem->data, 'WSHeader')) {				//Si es un header, lo agrego a la cola
				$aux_iterador[] = $elem->data;
			} elseif (is_array($elem->data)) {					//Es posible que sea un arreglo de WSMessage
				$aux_iterador = array_merge($aux_iterador, $elem->data);	
			}else {
				$datos[$elem->name] = trim($elem->data);				
			}
		}
		return $datos;
	}

	function set_headers($asociativo) {
		$data = array();
		foreach ($asociativo as $clave => $valor) {
			$data[] = new WSHeader(array('name' => $clave, 'data' => $valor));
		}
		$headers = array(
				new WSHeader(
					array(
						'name' => 'datos', 
						'data' => $data
				)
			)
		);
		$this->opciones['inputHeaders'] = $headers;
		toba::logger()->debug("Incluyendo headers en el mensaje del servicio_web");
	}
}
?>