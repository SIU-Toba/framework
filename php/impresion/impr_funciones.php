<?php

/*
 	@@acceso: PUBLICO
	@@desc: Transforma milimetros a pixeles.
	@@param: float | milimetros | 0.0
*/ 
function x($mm = 0.0)
{
   return ($mm * 72.0) / 25.4;
//   return ($mm * 2.8346456692913385826771653543307);
}

/*
 	@@acceso: PUBLICO
	@@desc: Transforma milimetros a pixeles para el eje y.
	@@param: float | milimetros de alto de hoja | 0.0
	@@param: float | milimetros de alto | 0.0
*/ 
function y($alto = 0.0, $mm = 0.0)
{
   return x($alto - $mm);
}

/*
 	@@acceso: PUBLICO
	@@desc: Transforma pixeles a milimetros.
	@@param: float | milimetros | 0.0
*/ 
function x_inv($pixels = 0.0)
{
   return $pixels * (25.4 / 72.0);
//   return $pixels * 0.35277777777777777777777777777778;
}

/*
 	@@acceso: PUBLICO
	@@desc: Transforma pixeles a milimetros para el eje y.
	@@param: float | milimetros de alto de hoja | 0.0
	@@param: float | pixels | 0.0
*/ 
function y_inv($alto = 0.0, $pixels = 0.0)
{
   return $alto - x_inv($pixels);
}

?>