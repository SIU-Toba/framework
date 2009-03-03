<?php

/**
 * Interface que debe implementar una extensi�n o redefinici�n de toba::sesion()
 * @package Centrales
 */
interface toba_interface_sesion
{
	/**
	 * Atrapa el inicio de la sesi�n del usuario en la instancia (unica vez en toda la sesi�n)
	 * @ventana
	 */
	function conf__inicial($datos_iniciales=null);

	/**
	 * Atrapa el fin de la sesi�n del usuario en la instancia (el usuario presiono salir)
	 * @ventana
	 */
	function conf__final();

	/**
	 * Atrapa la activaci�n de la sesi�n en cada pedido de p�gina (similar a toba::contexto_ejecucion()->conf__inicial pero se ejecuta s�lo con el usuario logueado)
	 */
	function conf__activacion();

}
?>