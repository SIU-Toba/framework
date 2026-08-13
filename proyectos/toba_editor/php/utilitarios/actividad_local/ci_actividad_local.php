<?php

class ci_actividad_local extends toba_ci
{
    public function conf__cuadro($cuadro)
    {
        $data = toba_info_editores::get_log_modificacion_componentes();
        $cuadro->set_datos($data);
    }
}
