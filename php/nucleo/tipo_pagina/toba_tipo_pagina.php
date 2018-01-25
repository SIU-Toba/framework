<?php

/**
*	El tipo de página determina el contenido antes y despues del cuerpo de item
*	Esto implica por ejemplo en un HTML todo el encabezado (titulo de la pagina, includes js, includes css, etc),
*	el menu particular que utiliza cada proyecto, barras generales y el pie de página
* 
* @package SalidaGrafica
*/
class toba_tipo_pagina
{
	function encabezado() {}
	function post_encabezado() {}
	function pre_contenido() {}
	function post_contenido() {}
	function pie() {}
}

?>
