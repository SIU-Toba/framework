<?php

class toba_servicio_web_mensaje
{
	protected $mensaje = null;
	protected $payload;
	
	/**
	 * @param mixed $datos Puede ser el XML del payload o un arreglo
	 * @param array $opciones Arreglo opcional de opciones WSF
	 */	
	function __construct($datos, $opciones=array()) 
	{
		if (is_string($datos)) {
			$this->payload = $datos;
			$this->mensaje = new WSMessage($this->payload, $opciones);			
		} elseif (is_array($datos)) {
			$proyecto = toba::proyecto()->get_id();
			$this->payload = "<ns1:servicio xmlns:ns1='http://toba.siu.edu.ar/$proyecto'>\n";
			$this->payload .= self::array_a_payload($datos);
			$this->payload .= "</ns1:servicio>";			
			$this->mensaje = new WSMessage($this->payload, $opciones);
		} elseif (is_a($datos, 'WSMessage')) {
			$this->mensaje = $datos;
			$this->payload = $this->mensaje->str;
		}			
	}
	
	/**
	 * @return WSMessage
	 */
	function wsf()
	{
		return $this->mensaje;	
	}
	
	function get_payload()
	{
		return $this->payload;
	}
	
	/**
	 * Retorna el payload del mensaje como un array de N-dimensiones
	 */
	function get_array()
	{
		$xml = new SimpleXMLElement($this->payload);
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
			if (substr($clave, 0, 4) === 'fila') {
				//El xml no puede tener claves numericas, se quita el sufijo
				if (is_numeric(substr($clave, 5))) {
					$clave = substr($clave, 5);
				}
			}
			$salida[$clave] = $valor;
			
		}		
		return $salida;
	}
	
	static function array_a_payload($array, $nivel=0)
	{
		$salida = '';
		$tab = str_repeat("\t", $nivel);
		foreach ($array as $clave => $valor) {
			if (is_numeric($clave)) {
				//El xml no puede tener claves numericas, se le anexa un sufijo
				$clave = 'fila_'.$clave;
			}
			$clave = toba_xml_tablas::encode($clave);
			$salida .= "$tab<$clave>";
			if (is_array($valor)) {
				$salida .= "\n".self::array_a_payload($valor, $nivel+1);
				$salida .= "$tab</$clave>\n";				
			} else {
				$valor = toba_xml_tablas::encode($valor);				
				$salida .= $valor;
				$salida .= "</$clave>\n";
			}

		}	
		return $salida;	
	}	
	
}


?>