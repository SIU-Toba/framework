<?php

/**
 * 
 * @package Centrales
 */
class toba_solicitud_servicio_web extends toba_solicitud
{

	function __construct($info)
	{	
		$this->info = $info;
		parent::__construct(toba::memoria()->get_item_solicitado(), toba::usuario()->get_id());		
	}	
	
	function procesar()
	{
		if (isset($this->info['basica']['item']) && $this->info['basica']['item'] == 'serv_pruebas') {
			
			agregar_dir_include_path(toba_dir().'/php/3ros/wsf');

			
			$opciones = array("classes" => array("toba_referencia_servicios_prueba" => array("operations" => array('eco'))));
			$service = new WSService($opciones);
			$payload = '<soapenv:Envelope xmlns:soapenv="http://www.w3.org/2003/05/soap-envelope"><soapenv:Header/><soapenv:Body>';
			$payload .= toba_referencia_servicios_prueba::get_payload();
			$payload .= '</soapenv:Body></soapenv:Envelope>';
			$service->reply($payload);
		}		
	}
}

abstract class toba_servicio_web_base
{
	function __construct()
	{
		
	}
	
	/**
	 * Payload de ejemplo para la publicación
	 */
	abstract static function get_payload();
}


class toba_referencia_servicios_prueba extends toba_servicio_web_base
{

	static function get_payload()
	{
		return '<ns1:eco xmlns:ns1="http://siu.edu.ar/toba_referencia"><text>Hola!</text></ns1:eco>';
	}
	
	/** 
	 * Servicio de eco
	 * @param string $mensaje El mensaje a repetir
	 * @return string $salida Mensaje repetido
	 */	
	function eco($mensaje) {
	    $salida = new WSMessage($mensaje->str);
	    return $salida;
	}
	
	
}

?>