<?php

class toba_filtro_columna_numero extends toba_filtro_columna_compuesta 
{
	protected $_condiciones = array(
			'es_igual_a' 		=> array('etiqueta' => 'es igual a',			'operador_sql' => '=',			'pre' => '', 	'post' => '', 	'casting' => ''),		
			'es_distinto_de'	=> array('etiqueta' => 'es distinto de',		'operador_sql' => '!=',			'pre' => '', 	'post' => '', 	'casting' => ''),
			'es_mayor_que' 		=> array('etiqueta' => 'es mayor que',			'operador_sql' => '>',			'pre' => '', 	'post' => '', 	'casting' => ''),
			'es_mayor_igual_que'=> array('etiqueta' => 'es mayor o igual que',	'operador_sql' => '>=',			'pre' => '', 	'post' => '', 	'casting' => ''),
			'es_menor_que' 		=> array('etiqueta' => 'es menor que',			'operador_sql' => '<',			'pre' => '', 	'post' => '', 	'casting' => ''),			
			'es_menor_igual_que'=> array('etiqueta' => 'es menor o igual que',	'operador_sql' => '<=',			'pre' => '', 	'post' => '', 	'casting' => ''),
			'es_menor_igual_que'=> array('etiqueta' => 'es menor o igual que',	'operador_sql' => '<=',			'pre' => '', 	'post' => '', 	'casting' => ''),
			'entre'				=> array('etiqueta' => 'entre',					'operador_sql' => '<=',			'pre' => '', 	'post' => '', 	'casting' => ''),
	);
	
	function ini()
	{
		$parametros = $this->_datos;
		$parametros['edit_tamano'] = 18;
		$this->_ef = new toba_ef_editable_numero($this, null, $this->_datos['nombre'], $this->_datos['etiqueta'],
											null, null, false, $parametros);
	}

}

?>