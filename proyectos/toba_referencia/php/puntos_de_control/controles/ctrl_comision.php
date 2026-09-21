<?php

class ctrl_comision extends toba_control
{
    public function ejecutar(&$parametros)
    {
        $this->set_resultado(false);
        $this->set_mensaje('Ocurrió un error con ' . get_class($this));
    }
}
