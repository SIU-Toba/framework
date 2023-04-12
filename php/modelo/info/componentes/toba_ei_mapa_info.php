<?php

class toba_ei_mapa_info extends toba_ei_info
{
    public static function get_tipo_abreviado()
    {
        return "Mapa";
    }


    public function get_nombre_instancia_abreviado()
    {
        return "mapa";
    }

    //------------------------------------------------------------------------
    //------ METACLASE -------------------------------------------------------
    //------------------------------------------------------------------------

    public function get_molde_subclase()
    {
        return $this->get_molde_vacio();
    }

    public function get_comentario_carga()
    {
        return array(
            "Permite cambiar la configuración del mapa previo a la generación de la salida",
        );
    }
}
