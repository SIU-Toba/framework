<?php

/**
 * Interface que debe implementar una extensi�n o redefinici�n de toba::contexto_ejecucion
 * @package Centrales
 */
interface toba_interface_contexto_ejecucion
{
	/**
	 * Ventana que se ejecuta siempre al ingresar el proyecto a la ejecuci�n del request (pedido de p�gina).
	 * Por este motivo es util para agregar configuraciones globales al proyecto
	 * @ventana
	 */
	function conf__inicial();

	/**
	 * Ventana que se ejecuta siempre a la salida del proyecto adela ejecuci�n del request (pedido de p�gina).
	 * @ventana
	 */
	function conf__final();

}
?>