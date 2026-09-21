<?php

php_referencia::instancia()->agregar(__FILE__);

class ci_wizard extends toba_ci
{
    protected $s__tipo_inst;

    /*
    *  Manejo del formulario de seleccion de tipo de instalación
    */
    public function conf__tipos()
    {
        if (isset($this->s__tipo_inst)) {
            return $this->s__tipo_inst;
        }
    }

    public function evt__tipos__modificacion($tipo)
    {
        $this->s__tipo_inst = $tipo;
    }

    /*
    *	Se configura el wizard
    */
    public function conf()
    {
        switch ($this->get_id_pantalla()) {
            //--- Se saltean dos etapas si la instalacion no es personalizada
            case 4:
            case 5:
                if ($this->s__tipo_inst['tipo'] != 'personalizada') {
                    $pantalla = ($this->wizard_avanza()) ? 6 : 3;
                    $this->set_pantalla($pantalla);
                }
                break;

                //--- Una vez instalado los archivos nos es posible volver atrás
            case 7:
            case 8:
                $this->pantalla()->eliminar_evento('cambiar_tab__anterior');
                break;
        }

        if ($this->get_id_pantalla() == 6) {
            //--- Se cambia la etiqueta del botón 'Siguiente' por 'Instalar'
            $this->pantalla()->evento('cambiar_tab__siguiente')->set_etiqueta('Instalar');
            $this->pantalla()->evento('cambiar_tab__siguiente')->set_imagen('instalar.png', 'proyecto');
        }
    }

}
