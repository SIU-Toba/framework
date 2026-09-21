<?php

class ci_grupo_permisos extends toba_ci
{
    public function ini()
    {
        $editable = toba::solicitud()->zona()->get_editable();
        if ($editable && !$this->dependencia('datos')->esta_cargada()) {
            list($proyecto, $grupo) = $editable;
            $this->dependencia('datos')->cargar(array('usuario_grupo_acc' => $grupo,
                                                        'proyecto' => $proyecto));
        }

    }

    //---- Eventos CI -------------------------------------------------------

    public function evt__procesar()
    {
        $this->dependencia('datos')->sincronizar();
    }

    //-------------------------------------------------------------------
    //--- DEPENDENCIAS
    //-------------------------------------------------------------------

    //---- form -------------------------------------------------------

    public function evt__form__modificacion($datos)
    {
        $this->dependencia('datos')->tabla('grupo_permiso')->set_permisos($datos['lista_permisos']);
    }

    public function conf__form()
    {
        $per['lista_permisos'] = $this->dependencia('datos')->tabla('grupo_permiso')->get_permisos();
        return $per;
    }


}
