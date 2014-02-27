<?php

namespace rest\lib;

use rest\rest;

class rest_filtro_sql
{
	protected $campos = array();
	
	function __construct()
	{
		
	}
	
	
	function agregar_campo($alias_qs, $alias_sql = NULL)
	{
//		$filtro->agregar_campo("pers.nombre", rest::request()->get("nombre"));
		
		$this->campos[$alias_qs] = array('alias_sql' => $alias_sql);
	}
	
	function get_sql_where($separador = 'AND')
	{
		$clausulas = array();
		foreach ($this->campos as $alias_qs => $campo) {
			$qs = trim(rest::request()->get($alias_qs));
			if ($qs != '') {
				$partes = explode(';', $qs);
				if (count($partes) < 2) {
					throw new rest_error(400, "Parámetro '$alias_qs' invalido. Se esperaba 'condicion;valor' y llego '$qs'");				
				}
				$condicion = $partes[0];
				$valor1 = $partes[1];
				$valor2 = count($partes) > 2 ? $partes[2] : null;
				$clausulas[] = $this->get_sql_clausula($alias_qs, $campo['alias_sql'], $condicion, $valor1, $valor2);
			}
		}
		if (! empty($clausulas)) {
			return "\t\t".implode("\n\t$separador\t", $clausulas);
		} else {
			return '1=1';
		}		
	}
	
	
	/**
	 * Lee los parametros 'limit' y 'page' del request rest y arma el equivalente en sql (limit/offset)
	 */
	function get_sql_limit()
	{
		$sql_limit = "";
		$limit = rest::request()->get("limit");
		if (isset($limit) && is_numeric($limit)) {
			$limit = (int) $limit;
			$sql_limit .= "LIMIT ".$limit;
			
			$page = rest::request()->get("page");
			if (isset($page) && is_numeric($page)) {
				$page = (int) $page;
				$offset =  ($page-1) * $limit;
				$sql_limit .= " OFFSET ".$offset;
			}
		}
		return $sql_limit;
	}

    /**
     * Lee el parametro 'order' del request y arma el ORDER BY de la sql
     * Solo permite ordenar por los campos definidos en el constructor (evitar inyeccion sql)
     * @throws rest_error
     * @return string
     */
	function get_sql_order_by()
	{
		$get_order = rest::request()->get("order");
		if (trim($get_order) == '') {
			return "";
		}
		$sql_order_by = array();
		$get_campos = explode(",", $get_order);		
			\toba::logger()->var_dump($get_order);			
		
		foreach ($get_campos as $get_campo) {
			$get_campo = trim($get_campo);
			$signo = substr($get_campo, 0, 1);
			switch ($signo) {
				case '+': $signo = " ASC"; break;
				case '-': $signo = " DESC"; break;
				default: throw new rest_error(400, "Parámetro 'order' invalido. Se esperaba + o - y se recibio '$signo'");
			}
			$campo = substr($get_campo, 1);
			if (! isset($this->campos[$campo])) {
				throw new rest_error(400, "Parámetro 'order' invalido. No esta permitido ordenar por campo '$campo'");				
			}
			$sql_order_by[] = $campo.$signo;			
		}
		if (empty($sql_order_by)) {
			return "";
		} else {
			return "ORDER BY ".implode(', ', $sql_order_by);
		}
	}
	
	protected function get_sql_clausula($campo_qs, $campo_sql, $condicion, $valor, $valor2 = null)
	{
		switch ($condicion) {
			case 'entre':
				return $campo_sql.' BETWEEN '.quote($valor).' AND '.quote($valor2);		
			case 'es_mayor_que':
				return $campo_sql.' > '.quote($valor);
			case 'desde';
			case 'es_mayor_igual_que':
				return $campo_sql.' >= '.quote($valor);
			case 'es_menor_que':
				return $campo_sql.' < '.quote($valor);			
			case 'es_menor_igual_que':
			case 'hasta':
				return $campo_sql.' <= '.quote($valor);			
			case 'es_igual_a':
				return $campo_sql.' = '.quote($valor);			
			case 'es_distinto_de':
				return $campo_sql.' != '.quote($valor);			
			case 'contiene':
				return $campo_sql.' ILIKE '.quote('%'.$valor.'%');			
			case 'no_contiene':
				return $campo_sql.' NOT ILIKE '.quote('%'.$valor.'%');			
			case 'comienza_con':
				return $campo_sql.' ILIKE '.quote($valor.'%');			
			case 'termina_con':
				return $campo_sql.' ILIKE '.quote('%'.$valor);
			default:
				throw new rest_error(400, "Parámetro '$campo_qs' invalido. No es valida la condicion '$condicion'");				
		}
	}
}

