<?php

/**
 * De esta clase tienen que heredar todos aquellos que pretendan ejecutar un test
 * de personalización.
 * @package Centrales
 * @subpackage Personalizacion
 */
abstract class toba_pers_caso_test
{
    /**
     * @var toba_db_postgres7
     */
    protected $db;
    protected $sql = array();

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function get_db()
    {
        return $this->db;
    }

    abstract public function get_descripcion();

    public function ejecutar()
    {
        foreach ($this->sql as $sentencia) {
            try {
                $this->db->ejecutar($sentencia);
            } catch (toba_error $e) {
                throw  new toba_error("Error cargando los datos de la personalizacion. El sql ejecutado fue: $sentencia");
            }
        }
    }
}
