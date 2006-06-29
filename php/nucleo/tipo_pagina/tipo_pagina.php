<?php

/**
*	El tipo de p�gina determina el contenido antes y despues del cuerpo de item
*	Esto implica por ejemplo en un HTML todo el encabezado (titulo de la pagina, includes js, includes css, etc),
*	el menu particular que utiliza cada proyecto, barras generales y el pie de p�gina
*/
abstract class tipo_pagina
{
	abstract function encabezado();
	abstract function pre_contenido();
	abstract function post_contenido();
	abstract function pie();
}

?>