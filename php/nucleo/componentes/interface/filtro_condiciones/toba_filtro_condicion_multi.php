<?php

/**
 * @package Componentes
 * @subpackage Filtro
 */
class toba_filtro_condicion_multi extends toba_filtro_condicion 
{
	function __construct($casting_izq='', $casting_der='')
	{
		parent::__construct('entre', 'IN', '', '', $casting_izq, $casting_der);
	}
	
	
	function get_sql($campo, $opciones)
	{
		$opciones = toba::db()->quote($opciones);
		$conjunto = '('.implode($this->casting_der.", ", $opciones).$this->casting_der.')';
		return $campo.$this->casting_izq. ' IN '.$conjunto;
	}
	
}

?>