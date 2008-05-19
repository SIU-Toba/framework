<?php

class toba_filtro_columna_cadena extends toba_filtro_columna
{
	protected $_condiciones = array(
			array( 'condicion' => 'es_igual_a', 	'etiqueta' => 'es igual a',		'operador_sql' => '=',			'pre' => '', 	'post' => '', 	'casting' => '::varchar'),	
			array( 'condicion' => 'comienza_con',	'etiqueta' => 'comienza con',	'operador_sql' => 'ILIKE',		'pre' => '', 	'post' => '%',	'casting' => '::varchar'),
			array( 'condicion' => 'termina_con', 	'etiqueta' => 'termina con',	'operador_sql' => 'ILIKE',		'pre' => '%', 	'post' => '', 	'casting' => '::varchar'),
			array( 'condicion' => 'contiene', 		'etiqueta' => 'contiene',		'operador_sql' => 'ILIKE',		'pre' => '%', 	'post' => '%', 	'casting' => '::varchar'),
			array( 'condicion' => 'no_contiene', 	'etiqueta' => 'no contiene',	'operador_sql' => 'NOT ILIKE',	'pre' => '', 	'post' => '%', 	'casting' => '::varchar'),
			array( 'condicion' => 'es_distinto_de',	'etiqueta' => 'es distinto de',	'operador_sql' => '!=',			'pre' => '', 	'post' => '', 	'casting' => '::varchar'),
	);
	protected $_ef;
	
	function ini()
	{
		$this->_ef = new toba_ef_editable($this, null, $this->_datos['nombre'], $this->_datos['etiqueta'],
											null, null, false, array());
	}

	function tiene_condicion()
	{
		return ! empty($this->_condiciones);
	}
	
	function get_html_condicion()
	{
		$html = "<select>";
		foreach ($this->_condiciones as $condicion) {
			$html .= "<option value='{$condicion['condicion']}'>{$condicion['etiqueta']}</option>\n";
		}
		return $html;
	}
	
	function get_html_valor()
	{
		echo $this->_ef->get_input();
	}
}

?>