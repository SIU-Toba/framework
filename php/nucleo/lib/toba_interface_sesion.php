<?php

/**
 * Interface que debe implementar una extensión o redefinición de toba::sesion()
 * @package Centrales
 */
interface toba_interface_sesion
{
	function iniciar_contexto();		// Inicalizar el contexto de ejecucion del proyecto
	function conf__inicial();			// Atrapar el inicio de sesion
	function conf__final();				// Atrapar la finalizacion de la sesion
	function conf__activacion();		// Activacion de la sesion en cada request
}
?>