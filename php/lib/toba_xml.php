<?php
/**
 * Clase que ayuda a la construcción de nuevos archivos xml
 */

class toba_xml {
	const indent_string = '	';
	protected $xml;

	function __construct($path)
	{
		$this->xml = new XMLWriter();
		$this->xml->openUri($path);
		$this->xml->startDocument('1.0');
		$this->xml->setIndent(true);
		$this->xml->setIndentString(self::indent_string);
	}

	function abrir_elemento($nombre)
	{
		$this->xml->startElement($nombre);
	}

	function cerrar_elemento()
	{
		$this->xml->endElement();
	}

	function add_atributo($nombre, $valor)
	{
		$this->xml->startAttribute($nombre);
		$this->xml->text($valor);
		$this->xml->endAttribute();
	}

	function cerrar_documento()
	{
		$this->xml->endDocument();
	}
}
?>
