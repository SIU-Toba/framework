<?php

class toba_servicio_web_cliente
{
	static protected $ini;	
	protected $wsf;
	protected $opciones;
	
	/**
	 * @return toba_servicios_web
	 */
	static function conectar($id_servicio, $opciones=array())
	{
		//-- Lee opciones predefinidas del .ini
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
		$opciones_ini = self::$ini->get_datos_entrada($id_servicio);
		//Convierte todos los '1' de texto en true-
		foreach (array_keys($opciones_ini) as $id_opcion) {
			if ($opciones_ini[$id_opcion] === '1') {
				$opciones_ini[$id_opcion] = true;
			}
		}		
		//-- Mezcla con las opciones recibidas y crea el objeto
		$opciones = array_merge($opciones_ini, $opciones);
		if (! isset($opciones['to'])) {
			throw new toba_error_def("Debe indicar la URL destino en el campo 'to'");			
		}		
		toba::logger()->debug("Invocando servicio $id_servicio. Opciones:<br>". var_export($opciones, true));
		$servicio = new toba_servicio_web_cliente($opciones);
		return $servicio;
	}
	
	function __construct($opciones) 
	{
		$this->opciones = $opciones;
		$this->wsf = new WSClient($this->opciones);
	}
	
	/**
	 * @return WSClient
	 */
	function wsf()
	{
		return $this->wsf;	
	}
	

	/**
	 * Envia un mensaje al servicio web y espera la respuesta
	 * @param toba_servicio_web_mensaje $mensaje
	 * @return toba_servicio_web_mensaje
	 */
	function request(toba_servicio_web_mensaje $mensaje)
	{
		try {
			$message = $this->wsf->request($mensaje->wsf());
			return new toba_servicio_web_mensaje($message);
		} catch (WSFault $fault) {
			throw new toba_error_comunicacion($fault->__toString(), $this->opciones, $this->wsf->getLastResponseHeaders());
		} catch (Exception $e) {
			throw new toba_error_comunicacion($e->getMessage(), $this->opciones, $this->wsf->getLastResponseHeaders());			
		}
	}
	
	function send(toba_servicio_web_mensaje $mensaje)
	{
		try {
			$this->wsf->send($mensaje->wsf());
		} catch (WSFault $fault) {
			throw new toba_error_comunicacion($fault->__toString(), $this->opciones, $this->wsf->getLastResponseHeaders());
		} catch (Exception $e) {
			throw new toba_error_comunicacion($e->getMessage(), $this->opciones, $this->wsf->getLastResponseHeaders());			
		}
	}
	

	
}


?>