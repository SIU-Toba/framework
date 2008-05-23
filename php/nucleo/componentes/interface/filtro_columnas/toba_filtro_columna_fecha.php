<?php

class toba_filtro_columna_fecha extends toba_filtro_columna_compuesta
{
	protected $_condiciones = array(
			'es_igual_a' 	=> array('etiqueta' => 'es igual a',	'operador_sql' => '=',			'pre' => '', 	'post' => '', 	'casting' => '::date'),
			'es_distinto_de' => array('etiqueta' => 'es distinto de',	'operador_sql' => '!=',		'pre' => '', 	'post' => '', 	'casting' => '::date'),
			'desde' 		=> array('etiqueta' => 'desde',			'operador_sql' => '<',			'pre' => '', 	'post' => '', 	'casting' => '::date'),
			'hasta' 		=> array('etiqueta' => 'hasta',			'operador_sql' => '>',			'pre' => '', 	'post' => '', 	'casting' => '::date'),
			'entre' 		=> array('etiqueta' => 'entre',			'operador_sql' => 'BETWEEN',	'pre' => '', 	'post' => '', 	'casting' => '::date'),
	);

	function ini()
	{
		$parametros = $this->_datos;
		$this->_ef = new toba_ef_editable_fecha($this, null, $this->_datos['nombre'], $this->_datos['etiqueta'],
											null, null, false, $parametros);
	}

	
		
}

?>