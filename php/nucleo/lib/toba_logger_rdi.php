<?php

class toba_logger_rdi implements \RDILog {
	
	private $log;
	private $_activo = false;
	
	function __construct($proyecto)
	{
		$this->log = toba_logger::instancia($proyecto);
	}
	
	function set_activo($activo)
	{
		$this->_activo = $activo;
	}	
	
	function add_debug($etiqueta, $valor_a_dumpear, $tabla=false)
	{
		if (! $this->_activo) {
			return;
		}
		
		$this->log->seccion($etiqueta);
		if (is_array($valor_a_dumpear)) {
			if (!$tabla) {
				$this->log->debug($valor_a_dumpear);
			} else {
				$this->log->var_dump($valor_a_dumpear);
			}
        } else {
			if(is_object($valor_a_dumpear)) {
				$this->log->var_dump($valor_a_dumpear);
			} else {
				$this->log->debug($valor_a_dumpear);
			}
        }
	}
	
	function add_error($excepcion)
	{
		if (! $this->_activo){
			return;
		}
		$this->log->critico($excepcion);
	}
}
?>
