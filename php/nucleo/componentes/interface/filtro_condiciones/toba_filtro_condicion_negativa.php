<?php
/**
 * @package Componentes
 * @subpackage Filtro
 */
class toba_filtro_condicion_negativa extends toba_filtro_condicion
{
	function get_sql($campo, $valor)
	{
		$valor = toba::db()->quote($this->pre.trim($valor).$this->post);
		return '('.$campo.$this->casting_izq.' '.$this->operador_sql.' '.
			$this->pre_evaluacion.$valor.$this->post_evaluacion.$this->casting_der. ' OR '. $campo.' IS NULL)';
	}
}
?>
