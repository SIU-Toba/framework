<?php

namespace SIUToba\rest\lib;

use SIUToba\rest\rest;

class rest_filtro_sql
{
    protected $quoter;
    protected $campos = array();
    protected $campos_ordenables = array();

    public function __construct()
    {
        $this->quoter = rest::app()->rest_quoter;
    }

    /**
     * Busca un campo en el request. Es opcional, se filtra de acuerdo a las reglas de
     * get_sql_clausula.
     *
     * @param $alias_qs       string el nombre en los parámetros del query string
     * @param null $alias_sql el nombre para hacer la sql
     * @param $valor_defecto  string formato get_sql_clausula
     *
     * @return $this
     */
    public function agregar_campo($alias_qs, $alias_sql = null, $valor_defecto = null)
    {
        if ($alias_sql === null) {
            $alias_sql = $alias_qs;
        }
        $this->campos[$alias_qs] = array('alias_sql' => $alias_sql, 'defecto' => $valor_defecto);

        return $this;
    }

    /**
     * @param $alias_qs
     * @param null $alias_sql
     *
     * @return $this
     */
    public function agregar_campo_ordenable($alias_qs, $alias_sql = null)
    {
        if ($alias_sql === null) {
            $alias_sql = $alias_qs;
        }
        $this->campos_ordenables[$alias_qs] = array('alias_sql' => $alias_sql);

        return $this;
    }

    /**
     * Un campo simple es solo ?campo=valor. Si el parametro esta se ejecuta la sql(usar %s como reemplazo del valor),
     * Sino esta y valor_defecto != null, se corre el sql con ese valor.
     *  ej agregar_campo_simple('estado', (estado = %s), 'A').
     */
    public function agregar_campo_simple($alias_qs, $sql, $valor_defecto = null)
    {
        $this->campos[$alias_qs] = array('sql' => $sql, 'defecto' => $valor_defecto);

        return $this;
    }

    public function agregar_campo_simple_local($alias_qs, $sql, $valor)
    {
        $this->campos[$alias_qs] = array('sql' => $sql, 'valor' => $valor);

        return $this;
    }

    /**
     * Un campo simple es solo ?campo=[1|0]. Si el parametro esta se ejecuta la sql_si, o sino sql_no,
     * Si valor por defecto es != null se corre alguna sql segun el valor
     *  ej agregar_campo_simple('activa', (fecha_vencicmiento > now), '', 1).
     */
    public function agregar_campo_flag($alias_qs, $sql_si, $sql_no = '', $valor_defecto = null)
    {
        $this->campos[$alias_qs] = array('sql_si' => $sql_si, 'sql_no' => $sql_no, 'defecto' => $valor_defecto);

        return $this;
    }

    public function agregar_campo_flag_local($alias_qs, $sql_si, $sql_no = '', $valor)
    { //se piden ambas sql para poder intercambiar facil un campo local o no.
        $this->campos[$alias_qs] = array('sql_si' => $sql_si, 'sql_no' => $sql_no, 'valor' => $valor);

        return $this;
    }

    /**
     * Agrega un campo al filtro sin permitirlo en el request.
     *
     * @param $alias_qs       string el nombre en los parámetros del query string
     * @param null $alias_sql el nombre para hacer la sql
     * @param $valor          string el valor del campo
     *
     * @return $this
     */
    public function agregar_campo_local($alias_qs, $alias_sql = null, $valor)
    {
        if ($alias_sql === null) {
            $alias_sql = $alias_qs;
        }
        $this->campos[$alias_qs] = array('alias_sql' => $alias_sql, 'valor' => $valor);

        return $this;
    }

    public function get_sql_where($separador = 'AND')
    {
        $clausulas = array();
        $campos = $this->campos;

        foreach ($campos as $alias_qs => $campo) {
            if (isset($campo['valor'])) {
                $valor = $campo['valor']; //es un campo local
            } else {
                $query = trim(rest::request()->get($alias_qs));
                $valor = ($query != '') ? $query : $campo['defecto'];
            }
            if ($valor !== null) {
                if (isset($campo['sql'])) { //es un campo simple
                    $clausula = $this->procesar_simple($valor, $alias_qs, $campo);
                } elseif (isset($campo['sql_si'])) { //es un campo simple
                    $clausula = $this->procesar_flag($valor, $alias_qs, $campo);
                } else {
                    $clausula = $this->procesar_filtro($valor, $alias_qs, $campo);
                }
                if ($clausula) {
                    $clausulas[] = $clausula;
                }
            }
        }
        if (!empty($clausulas)) {
            return "\t\t".implode("\n\t$separador\t", $clausulas);
        } else {
            return '1 = 1';
        }
    }

