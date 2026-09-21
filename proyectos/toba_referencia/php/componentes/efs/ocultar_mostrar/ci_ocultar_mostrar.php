<?php

class ci_ocultar_mostrar extends toba_ci
{
    protected $s__datos;

    public function evt__form__modificacion($datos)
    {
        $this->s__datos = $datos;
    }

    public function conf__form($componente)
    {
        $componente->set_datos($this->s__datos);
    }

    public function evt__procesar()
    {

    }
}
