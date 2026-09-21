<?php

/**
 * Clase padre de los recuperadores de datos
 * @package Centrales
 * @subpackage Personalizacion
 */
class toba_recuperador_data
{
    protected $data;

    public function __construct()
    {
        $this->data = array();
    }

    public function get_unicos($schema)
    {
        if (!isset($this->data[$schema])) {
            throw  new toba_error("toba_bi_schema_data: El schema $schema no es válido");
        }
        return $this->data[$schema];
    }

    public function get_diferentes()
    {
        return $this->data['diferentes'];
    }

    public function set_unicos($schema, &$data)
    {
        $this->data[$schema] = $data;
    }

    public function set_diferentes(&$data)
    {
        $this->data['diferentes'] = $data;
    }
}
