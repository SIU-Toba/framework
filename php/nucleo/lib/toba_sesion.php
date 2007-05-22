<?php

/**
 * Representa la sesi�n del usuario en la aplicacion. En su ciclo de vida presenta tres momentos:
 *  - Inicio, donde se producen validaciones (generalmente despues de un login)
 *  - Nudo o refresco, donde se valida que no haya excedido el tiempo de no-interaccion, etc
 *  - Finalizacion. Se borra toda la informacion de la sesion en memoria
 * 
 * Cabe aclarar que el sentido de la sesion es orientada al ciclo de vida del usuario en este proyecto y no
 * sobre el  $_SESSION, siendo �ste un contenedor de informaci�n manejado en bajo nivel por lo que llamamos memoria
 * @see toba_memoria
 * 
 * @package Centrales
 */
class toba_sesion
{
	/**
	 * Ventana de extensi�n del inicio de la sesion de un usuario
	 * @ventana
	 */
	function conf__inicial($datos) {}

	/**
	 * Ventana de extensi�n del fin de la sesi�n actual
	 * @ventana
	 */	
	function conf__final() {}

	/**
	 * Ventana de extensi�n de la refresco o actualizaci�n de la sesi�n actual
	 * @ventana
	 */		
	function conf__activacion() {}
}
?>