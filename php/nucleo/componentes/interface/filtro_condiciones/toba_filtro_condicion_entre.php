<?php

/**
 * @package Componentes
 * @subpackage Filtro
 */
class toba_filtro_condicion_entre extends toba_filtro_condicion 
{
	protected $etiqueta;
	protected $operador_sql;
	protected $pre;
	protected $post;
	protected $casting_izq;
	protected $casting_der;
	
	function __construct($casting_izq='', $casting_der='')
	{
		parent::__construct('entre', 'BEETWEEN', '', '', $casting_izq, $casting_der);
	}
	
	
	function get_sql($campo, $valor)
	{
		$desde = trim($valor['desde']);
		$hasta = trim($valor['hasta']);
		if ($desde == ''  || $hasta == '') {
			throw new toba_error('Valores inválidos');
		}
		$desde = toba::db()->quote($desde);
		$hasta = toba::db()->quote($hasta);
		return '('.$campo.$this->casting_izq.' BETWEEN '.$desde.$this->casting_der." AND ".$hasta.$this->casting_der.')';
	}
	
	
}

?>