    /**
     * Lee los parametros 'limit' y 'page' del request rest y arma el equivalente en sql (limit/offset).
     */
    public function get_sql_limit($default = '')
    {
        $sql_limit = "";
        $limit = rest::request()->get("limit", $default);
        if (isset($limit) && is_numeric($limit)) {
            $limit = (int) $limit;
            $sql_limit .= "LIMIT ".$limit;

            $page = rest::request()->get("page");
            if (isset($page) && is_numeric($page)) {
                $page = (int) $page;
                if (!$page) {
                    throw new rest_error(400, "Parámetro 'page' invalido. Se esperaba un valor mayor o igual que 1 y se recibio '$page'");
                }
                $offset = ($page - 1) * $limit;
                $sql_limit .= " OFFSET ".$offset;
            }
        }

        return $sql_limit;
    }

    /**
     * Lee el parametro 'order' del request y arma el ORDER BY de la sql
     * Solo permite ordenar por los campos definidos en el constructor (evitar inyeccion sql).
     *
     * @param null $default si no hay un param order, se usa el default
     *
     * @throws rest_error
     *
     * @return string
     */
    public function get_sql_order_by($default = null)
    {
        $get_order = rest::request()->get("order");
        $usar_default = false;
        if (trim($get_order) == '') {
            if ($default !== null) {
                $usar_default = true;
                $get_order = $default;
            } else {
                return "";
            }
        }
        $sql_order_by = array();
        $get_campos = explode(",", $get_order);

        foreach ($get_campos as $get_campo) {
            $get_campo = trim($get_campo);
            $signo = substr($get_campo, 0, 1);
            switch ($signo) {
                case '+':
                    $signo = " ASC";
                    break;
                case '-':
                    $signo = " DESC";
                    break;
                default:
                    throw new rest_error(400, "Parámetro 'order' invalido. Se esperaba + o - y se recibio '$signo'");
            }
            $campo = substr($get_campo, 1);
            if ($usar_default) {
                $sql_order_by[] = $campo.$signo;
            } elseif (!isset($this->campos_ordenables[$campo])) {
                throw new rest_error(400, "Parámetro 'order' invalido. No esta permitido ordenar por campo '$campo'");
            } else {
                $alias_sql = $this->campos_ordenables[$campo]['alias_sql'];
                $sql_order_by[] = $alias_sql.$signo;
            }
        }
        if (empty($sql_order_by)) {
            return "";
        } else {
            return "ORDER BY ".implode(', ', $sql_order_by);
        }
    }

    protected function procesar_simple($valor, $alias_qs, $campo)
    {
        $clausula = '';
        if ($valor != null) {
            $clausula = sprintf($campo['sql'], $this->quote($valor));
        }

        return $clausula;
    }

    protected function procesar_flag($valor, $alias_qs, $campo)
    {
        $clausula = '';

        if ($valor == '1') {
            $clausula = $campo['sql_si'];
        } elseif ($valor == '0') {
            $clausula = $campo['sql_no'];
        }

        return $clausula;
    }

    protected function procesar_filtro($valor, $alias_qs, $campo)
    {
        $partes = explode(';', $valor);
        if (count($partes) < 2) {
            throw new rest_error(400, "Parámetro '$alias_qs' invalido. Se esperaba 'condicion;valor' y llego '$valor'");
        }
        $condicion = $partes[0];
        $valor1 = $partes[1];
        $valor2 = count($partes) > 2 ? $partes[2] : null;
        $clausula = $this->get_sql_clausula($alias_qs, $campo['alias_sql'], $condicion, $valor1, $valor2);

        return $clausula;
    }

    protected function get_sql_clausula($campo_qs, $campo_sql, $condicion, $valor, $valor2 = null)
    {
        switch ($condicion) {
            case 'entre':
                return $campo_sql.' BETWEEN '.$this->quote($valor).' AND '.$this->quote($valor2);
            case 'es_mayor_que':
                return $campo_sql.' > '.$this->quote($valor);
            case 'desde';
            case 'es_mayor_igual_que':
                return $campo_sql.' >= '.$this->quote($valor);
            case 'es_menor_que':
                return $campo_sql.' < '.$this->quote($valor);
            case 'es_menor_igual_que':
            case 'hasta':
                return $campo_sql.' <= '.$this->quote($valor);
            case 'es_igual_a':
                return $campo_sql.' = '.$this->quote($valor);
            case 'es_distinto_de':
                return $campo_sql.' <> '.$this->quote($valor); //o !=, pero internamente se convierte a <>
            case 'contiene':
                return $campo_sql.' ILIKE '.$this->quote('%'.$valor.'%');
            case 'no_contiene':
                return $campo_sql.' NOT ILIKE '.$this->quote('%'.$valor.'%');
            case 'comienza_con':
                return $campo_sql.' ILIKE '.$this->quote($valor.'%');
            case 'termina_con':
                return $campo_sql.' ILIKE '.$this->quote('%'.$valor);
            default:
                throw new rest_error(400, "Parámetro '$campo_qs' invalido. No es valida la condicion '$condicion'");
        }
    }

    protected function quote($dato)
    {
        if (!is_array($dato)) {
            return $this->quoter->quote($dato);
        } else {
            $salida = array();
            foreach (array_keys($dato) as $clave) {
                $salida[$clave] = $this->quote($dato[$clave]);
            }

            return $salida;
        }
    }
}
