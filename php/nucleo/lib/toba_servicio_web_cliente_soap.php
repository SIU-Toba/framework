<?php

class toba_servicio_web_cliente_soap extends toba_servicio_web_cliente
{
	protected $wsf;

	
	function __construct($opciones, $id_servicio, $proyecto = null) 
	{
        parent::__construct($opciones, $id_servicio, $proyecto);
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
			if (! toba::instalacion()->es_produccion()) {
				toba::logger()->debug("Request: " . var_export($this->wsf->getLastRequest(), true));
				toba::logger()->debug("Response: " . var_export($this->wsf->getLastResponse(), true));
				toba::logger()->var_dump($this->wsf->getLastResponseHeaders());
			}
			
			//-- INICIO PARCHE: Intenta parsear un Fault por bug en libreria WSF con esquema de seguridad..
			if (is_a($message, 'WSMessage')) {
				$inicio = "<soapenv:Fault";
				if (substr($message->str, 0, strlen($inicio)) == $inicio) {
					$xml = new SimpleXMLElement($message->str);
					$ns = $xml->getDocNamespaces(true);
					$childrens = $xml->children($ns['soapenv']);
					$code = @(string) $childrens->Code->Value;
					$reason = @(string) $childrens->Reason->Text;
					$detail = @(string) $childrens->Detail->children($ns['soapenv']->children, true)->error;
					throw new WSFault(str_replace("soapenv:", "", $code), $reason, null, $detail);
				}
			}
			//--- FIN PARCHE
			
			return new toba_servicio_web_mensaje($message);
		} catch (WSFault $fault) {	
			if (! toba::instalacion()->es_produccion()) {
				toba::logger()->debug("Request: " . var_export($this->wsf->getLastRequest(), true));
				toba::logger()->debug("Response: " . var_export($this->wsf->getLastResponse(), true));
				toba::logger()->var_dump($this->wsf->getLastResponseHeaders());
			}			
			$detalle = (isset($fault->Detail)) ? $fault->Detail: '';	
			$code = (isset($fault->Code)) ? $fault->Code: '';	
			self::get_modelo_proyecto($this->proyecto);
			throw new toba_error_servicio_web($fault->Reason, $detalle, $code);
		} catch (Exception $e) {
			if (! toba::instalacion()->es_produccion()) {
				toba::logger()->debug("Request: " . var_export($this->wsf->getLastRequest(), true));
				toba::logger()->debug("Response: " . var_export($this->wsf->getLastResponse(), true));
				toba::logger()->var_dump($this->wsf->getLastResponseHeaders());
			}			
			throw new toba_error_comunicacion($e->getMessage(), $this->opciones, $this->wsf->getLastResponseHeaders());			
		}
	}
	
	function send(toba_servicio_web_mensaje $mensaje)
	{
		try {
			$this->wsf->send($mensaje->wsf());
		} catch (WSFault $fault) {
			self::get_modelo_proyecto($this->proyecto);
			toba::logger()->debug("Request: " .$this->wsf->getLastRequest());
			toba::logger()->debug("Response: " .$this->wsf->getLastResponse());
			$detalle = (isset($fault->Detail)) ? $fault->Detail: '';	
			throw new toba_error_servicio_web($fault->Reason, $fault->Code, $detalle);
		} catch (Exception $e) {
			throw new toba_error_comunicacion($e->getMessage(), $this->opciones, $this->wsf->getLastResponseHeaders());			
		}
	}
	
}
?>