<?php

abstract class RDIConector
{
	const ATRIBUTO_ID =				'id';
	const ATRIBUTO_ID_VERSION =		'idVersion';	
	const ATRIBUTO_NOMBRE =			'nombre';
	const ATRIBUTO_CREACION =		'creacion';
	const ATRIBUTO_VERSION =		'version';
	
	protected $conexion;
    protected $url;
    protected $usuario;
    protected $clave;
    protected $sistema;
    protected $instalacion;
	protected $log;
	protected $atributos;
	
    function __construct($url, $usuario, $clave)
    {
        $this->url = $url;
        $this->usuario = $usuario;
        $this->clave = $clave;
		$this->establecerAtributosBasicos();
		$this->conexion = $this->crearConexion();
        if(! $this->controlServidorPoseeTipos(RDITipos::getTiposBasicos())) {
            throw new RDIExcepcion('El servidor no posee los tipos requeridos por SIU-RDI'); //Mejorar Msg
        }
    }    
	
	function atributo($id)
	{
		return $this->atributos[$id];
	}
	
	function atributos()
	{
		return $this->atributos;
	}
	
	function asociarLog($log)
	{
		$this->log = $log;
		$this->conexion->asociarLog($log);
	}
	
	function log($titulo, $texto)
	{
		if(isset($this->log)) {
			$this->log->add_debug($titulo, $texto);
		}
	}	

	function log_error($excepcion)
	{
		if(isset($this->log)) {
			$this->log->add_error($excepcion);
		}
	}
	
	abstract function establecerAtributosBasicos();
	abstract function crearConexion();
	abstract function infoConexion();
	abstract function controlServidorPoseeTipos($tiposBasicos);
	
    //--------------------------------------------------
    //-- Documentos
    //--------------------------------------------------        

    abstract function crearDocumento($idCarpeta, $nombre, $tipo, $props, $contenido, $mimeType);
	abstract function recuperarAtributos($idRecurso);
	abstract function recuperarContenido($idRecurso);
	abstract function checkOut($idRecurso);
	abstract function cancelCheckOut($idRecurso);
	abstract function checkIn($idRecurso);
    abstract function modificarContenido($idRecurso, $contenido, $mimeType);
	abstract function modificarAtributos($idRecurso, $atributos);
	abstract function eliminarDocumento($idRecurso);

    //--------------------------------------------------
    //-- Carpetas
    //--------------------------------------------------        
    
	abstract function getIdCarpeta($path);
    abstract function crearCarpeta($idPadre, $nombreDir);
	abstract function getHijosCarpeta($idPadre);

    //--------------------------------------------------
    //-- Consultas
    //--------------------------------------------------   	

	abstract function consultaDirecta($CMISQL, $mapeoColumnas, $opciones);

	/**
	 *	Utilidad para ejecutar querys desde consola
	 */
	protected static $debugEchoQuery = false;	
	static function debugEchoQuerys()
	{
		self::$debugEchoQuery = true;
	}
}

