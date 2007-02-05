<?php
require_once('nucleo/lib/toba_interface_contexto_ejecucion.php');

/**
 * Representa el contexto de ejecucion de un proyecto.
 * Este es el lugar indicado para poner los includes de los proyectos
 * 
 * @package Centrales
 */
class toba_contexto_ejecucion implements toba_interface_contexto_ejecucion
{
	/**
	 * Creacion del contexto de ejecucion
	 * @ventana
	 */
	function conf__inicial() {}

	/**
	 * Finalizacion del contexto de ejecucion
	 * @ventana
	 */	
	function conf__final() {}

}
?>