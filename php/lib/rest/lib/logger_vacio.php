<?php

namespace rest\lib;

/**
 * Un logger que no hace nada, solo ocupa las llamadas para que siempre exista un logger
 * Class logger_vacio
 * @package rest\lib
 */
class logger_vacio implements logger
{

	/**
	 * $this->ref_niveles[2] = "CRITICAL";
	 * $this->ref_niveles[3] = "ERROR";
	 * $this->ref_niveles[4] = "WARNING";
	 * $this->ref_niveles[5] = "NOTICE";
	 * $this->ref_niveles[6] = "INFO";
	 * $this->ref_niveles[7] = "DEBUG";
	 */
	function set_nivel($nivel)
	{
		// TODO: Implement set_nivel() method.
	}

	/**
	 * Guarda los sucesos actuales en el sist. de archivos
	 */
	function guardar()
	{
		// TODO: Implement guardar() method.
	}

	/**
	 * Desactiva el logger durante todo el pedido de página actual
	 */
	function desactivar()
	{
		// TODO: Implement desactivar() method.
	}

	/**
	 * Dumpea el contenido de una variable al logger
	 */
	function var_dump($variable)
	{
		// TODO: Implement var_dump() method.
	}

	/**
	 * Registra un suceso útil para rastrear problemas o bugs en la aplicación
	 */
	function debug($mensaje)
	{
		// TODO: Implement debug() method.
	}

	/**
	 * Registra un suceso netamente informativo, para una inspección posterior
	 */
	function info($mensaje)
	{
		// TODO: Implement info() method.
	}

	/**
	 * Registra un suceso no contemplado que no es critico para la aplicacion
	 */
	function notice($mensaje)
	{
		// TODO: Implement notice() method.
	}

	/**
	 * Registra un suceso no contemplado pero que posiblemente no afecta la correctitud del proceso
	 */
	function warning($mensaje)
	{
		// TODO: Implement warning() method.
	}

	/**
	 * Registra un suceso CRITICO (un error muy grave)
	 */
	function crit($mensaje)
	{
		// TODO: Implement crit() method.
	}

	/**
	 * Registra un error en la apl., este nivel es que el se usa en las excepciones
	 */
	function error($mensaje)
	{
		// TODO: Implement error() method.
	}
}