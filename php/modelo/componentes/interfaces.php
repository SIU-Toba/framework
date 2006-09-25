<?php

/**
 * Conoce como es la composicion interna de una clase del ambiente
 * @package Centrales
 */
interface meta_clase
{
	//Generacion de codigo
	function get_plan_construccion_metodos();
	//Analisis de codigo
	function es_evento($metodo);
	function es_evento_predefinido($metodo);
	function es_evento_valido($metodo);
	function es_evento_sospechoso($metodo);
}
?>