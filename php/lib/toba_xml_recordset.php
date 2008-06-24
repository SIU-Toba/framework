<?php

class toba_xml_recordset
{
	protected $archivo;
	
	/**
	 * @var DOMDocument
	 */
	protected $xml;
	
	function __construct($archivo=null) {
		if (isset($archivo)) {
			$this->archivo = $archivo;
		} else {
			$this->xml = new DOMDocument('1.0','UTF-8');
			$this->xml->formatOutput = true;
		}
	}
	
	function set_asociativo($nombre_raiz, $arreglo, $nodo_actual=null) {
		if (! isset($nodo_actual)) {
			$nodo_actual = $this->xml;
		}
		if (! is_int($nombre_raiz)) {
			$nombre_raiz = utf8_encode($nombre_raiz);
		} else {
			$nombre_raiz = 'fila';
		}
		$raiz = $this->xml->createElement($nombre_raiz);
		$raiz = $nodo_actual->appendChild($raiz);
		
		foreach ($arreglo as $clave => $valor) {
			if (is_array($valor)) {
				$this->set_asociativo($clave, $valor, $raiz);
			} else {
				$valor = utf8_encode($valor);
				$clave = utf8_encode($clave);
				$raiz->appendChild($this->xml->createElement($clave, $valor));
			}			
		}
	}
	
	function guardar($archivo)
	{
		$this->xml->save($archivo);
	}
	
	function get_xml()
	{
		return $this->xml;
	}
	
}


?>