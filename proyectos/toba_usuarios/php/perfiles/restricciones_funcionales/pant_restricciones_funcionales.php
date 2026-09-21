<?php

class pant_restricciones_funcionales extends toba_ei_pantalla
{
    public function generar_layout()
    {
        $this->dep('form_restriccion')->generar_html();
        $this->dep('arbol')->generar_html();

    }
}
