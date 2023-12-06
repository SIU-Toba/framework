<?php

class toba_ei_esquema_info extends toba_ei_info
{
    public static function get_tipo_abreviado()
    {
        return "Esquema";
    }


    public function get_nombre_instancia_abreviado()
    {
        return "esquema";
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
            "Permite cambiar la configuración del esquea previo a la generación de la salida",
            "El formato de carga a través del método set_datos es un arreglo de objetos que implementen la interface toba_nodo_arbol",
        );
    }
}
