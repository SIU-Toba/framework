<?php


class ci_abm_basico extends toba_ci
{
    //---- cuadro -------------------------------------------------------

    public function evt__cuadro__seleccion($seleccion)
    {
        $this->dependencia('datos')->cargar($seleccion);
    }

    public function conf__cuadro()
    {
        return $this->get_datos_listado();
    }

    //---- form -------------------------------------------------------

    public function evt__formulario__alta($datos)
    {
        $datos['proyecto'] = toba_editor::get_proyecto_cargado();
        $this->dependencia('datos')->set($datos);
        $this->dependencia('datos')->sincronizar();
        $this->dependencia('datos')->resetear();
    }

    public function evt__formulario__baja()
    {
        $this->dependencia('datos')->eliminar_filas(false);
        $this->dependencia('datos')->sincronizar();
        $this->dependencia('datos')->resetear();
    }

    public function evt__formulario__modificacion($datos)
    {
        $datos['proyecto'] = toba_editor::get_proyecto_cargado();
        $this->dependencia('datos')->set($datos);
        $this->dependencia('datos')->sincronizar();
        $this->dependencia('datos')->resetear();
    }

    public function evt__formulario__cancelar()
    {
        $this->dependencia('datos')->resetear();
    }

    public function conf__formulario(toba_ei_formulario $form)
    {
        if ($this->dependencia('datos')->hay_cursor()) {
            $form->set_datos($this->dependencia('datos')->get());
        }
    }
}
