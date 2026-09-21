<?php

class toba_rf_grupo extends toba_rf
{
    protected $id;

    public function __construct($nombre, $padre)
    {
        parent::__construct($nombre, $padre);
        $this->id = uniqid();
    }

    public function sincronizar()
    {
        foreach ($this->get_hijos() as $hijo) {
            $hijo->sincronizar();
        }
    }
}
