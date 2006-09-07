<?php

/**
 * Metodos básicos que tiene cumplir una salida de impresión
 * @package SalidaGrafica
 */
interface toba_impresion
{
	function titulo( $texto );
	function subtitulo( $texto );
	function mensaje( $texto );
	function salto_pagina();
}

?>