<?php

/**
 * Clase que contiene la l�gica de administraci�n de la aplicaci�n, es utiliza por los comandos
 */
interface toba_aplicacion_modelo
{
	/**
	 * Inicializaci�n de la clase en el entorno consumidor
	 * @param toba_modelo_instalacion $instalacion Representante de la instalaci�n de toba como un todo
	 * @param toba_modelo_instancia $instancia Representante de la instancia actualmente utilizada
	 * @param toba_modelo_proyecto $proyecto Representante del proyecto como un proyecto toba (sin la l�gica de admin. de la aplicaci�n)
	 */
	function set_entorno($manejador_interface, toba_modelo_instalacion $instalacion, toba_modelo_instancia $instancia, toba_modelo_proyecto $proyecto);
	
	/**
	 * @return toba_modelo_instalacion
	 */
	function get_instalacion();

	/**
	 * @return toba_modelo_instancia
	 */
	function get_instancia();

	/**
	 * @return toba_modelo_proyecto
	 */
	function get_proyecto();
	
	/**
	 * Retorna la versi�n actualmente instalada de la aplicaci�n (puede no estar migrada)
	 * @return toba_version
	 */
	function get_version_actual();

	/**
	 * Retorna la versi�n a la cual se debe migrar la aplicaci�n (si ya esta migrada deber�a ser igual a la 'version_actual')
	 * @return toba_version
	 */	
	function get_version_nueva();

	/**
	 * Ejecuta los scripts de migraci�n entre dos versiones espec�ficas del sistema
	 * @param toba_version $desde
	 * @param toba_version $hasta
	 */
	function migrar(toba_version $desde, toba_version $hasta);	
	
	/**
	 * Debe crar todo recurso extra necesario para la ejecuci�n de la aplicaci�n (base de negocios por ejemplo) 
	 * @param array $datos_servidor Asociativo con los par�metros de conexi�n a la base
	 */
	function instalar($datos_servidor);
	
	/**
	 * Eliminar todos los recursos creados por 'instalar'
	 */
	function desinstalar();

}


?>