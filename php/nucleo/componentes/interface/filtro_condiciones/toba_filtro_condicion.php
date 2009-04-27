<?php

/**
 * @package Componentes
 * @subpackage Filtro
 */
class toba_filtro_condicion
{
	protected $etiqueta;
	protected $operador_sql;
	protected $pre;
	protected $post;
	protected $casting_izq;
	protected $casting_der;
	protected $pre_evaluacion = '';
	protected $post_evaluacion = '';
	protected $_es_condicion_fija = false;
	
	function __construct($etiqueta='', $operador_sql='', $pre='', $post='', $casting_izq='', $casting_der='')
	{
		$this->etiqueta = $etiqueta;
		$this->operador_sql = $operador_sql;
		$this->pre = $pre;
		$this->post = $post;
		$this->casting_izq = $casting_izq;
		$this->casting_der = $casting_der;
	}
	
	function get_etiqueta()
	{
		return $this->etiqueta;
	}
	
	function set_operador_sql($operador)
	{
		$this->operador_sql = $operador;
	}
	
	function set_post_valor($cadena)
	{
		$this->post = $cadena;
	}
	
	function set_pre_valor($cadena)
	{
		$this->pre = $cadena;
	}
	
	function set_casting_izq($casting)
	{
		$this->casting_izq = $casting;
	}
	
	function set_casting_der($casting)
	{
		$this->casting_der = $casting;
	}
	
	function set_pre_evaluacion($cadena)
	{
		$this->pre_evaluacion = $cadena;
	}
	
	function set_post_evaluacion($cadena)
	{
		$this->post_evaluacion = $cadena;
	}	
	
	/**
	 * Retorna la clausula sql en base a la condicion actual
	 */
	function get_sql($expresion, $valor)
	{
		$valor = toba::db()->quote($this->pre.trim($valor).$this->post);
		return $expresion.$this->casting_izq.' '.$this->operador_sql.' '.
						$this->pre_evaluacion.$valor.$this->post_evaluacion.$this->casting_der;
		
	}

	function set_condicion_fija($es_fija = true)
	{
		$this->_es_condicion_fija = $es_fija;
	}

	function es_condicion_fija()
	{
		return $this->_es_condicion_fija;
	}
}
?>