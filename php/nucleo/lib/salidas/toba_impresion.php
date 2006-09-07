<?php

/**
 * Metodos b�sicos que tiene cumplir una salida de impresi�n
 * @package Librerias
 * @subpackage SalidaGrafica
 */
interface toba_impresion
{
	function titulo( $texto );
	function subtitulo( $texto );
	function mensaje( $texto );
	function salto_pagina();
}

?>