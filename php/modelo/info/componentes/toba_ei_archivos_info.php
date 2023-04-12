<?php

class toba_ei_archivos_info extends toba_ei_info
{
    public static function get_tipo_abreviado()
    {
        return "Archivos";
    }


    public function get_nombre_instancia_abreviado()
    {
        return "archivos";
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
            "Permite cambiar la configuración del componente previo a la generación de la salida",
        );
    }

    public function eventos_predefinidos()
    {
        $eventos = parent::eventos_predefinidos();
        $eventos['seleccionar_archivo']['parametros'] = array('$archivo');
        $eventos['seleccionar_archivo']['comentarios'] = array("Indica que el usuario seleccionó un archivo puntual de la lista", '@param string $archivo');
        return $eventos;
    }
}
