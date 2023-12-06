<?php
/*
*
*/
class toba_molde_cuadro_col
{
    private $datos;

    public function __construct($identificador, $estilo)
    {
        $this->datos['clave'] = $identificador;
        $this->datos['estilo'] = $estilo;
    }

    //---------------------------------------------------
    //-- API de construccion
    //---------------------------------------------------

    public function set_etiqueta($etiqueta)
    {
        $this->datos['titulo'] = $etiqueta;
    }

    public function set_estilo($estilo)
    {
        $this->datos['estilo'] = $estilo;
    }

    public function set_formato($formato)
    {
        $this->datos['formateo'] = $formato;
    }

    public function set_orden($orden)
    {
        $this->datos['orden'] = $orden;
    }

    //---------------------------------------------------

    public function get_datos()
    {
        return $this->datos;
    }
}
