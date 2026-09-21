<?php

php_referencia::instancia()->agregar(__FILE__);

class extension_ci extends toba_ci
{
    public function conf__formulario()
    {
        return array(
            'id' => '12',
            'descripcion' => 'Esta es la descripción.',
            'comentarios' => 'Este es un comentario.'
        );
    }


    //-- PANTALLAS

    public function conf__columnas()
    {
        $this->dep('formulario')->cambiar_layout();
    }


    public function conf__basico()
    {
        $this->dep('formulario')->set_template(null);
    }

}
