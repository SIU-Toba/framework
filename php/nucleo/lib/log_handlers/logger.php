<?php
	
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Psr\Log\InvalidArgumentException;

class logger extends AbstractLogger
{
	use \toba_basic_logger;
	
	static protected $instancia;	
	
	protected $activo = true;	
	protected $proyecto_actual;
	protected $id_solicitud;	
	protected $mapeo_niveles = array();
	
	function __construct($proyecto)
	{
		$this->proyecto_actual =  $proyecto;
		$this->mapeo_niveles = array_flip($this->get_niveles());		
	}
	
	static function instancia($proyecto=null)
	{
		if (is_null($proyecto)) {
			$proyecto = \toba_basic_logger::get_proyecto_actual();
		}
		
		if (! isset(self::$instancia[$proyecto])) {
			self::$instancia[$proyecto] = new logger($proyecto);		//Hay que cambiarlo por logger y setearle el handler especifico para que guarde
		}
		return self::$instancia[$proyecto];
	}
		
	public function set_logger_instance(LoggerInterface $writer, $proyecto=null)
	{
		if (is_null($proyecto)) {
			$this->write_handler = array($writer);
		} else {
			$this->write_handler[$proyecto] = $writer;
		}
	}
	
	public function set_id_solicitud($solicitud)
	{
		$this->id_solicitud = $solicitud;
	}
	
	public function log($level, $message, array $context = array())
	{
		if (! $this->activo) {			//Si no estoy logueando ni me gasto.
			return;
		}				
		// PSR-3 dice que el mensaje siempre debe ser un string
		$mensaje = (is_object($message)) ?  $message->__toString() : (string) $message;		
		/*if (strpos('{', $mensaje) !== false) {					//Habria que parsear para ver si no existe algun replace en base al contexto.
			//Hay que hacer el replace aca dentro del mensaje por ahora awanto			
		}*/
		
		$nivel_toba = (is_int($level) && isset($this->ref_niveles[$level]));		
		if (! is_int($level)) {
			$nivel_pedido = strtoupper($level);
		} elseif ($nivel_toba) {
			$nivel_pedido = $this->ref_niveles[$level];
		}
		
		$nivel_psr =  (! is_int($level) && isset($this->mapeo_niveles[$nivel_pedido]));
		if (($nivel_psr || $nivel_toba) && $this->mapeo_niveles[$nivel_pedido] <= $this->nivel_maximo) {
			if (isset($context['exception']) && ($context['exception'] instanceof  \Exception)) {
				//Obtener la traza de la excepcion?.
			}
			$msg = $this->format_msg($mensaje, $nivel_pedido);			
			$this->escribir_msg($msg, $nivel_pedido);
		}
	}
	
	public function guardar()
	{
		foreach($this->write_handler as $handler) {
			if (method_exists($handler, 'guardar')) {
				$handler->guardar();
			}
		}
	}
		
	protected function escribir_msg($mensaje, $nivel)
	{		
		if (isset($this->write_handler[$this->proyecto_actual])) {
			$this->write_handler[$this->proyecto_actual]->log($nivel, $mensaje);
		} else {													//Si no hay uno especifico para el proyecto lo zampo a todos x es gral.
			foreach($this->write_handler as $log_writer) {
				$log_writer->log($nivel, $mensaje);
			}
		}
	}
	
	protected function armar_mensaje($mensaje, $nivel)
	{
		return  "[" . $this->id_solicitud . "][" .$this->proyecto_actual . "][" . $nivel ."] "  . $mensaje . PHP_EOL;
	}
	
	protected function format_msg($mensaje, $level)
	{
		$level = strtolower($level);
		switch ($level) {
			case LogLevel::EMERGENCY:
				$msg = $this->armar_mensaje($mensaje, TOBA_LOG_EMERGENCY);
				break;
			case LogLevel::ALERT:
				$msg = $this->armar_mensaje($mensaje, TOBA_LOG_ALERT);
				break;
			case LogLevel::CRITICAL:
				$msg = $this->armar_mensaje($mensaje, TOBA_LOG_CRIT);
				break;
			case LogLevel::ERROR:
				$msg = $this->armar_mensaje($mensaje, TOBA_LOG_ERROR);
				break;
			case LogLevel::WARNING:
				$msg = $this->armar_mensaje($mensaje, TOBA_LOG_WARNING);
				break;
			case LogLevel::NOTICE:
				$msg = $this->armar_mensaje($mensaje, TOBA_LOG_NOTICE);
				break;
			case LogLevel::INFO:
				$msg = $this->armar_mensaje($mensaje, TOBA_LOG_INFO);
				break;
			case LogLevel::DEBUG:
				$msg = $this->armar_mensaje($mensaje, TOBA_LOG_DEBUG);
				break;
			default:
				// Unknown level --> PSR-3 says kaboom 
				throw new InvalidArgumentException("Severidad del msg desconocida"	);
		}
		return $msg;
	}	
}
?>