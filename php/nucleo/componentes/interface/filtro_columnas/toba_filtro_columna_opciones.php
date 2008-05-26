<?php

class toba_filtro_columna_opciones extends toba_filtro_columna
{
	
	protected $_condiciones = array(
			'es_igual_a' 	=> array('etiqueta' => 'es igual a',		'operador_sql' => '=',			'pre' => '', 	'post' => '', 	'casting' => '::varchar'),
			'es_distinto_de' => array('etiqueta' => 'es distinto de',	'operador_sql' => '!=',			'pre' => '', 	'post' => '', 	'casting' => '::varchar'),
	);
	
	function ini()
	{
		$parametros = $this->_datos;
		$clase_ef = 'toba_'.$this->_datos['opciones_ef'];
		$this->_ef = new $clase_ef($this, null, $this->_datos['nombre'], $this->_datos['etiqueta'],
											null, null, false, $parametros);
	}
	
	function es_seleccion_multiple()
	{
		return $this->_datos['opciones_es_multiple'];
	}
	
	function tiene_condicion()
	{
		return !$this->es_seleccion_multiple() && !empty($this->_condiciones);
	}	
	
	function get_where()
	{
		if (! $this->es_seleccion_multiple()) {
			return parent::get_where();
		} elseif (isset($this->_estado['valor']) && is_array($this->_estado['valor'])) {
			$opciones = toba::db()->quote($this->_estado['valor']);
			$valor = '('.implode(", ", $opciones).')';
			return $this->get_alias_tabla().$this->get_nombre(). ' IN '.$valor;
		}
	}
	
}

?>