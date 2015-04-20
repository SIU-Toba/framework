<?php

class RDIServicioBasico 
{
	protected $conector;
	protected $sistema;
	protected $instalacion;
	protected $log;
	
	function __construct($conector, $sistema, $instalacion) 
	{
		$this->conector = $conector;
		$this->sistema = $sistema;
		$this->instalacion = $instalacion;
	}
	
	function asociarLog($log)
	{
		$this->log = $log;
	}
	
	protected function log($titulo, $texto)
	{
		if(isset($this->log)) {
			$this->log->add_debug($titulo, $texto);
		}
	}

	protected function definirUbicacion($parametros) 
    {		
		$this->log('RECURSO',"Definir ubicacion ('/')");
		$idCarpeta = $this->conector->getIdCarpeta('/');
		return $idCarpeta;
    }	
	//---------------------------------------------------------------------------------------------------//
	function crearTipo($contenido)
	{
		//Armar la entrada en base al arreglo contenido
		
		
		
		
		
		$idTipo = $this->conector->crearTipo($contenido);
		return $idTipo;
	}
	
	function getTipo($tipoId)
	{
		return $this->conector->getTipo($tipoId);
	}
	
	function eliminarTipo($tipoId)
	{
		return $this->conector->eliminarTipo($tipoId);
	}
}
