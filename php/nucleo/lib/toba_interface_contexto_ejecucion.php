<?php

/**
 * Interface que debe implementar una extensi�n o redefinici�n de toba::contexto_ejecucion
 * @package Centrales
 */
interface toba_interface_contexto_ejecucion
{
	function conf__inicial();			// Atrapar el inicio de sesion
	function conf__final();				// Atrapar la finalizacion de la sesion
}
?>