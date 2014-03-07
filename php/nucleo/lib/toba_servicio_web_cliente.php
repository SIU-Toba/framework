<?php

class toba_servicio_web_cliente
{
    protected $wsf;
    protected $opciones;
    protected $id_servicio;
    protected $proyecto;

    protected static $modelo_proyecto;
    protected static function get_modelo_proyecto($proyecto_id)
    {
        if (! isset(self::$modelo_proyecto)) {
            $modelo = toba_modelo_catalogo::instanciacion();
            $modelo->set_db(toba::db());
            self::$modelo_proyecto = $modelo->get_proyecto(toba::instancia()->get_id(), $proyecto_id);
        }
    }


    function __construct($opciones, $id_servicio, $proyecto = null)
    {
        if (! isset($proyecto)) {
            $proyecto = toba_editor::activado() ? toba_editor::get_proyecto_cargado() : toba::proyecto()->get_id();
        }
        $this->proyecto = $proyecto;
        $this->opciones = $opciones;
        $this->id_servicio = $id_servicio;
    }

}
