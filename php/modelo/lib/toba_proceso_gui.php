<?php
/**
*	Interface grafica de usuario
*/
interface toba_proceso_gui
{
    public function titulo($texto);
    public function subtitulo($texto);
    public function mensaje($texto);
    public function error($texto);
    public function progreso_avanzar();
    public function enter();
}

class toba_mock_proceso_gui implements toba_proceso_gui
{
    public function titulo($texto)
    {
    }
    public function subtitulo($texto)
    {
    }
    public function mensaje($texto)
    {
    }
    public function error($texto)
    {
    }
    public function progreso_avanzar()
    {
    }
    public function progreso_fin()
    {
    }
    public function enter()
    {
    }
    public function cerrar()
    {
    }
    public function lista()
    {
    }
    public function separador()
    {
    }
}
