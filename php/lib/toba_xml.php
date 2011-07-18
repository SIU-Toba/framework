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
		$this->xml->startDocument('1.0', 'ISO-8859-1');
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

	/**
	 *
	 * @param <type> $nombre
	 * @param <type> $valor un string
	 * @param boolean si desea convertir el valor automaticamente de latin1 a utf8
	 */
	function add_atributo($nombre, $valor, $conv_utf8 = false)
	{
		$this->xml->startAttribute($nombre);
		if (is_bool($valor)) {
			$valor = ($valor) ? 'TRUE': 'FALSE';
		}

		if ($conv_utf8) {
			$this->xml->text(utf8_e_seguro($valor));
		} else {
			$this->xml->text($valor);
		}
		$this->xml->endAttribute();
	}

	function cerrar_documento()
	{
	   $this->xml->endDocument();
	   $this->xml->flush();
	}
}
?>
