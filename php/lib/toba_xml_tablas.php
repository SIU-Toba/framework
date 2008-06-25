<?php

class toba_xml_tablas
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
	
	/**
	 * Carga el xml con un nodo por cada tabla, uno por cada registro y dentro uno por cada propiedad
	 * <tablas>
	 * 	 <$tabla>
	 * 		<fila>
	 * 			<$campo>$valor</$campo>
	 */
	function set_tablas($arreglo, $nombre='tablas', $nodo_raiz=null) {
		if (! isset($nodo_raiz)) {
			$nodo_raiz = $this->xml;
		}
		if (! is_int($nombre)) {
			$nombre = utf8_encode($nombre);
		} else {
			$nombre = 'fila';
		}
		$actual = $nodo_raiz->appendChild($this->xml->createElement($nombre));
		
		foreach ($arreglo as $clave => $valor) {
			if (is_array($valor)) {
				//--Acceso recursivo para los registros o composición de tablas
				$this->set_tablas($valor, $clave, $actual);
			} else {
				$valor = utf8_encode($valor);
				$clave = utf8_encode($clave);
				$actual->appendChild($this->xml->createElement($clave, $valor));
			}			
		}
	}
	
	/**
	 * Retorna un arreglo asociativo por nombre de tabla conteniendo dentro un recordset
	 * 	
	 *	$tablas = array(
	 * 		'tabla' => array(
	 * 				0 => array('campo' => 'valor', ..)
	 * 
	 */
	function get_tablas()
	{
		$datos = simplexml_load_file($this->archivo);
		$salida = array();
		foreach ($datos as $tabla => $filas) {
			$tabla = utf8_decode($tabla);
			$salida[$tabla] = array();
			foreach ($filas as $fila) {
				$registro = array();
				$vars = get_object_vars($fila);
				foreach ($vars as $clave => $valor) {
					$registro[utf8_decode($clave)] = utf8_decode(strval($valor));
				}
				$salida[$tabla][] = $registro;				
			}

		}
		return $salida;
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