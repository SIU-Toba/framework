<?php
/*
*
*/
class toba_molde_datos_tabla_col
{
    private $datos;

    public function __construct($identificador, $tipo)
    {
        $this->datos['columna'] = $identificador;
        $this->datos['tipo'] = $tipo;
    }

    //---------------------------------------------------
    //-- API de construccion
    //---------------------------------------------------

    public function set_secuencia($secuencia)
    {
        $this->datos['secuencia'] = $secuencia;
    }

    public function pk()
    {
        $this->datos['pk'] = 1;
    }

    public function externa()
    {
        $this->datos['externa'] = 1;
    }

    //---------------------------------------------------

    public function get_datos()
    {
        return $this->datos;
    }
}
