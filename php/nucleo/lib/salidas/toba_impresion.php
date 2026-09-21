<?php

/**
 * Metodos básicos que tiene cumplir una salida de impresión
 * @package SalidaGrafica
 */
interface toba_impresion
{
    public function titulo($texto);
    public function subtitulo($texto);
    public function mensaje($texto);
    public function salto_pagina();
}
