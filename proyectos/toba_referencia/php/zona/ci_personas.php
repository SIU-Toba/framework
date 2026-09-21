<?php

php_referencia::instancia()->agregar(__FILE__);
require_once('operaciones_simples/consultas.php');

class ci_personas extends toba_ci
{
    public function conf()
    {
        if (! toba::zona()->cargada()) {
            $this->pantalla()->eliminar_evento('descargar');
        } else {
            toba::zona()->desactivar_items(array(array('orden' => '2')));
            //Tambien se puede realizar de la siguiente forma
            toba::zona()->desactivar_item('1000069');
        }
        toba::menu()->set_modo_confirmacion('Esta a punto de abandonar la edición de la persona sin grabar, ¿Desea continuar?', true);
    }

    public function evt__descargar()
    {
        toba::zona()->resetear();
    }

    public function evt__cuadro__cargar($seleccion)
    {
        toba::zona()->cargar($seleccion);
    }

    public function conf__cuadro(toba_ei_cuadro $componente)
    {
        $componente->set_datos(consultas::get_personas());
    }
}
