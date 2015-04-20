<?php

class  RDICliente
{
	const CONECTOR_CMIS_ATOM = 'CMIS_ATOM';
	const CONECTOR_CMIS_REST = 'CMIS_REST';
	const CONECTOR_NUEXO = 'NUXEO';
	const CONECTOR_ALFRESCO = 'ALFRESCO';
	
    protected $conector;
    protected $url;
    protected $usuario;
    protected $clave;
    protected $sistema;
    protected $instalacion;
	protected $log;
	protected $mapeoServicios;

    function __construct($tipoConector, $url, $usuario, $clave, $sistema, $instalacion)
    {	
		$this->conector = $this->conectar($tipoConector, $url, $usuario, $clave);
        $this->url = $url;
        $this->usuario = $usuario;
        $this->clave = $clave;
        $this->sistema = $sistema;
        $this->instalacion = $instalacion;
    }
	
	private function conectar($tipoConector, $url, $usuario, $clave)
	{
		switch($tipoConector) {
			case self::CONECTOR_CMIS_ATOM:
		        $conector = new RDIConectorCMIS_ATOM($url, $usuario, $clave);
				break;
			case self::CONECTOR_CMIS_REST: 
				$conector = new RDIConectorCMIS_REST($url, $usuario, $clave);
				break;
			default:
				throw new RDIExcepcion("El conector '$tipoConector' no se encuentra implementado");
		}
		return $conector;
	}	
	
	function info()
	{
		return $this->conector->infoConexion();
	}
	
	function asociarLog($log)
	{
		$this->log = $log;
		$this->conector->asociarLog($log);
	}
	
    //--------------------------------------------------
    //-- SERVICIOS 
    //--------------------------------------------------

	function mapeoServicios()
	{
		if(!isset($this->mapeoServicios)){
			$this->mapeoServicios = new RDIServicios();
		} 
		return $this->mapeoServicios;
	}
	
	function servicio($idServicio)
	{
		$clase = $this->mapeoServicios()->getClase($idServicio);
		$servicio = new $clase($this->conector, $this->sistema, $this->instalacion);
		if (isset($this->log)) {
			$servicio->asociarLog($this->log);
		}
		return $servicio;
	}

	//--------------------------------------------------
    //-- RECURSOS 
    //--------------------------------------------------
	
	function getListaRecursos()
	{
		$mapeo = $this->mapeoServicios()->getLista();
		return array_keys($mapeo);
	}
	
	function existeRecurso($recurso)
	{
		$mapeo = $this->mapeoServicios()->getLista();
		return isset($mapeo[$recurso]);
	}
}