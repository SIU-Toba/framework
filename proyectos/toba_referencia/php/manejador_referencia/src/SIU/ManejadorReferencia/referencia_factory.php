<?php


use SIU\InterfacesManejadorSalidaToba\IFactory;

class referencia_factory implements IFactory
{
    public function getProvider()
    {
        return 'referencia';
    }

    public function getPaginaBasica()
    {
        return 'referencia_tp_basico';
    }

    public function getPaginaTitulo()
    {
        return null;
    }

    public function getPaginaNormal()
    {
        return null;
    }

    public function getPaginaPopup()
    {
        return null;
    }

    public function getPaginaLogon()
    {
        return null;
    }

    public function getMenu()
    {
        return null;
    }

    public function getElementoInterfaz()
    {
        return 'referencia_ei';
    }

    public function getPantalla()
    {
        return 'referencia_pantalla';
    }

    public function getCuadro()
    {
        return 'referencia_cuadro';
    }

    public function getCuadroSalidaHtml()
    {
        return 'referencia_cuadro_salida_html';
    }

    public function getFiltro()
    {
        return null;
    }

    public function getFormulario()
    {
        return 'referencia_formulario';
    }

    public function getFormularioMl()
    {
        return 'referencia_formulario_ml';
    }

    public function getEventoUsuario()
    {
        return 'referencia_evento_usuario';
    }

    public function getEventoTab()
    {
        return 'referencia_evento_tab';
    }

    public function getInputsForm()
    {
        return null;
    }

    public function getFiltroColumnas()
    {
        return null;
    }
}
