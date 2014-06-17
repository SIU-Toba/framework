<?php

namespace rest\chulupi;

use kernel\kernel;
use rest\lib\logger;
use rest\lib\rest_error;
use siu\errores\error_guarani_procesar_renglones;

class chulupi_logger implements logger
{
	/**
	 * @var \kernel\util\log
	 */
	protected $logger;

	const TAG = 'REST';

	function __construct()
	{
		$this->logger = kernel::log();
	}


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
		$this->logger->set_nivel($nivel);
	}

	/**
	 * Guarda los sucesos actuales en el sist. de archivos
	 */
	function guardar()
	{
//		$this->logger->fin();
//		$this->logger->guardar();
		//lo finalizo desde chulupi para poder loguear cosas propias
	}

	/**
	 * Desactiva el logger durante todo el pedido de página actual
	 */
	function desactivar()
	{
		$this->logger->set_activo(false);
	}

	/**
	 * Dumpea el contenido de una variable al logger
	 */
	function var_dump($variable)
	{
		$this->logger->add_debug(self::TAG . ': dump', print_r($variable, true));
	}

	/**
	 * Registra un suceso útil para rastrear problemas o bugs en la aplicación
	 */
	function debug($mensaje)
	{
		$this->logger->add_debug(self::TAG,  $mensaje);
	}

	/**
	 * Registra un suceso netamente informativo, para una inspección posterior
	 */
	function info($mensaje)
	{
		$this->logger->add_info(self::TAG, $mensaje);
	}

	/**
	 * Registra un suceso no contemplado que no es critico para la aplicacion
	 */
	function notice($mensaje)
	{
		$this->warning($mensaje);
	}

	/**
	 * Registra un suceso no contemplado pero que posiblemente no afecta la correctitud del proceso
	 */
	function warning($mensaje)
	{
		$this->logger->add_php_warning(self::TAG, $mensaje);
	}

	/**
	 * Registra un suceso CRITICO (un error muy grave)
	 */
	function crit($mensaje)
	{
		$this->error($mensaje);
	}

	/**
	 * Registra un error en la apl., este nivel es que el se usa en las excepciones
	 */
	function error($mensaje)
	{
		if($mensaje instanceof \Exception){
			$this->logger->add_error(self::TAG, $mensaje);
		}else {
			$this->logger->add_error(new rest_error(500, $mensaje));
		}
	}
} 