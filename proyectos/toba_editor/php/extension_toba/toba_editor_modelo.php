<?php

class toba_editor_modelo extends toba_aplicacion_modelo_base
{
    public function __construct()
    {
        $this->permitir_exportar_modelo = false;
        $this->permitir_instalar = false;
    }

    public function get_version_nueva()
    {
        return $this->get_instalacion()->get_version_actual();
    }
}
