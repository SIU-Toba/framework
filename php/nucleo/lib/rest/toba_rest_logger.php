<?php

//use SIUToba\rest\lib\logger;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class toba_rest_logger extends AbstractLogger// implements logger
{

	/**
	 * @var \toba_logger
	 */
	//protected $logger;

	function __construct()
	{
		//$this->logger = \toba_logger_ws::instancia();
		parent::__construct();
	}


	/**
	* $this->ref_niveles[2] = "EMERGENCY";
	* $this->ref_niveles[2] = "ALERT";
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
		//Lo llama toba cuando lo embebe. Se deja que lo llame, pero igual se llama desde rest
		//para dar lugar a otras implementaciones de logs
		$this->logger->guardar();
	}

	/**
	 * Desactiva el logger durante todo el pedido de p?ina actual
	 */
	function desactivar()
	{
		$this->logger->desactivar();
	}

	/**
	 * Dumpea el contenido de una variable al logger (not PSR-3)
	 */
	function var_dump($variable)
	{
		$this->logger->var_dump($variable);
	}

	/**
	* Logs with an arbitrary level.
	*
	* @param mixed  $level
	* @param string $message
	* @param array  $context
	*
	* @return void
	*/
    public function log($level, $message, array $context = array())
    {
        // PSR-3 dice que el mensaje siempre debe ser un string
        $message = (is_object($message)) ?  $message->__toString() : (string) $message;

        // mapeo de niveles al logger de toba anterior, hay que ver que agregar para que loguee a un solo archivo ademas
        switch ($level) {
            case PsrLogLogLevel::EMERGENCY:
                $this->logger->emergency($mensaje);
                error_log($mensaje, 4);
                break;
            case PsrLogLogLevel::ALERT:
                $this->logger->alert($mensaje);
                error_log($mensaje, 4);
                break;
            case PsrLogLogLevel::CRITICAL:
                $this->logger->crit($mensaje);
                error_log($mensaje, 4);
                break;
            case PsrLogLogLevel::ERROR:
                $this->logger->error($mensaje);
                error_log($mensaje, 4);
                break;
            case PsrLogLogLevel::WARNING:
                $this->logger->warning($mensaje);
                error_log($mensaje, 4);
                break;
            case PsrLogLogLevel::NOTICE:
                $this->logger->notice($mensaje);
                error_log($mensaje, 4);
                break;
            case PsrLogLogLevel::INFO:
                $this->logger->info($mensaje);
                error_log($mensaje, 4);
                break;
            case PsrLogLogLevel::DEBUG:
                // argument
                $this->logger->debug($mensaje);
                error_log($mensaje, 4);
                break;
            default:
                // Unknown level --> PSR-3 says kaboom 
                throw new PsrLogInvalidArgumentException(
                    "Severidad del msg desconocida"
                );
        }
    }
}