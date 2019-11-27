<?php
/**
 * Formato de página pensado para un popup.
 * Se incluye un javascript para poder comunicarse con la ventana padre y además se almacena
 * en la memoria el ef que origino la apertura del popup para poder hacer esta comunicación.
 *
 * @package SalidaGrafica
 */
class toba_tp_popup extends toba_tp_basico_titulo
{

    function barra_superior()
    {
            $info = toba::solicitud()->get_datos_item();
            echo toba::output()->get('PaginaPopup')->getContenidoBarraSuperior($info['item_descripcion'],$this->titulo_item());
    }


    function inicio_barra_superior()
    {
        echo toba::output()->get('PaginaPopup')->getInicioBarraSuperior();
    }

    function fin_barra_superior()
    {
        echo toba::output()->get('PaginaPopup')->getFinBarraSuperior();
    }
}

?>
