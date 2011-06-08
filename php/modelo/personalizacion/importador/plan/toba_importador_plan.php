<?php
class toba_importador_plan implements Iterator {
	const xml_vacio = '<vacio />';

	protected $nuevos;
	protected $modificados;
	protected $borrados;

	/**
	 * @var SimpleXMLIterator
	 */
	protected $it_actual;
	protected $tipo_actual;
	protected $path_metadatos;

    function __construct($path_plan = null)
	{
		if (isset($path_plan)) {
			$this->path_metadatos = dirname($path_plan);
			// Cargamos el xml
			$this->cargar_plan($path_plan);
		}
	}

	protected function cargar_plan($path)
	{
		$xml = simplexml_load_file($path);
		$this->set_plan($xml);
	}

	function set_plan($xml)
	{
		$this->nuevos = $this->get_nuevos($xml);
		$this->modificados = $this->get_modificados($xml);
		$this->borrados = $this->get_borrados($xml);
	}

	function rewind()
	{
		$this->nuevos->rewind();
		$this->modificados->rewind();
		$this->borrados->rewind();
		$this->it_actual = $this->get_siguiente_iterador_valido();
	}

	/**
	 * @return toba_importador_plan_item
	 */
	function current()
	{
		$xml_item = $this->it_actual->current();
		$item_plan =  new  toba_importador_plan_item($this->path_metadatos, $this->tipo_actual, (string)$xml_item['id'], (string)$xml_item['path']);
		return $item_plan;
	}

	function key()
	{
		return $this->it_actual->key();
	}

	function next()
	{
		$this->it_actual->next();
		if (!$this->it_actual->valid()) {
			$this->it_actual = $this->get_siguiente_iterador_valido();
		}

	}

	function valid()
	{
		return $this->it_actual->valid();
	}

	protected function get_siguiente_iterador_valido()
	{
		if ($this->nuevos->valid()) {
			$this->tipo_actual = toba_pers_xml_elementos::nuevas;
			return $this->nuevos;
		} elseif ($this->modificados->valid()) {
			$this->tipo_actual = toba_pers_xml_elementos::modificadas;
			return $this->modificados;
		} elseif ($this->borrados->valid()) {
			$this->tipo_actual = toba_pers_xml_elementos::borradas;
			return $this->borrados;
		}

		return  new  EmptyIterator();
	}

	protected function get_nuevos(&$xml)
	{
		$tag_nuevos = toba_pers_xml_elementos::nuevas;
		$xml_string = (isset($xml->$tag_nuevos)) ? $xml->$tag_nuevos->asXML() : self::xml_vacio;
		return  new  SimpleXMLIterator($xml_string);
	}

	protected function get_modificados(&$xml)
	{
		$tag_mod = toba_pers_xml_elementos::modificadas;
		$xml_string = (isset($xml->$tag_mod)) ? $xml->$tag_mod->asXML() : self::xml_vacio;
		return  new  SimpleXMLIterator($xml_string);
	}

	protected function get_borrados(&$xml)
	{
		$tag_borradas = toba_pers_xml_elementos::borradas;
		$xml_string = (isset($xml->$tag_borradas)) ? $xml->$tag_borradas->asXML() : self::xml_vacio;
		return  new  SimpleXMLIterator($xml_string);
	}
}
?>
