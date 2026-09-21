<?php

/**
 * Tipo de página pensado para pantallas de login, presenta un logo y un pie de página básico
 *
 * @package SalidaGrafica
 */
class toba_tp_logon extends toba_tp_basico
{
    public function inicio_barra_superior()
    {
        echo toba::output()->get('PaginaLogon')->getInicioBarraSuperior();
    }

    public function fin_barra_superior()
    {
        echo toba::output()->get('PaginaLogon')->getFinBarraSuperior();
    }

    public function pre_contenido()
    {
        echo toba::output()->get('PaginaLogon')->getPreContenido();
    }

    public function post_contenido()
    {
        echo toba::output()->get('PaginaLogon')->getPostContenido();
    }

    public function footer()
    {
        echo toba::output()->get('PaginaLogon')->getFooterHtml();
    }
}
