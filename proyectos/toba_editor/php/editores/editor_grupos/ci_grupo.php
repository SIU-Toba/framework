<?php


class ci_grupo extends toba_ci
{
    protected $carga_ok;

    public function ini()
    {
        $zona = toba::solicitud()->zona();
        if ($editable = $zona->get_editable()) {
            $clave['proyecto'] = $editable[0];
            $clave['usuario_grupo_acc'] = $editable[1];
            $this->carga_ok = $this->dependencia('datos')->cargar($clave);
        }
    }

    public function conf()
    {
        if (!$this->carga_ok) {
            $this->pantalla()->eliminar_evento('eliminar');
        }
    }

    //---- Eventos CI -------------------------------------------------------

    public function evt__guardar()
    {
        $this->dependencia('datos')->sincronizar();
        $clave = $this->dependencia('datos')->get_clave_valor(0);
        toba::zona()->cargar(array($clave['proyecto'], $clave['usuario_grupo_acc']));
        admin_util::refrescar_barra_lateral();
    }

    public function evt__eliminar()
    {
        $this->dependencia('datos')->eliminar_todo();
        toba::solicitud()->zona()->resetear();
        admin_util::refrescar_barra_lateral();
    }

    //-------------------------------------------------------------------
    //--- DEPENDENCIAS
    //-------------------------------------------------------------------

    public function evt__formulario__modificacion($datos)
    {
        $datos['proyecto'] = toba_editor::get_proyecto_cargado();
        $this->dependencia('datos')->set($datos);
    }

    public function conf__formulario()
    {
        return $this->dependencia('datos')->get();
    }
}
