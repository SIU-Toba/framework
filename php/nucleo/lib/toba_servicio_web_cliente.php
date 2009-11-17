<?php

class toba_servicio_web_cliente
{
	static protected $ini;	
	protected $wsf;
	protected $opciones;
	
	/**
	 * @return toba_servicios_web
	 */
	static function conectar($id_servicio)
	{
		$path_ini = toba::nucleo()->toba_instalacion_dir().'/servicios_web.ini';		
		if (! isset(self::$ini)) {
			if (! file_exists($path_ini)) {
				throw new toba_error_def("No existe el archivo '$path_ini'");
			}			
			self::$ini = new toba_ini($path_ini);			
		}
		if (! self::$ini->existe_entrada($id_servicio)) {
			throw new toba_error_def("No existe la entrada '$id_servicio' en el archivo '$path_ini'");
		}		
		$opciones = self::$ini->get_datos_entrada($id_servicio);
		if (! isset($opciones['to'])) {
			throw new toba_error_def("Debe indicar la URL destino en el campo 'to'");			
		}		
		$servicio = new toba_servicio_web_cliente($opciones);
		return $servicio;
	}
	
	function __construct($opciones) 
	{
		$this->opciones = $opciones;
		$this->wsf = new WSClient($this->opciones);
	}
	
	function request($payload)
	{
		try {
			return $this->wsf->request($payload);
		} catch (WSFault $fault) {
			throw new toba_error_comunicacion($fault->__toString(), $this->opciones, $this->wsf->getLastResponseHeaders());
		} catch (Exception $e) {
			throw new toba_error_comunicacion($e->getMessage(), $this->opciones, $this->wsf->getLastResponseHeaders());			
		}
	}
	
	/**
	 * @return WSClient
	 */
	function wsf()
	{
		return $this->wsf;	
	}
	
}


?>