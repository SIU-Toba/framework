<?php

class ctrl_persona extends toba_control
{
    public function ejecutar(&$parametros)
    {
        if ($parametros['nro_inscripcion'][0] == 'p01') {
            $this->set_resultado(false);
            $this->set_mensaje('Ocurrió un error con ' . get_class($this));
        }
    }
}

class ctrl_persona_1 extends toba_control
{
    public function ejecutar(&$parametros)
    {
        if ($parametros['nro_inscripcion'][0] == 'p02') {
            $this->set_resultado(false);
            $this->set_mensaje('Ocurrió un error con ' . get_class($this));
        }
    }
}

class ctrl_persona_2 extends toba_control
{
    public function ejecutar(&$parametros)
    {
        if ($parametros['nro_inscripcion'][0] != '') {
            $this->set_resultado(false);
            $this->set_mensaje('Ocurrió un error con ' . get_class($this));
        }
    }
}
