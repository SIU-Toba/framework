<?php

/**
 * Clase que representa un item en el plan de importacion
 * @package Centrales
 * @subpackage Personalizacion
 */
class toba_importador_plan_item
{
    protected $path_metadatos;
    protected $id;
    protected $tipo;
    protected $path;

    public function __construct($path_metadatos, $tipo, $id, $path = null)
    {
        $this->path_metadatos = $path_metadatos;
        $this->id	= $id;
        $this->tipo = $tipo;
        $this->path = $path;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_tipo()
    {
        return $this->tipo;
    }

    public function get_path()
    {
        return $this->path;
    }

    public function get_path_absoluto()
    {
        return $this->path_metadatos .'/'. $this->path;
    }
}
