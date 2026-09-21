<?php

abstract class tester_caso
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

    abstract public function get_descripcion();

    public function get_schema_log_toba()
    {
        return $this->db->get_schema() . '_logs';
    }

    public function ejecutar()
    {
        foreach ($this->sql as $sentencia) {
            try {
                $this->db->ejecutar($sentencia);
            } catch (toba_error $e) {
                throw new toba_error("Error cargando los datos de la personalizacion. El sql ejecutado fue: $sentencia");
            }
        }
    }
}
