<?php

/**
 * Conoce como es la composicion interna de una clase del ambiente
 * Es utilizada para
 * @package Centrales
 */
interface meta_clase
{
	function get_molde_subclase();
	function get_clase_nombre();
	function get_clase_archivo();
	function get_subclase_nombre();
	function get_subclase_archivo();
}
?>