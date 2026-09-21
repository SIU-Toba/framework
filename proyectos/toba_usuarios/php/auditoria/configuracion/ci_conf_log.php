<?php

class ci_conf_log extends toba_ci
{
    protected $s__seleccionado;
    protected $s__datos;

    //-----------------------------------------------------------------------------------
    //---- Eventos ----------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    public function evt__procesar()
    {
        $this->dep('datos')->sincronizar();
        $this->finalizar();
    }

    public function evt__cancelar()
    {
        $this->finalizar();
    }

    public function finalizar()
    {
        $this->dep('datos')->resetear();
        unset($this->s__seleccionado);
        $this->set_pantalla('pant_inicial');
    }

    //-----------------------------------------------------------------------------------
    //---- form -------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    public function conf__form($form)
    {
        if ($this->dep('datos')->esta_cargada()) {
            $form->set_datos($this->dep('datos')->get());
        }
    }

    public function evt__form__modificacion($datos)
    {
        $this->dep('datos')->set($datos);
    }

    //-----------------------------------------------------------------------------------
    //---- cuadro -----------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    public function conf__cuadro(toba_ei_cuadro $cuadro)
    {
        $cuadro->set_datos(consultas_instancia::get_lista_proyectos());
    }

    public function evt__cuadro__seleccion($seleccion)
    {
        $this->s__seleccionado = $seleccion['proyecto'];
        $this->dep('datos')->cargar($seleccion);
        $this->set_pantalla('pant_edicion');
    }

    public function get_conf_proyecto($proyecto)
    {
        $datos = consultas_instancia::get_datos_proyecto($proyecto);
        return $datos;
    }
}
