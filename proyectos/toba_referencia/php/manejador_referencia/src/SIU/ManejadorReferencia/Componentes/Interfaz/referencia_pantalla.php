<?php

//namespace SIU\ManejadorReferencia\Componentes\Interfaz;

use SIU\InterfacesManejadorSalidaToba\Componentes\Interfaz\IPantalla;

class referencia_pantalla implements IPantalla
{
    public function getInicioHtml($objeto_js, $style)
    {
        return "<div id='$objeto_js'>";
    }

    public function getInicioColapsado($id, $style)
    {
        return "<div $style id='$id'>\n";
    }

    public function getInicioWrapperCuerpo($info_ci)
    {
        return "";
    }

    public function getClaseBotonera($superior)
    {
        if (!$superior) {
            return "col-md-12 divider";
        }
    }

    public function getInicioCuerpo($tipo_navegacion)
    {
        return "";
    }

    public function getPreTabs($tipo_navegacion)
    {
        if ($tipo_navegacion == 'tab_h') {
            return "<div class='nav-tabs-custom'>";
        }
        if ($tipo_navegacion == 'tab_v' || $tipo_navegacion == 'wizard') {
            return "<div class='nav-tabs-custom nav-stacked '>";
        }
        return '';
    }

    public function getPostTabs($tipo_navegacion)
    {
        return "";
    }

    public function getInicioDescWizard()
    {

    }

    public function getTituloWizard($titulo)
    {
        return "<div class='panel-heading'> <b>$titulo</b> </div>";
    }

    public function getFinDescWizard()
    {

    }

    public function getTocWizard($lista_tabs, $id_controlador)
    {
        $salida = "<ul class='nav nav-tabs nav-stacked col-md-2'>";
        $pasada = true;
        foreach ($lista_tabs as $id => $pantalla) {

            if ($pasada) {
                $clase = 'ci-wiz-toc-pant-pasada';
            } else {
                $clase = 'ci-wiz-toc-pant-futuro';
            }
            if ($id == $id_controlador) {
                $clase = 'active';
                $pasada = false;
            }
            $salida .= "<li class='$clase'> <a>";
            $salida .= $pantalla->get_etiqueta();
            $salida .= "</a></li>";
        }
        $salida .= "</ul>";
        return $salida;
    }

    public function getInicioTabs($tipo_tab)
    {
        if ($tipo_tab == 'tab_h') {
            return "<ul class='nav nav-tabs'>\n";
        }
        if ($tipo_tab == 'tab_v') {
            return "<ul class='nav nav-tabs nav-stacked col-md-2  no-padding'>";
        }
        return '';
    }

    public function getFinTabs($tipo_tab)
    {
        return "</ul>";
    }

    public function getPreContenido($tipo_navegacion)
    {
        if ($tipo_navegacion == 'tab_h') {
            return "	<div class='tab-content'>";
        }
        if ($tipo_navegacion == 'tab_v' || $tipo_navegacion == 'wizard') {
            return "	<div class='tab-content col-md-10'>";
        }
    }

    public function getPostContenido($tipo_navegacion)
    {
        $salida = "	</div>"; // Fin de contenido
        $salida .= "</div>"; // Fin de tabs
        return $salida;
    }

    public function getFinCuerpo($tipo_navegacion)
    {
        return "";
    }

    public function getFinWrapperCuerpo()
    {
        return "";
    }

    public function getFinColapsado()
    {
        return "</div>";
    }

    public function getFinHtml()
    {
        return "</div>";
    }

    public function getSeparadorDependencias()
    {
        return '';
    }

}
