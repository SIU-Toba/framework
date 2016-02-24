<?php

class toba_servicio_web_cliente_rest extends toba_servicio_web_cliente
{
	const HEADER_VERSION = 'API-Version';
	protected $guzzle;
	
	function __construct($opciones, $id_servicio, $proyecto = null) 
	{
		parent::__construct($opciones, $id_servicio, $proyecto);
	}

	static function conectar($id_servicio, $opciones=array(), $proyecto = null)
	{
		if (! isset($proyecto)) {
			$proyecto = toba_editor::activado() ? toba_editor::get_proyecto_cargado() : toba::proyecto()->get_id();
		}
		self::get_modelo_proyecto($proyecto);
		$ini = toba_modelo_rest::get_ini_cliente(self::$modelo_proyecto, $id_servicio);
		$opciones_ini = $ini->get_datos_entrada('conexion');

		//Convierte todos los '1' de texto en true
		foreach (array_keys($opciones_ini) as $id_opcion) {
			if ($opciones_ini[$id_opcion] === '1' || $opciones_ini[$id_opcion] === 1) {
				$opciones_ini[$id_opcion] = true;
			}
		}

		//-- Mezcla con las opciones recibidas y crea el objeto
		$opciones = array_merge($opciones_ini, $opciones);
		if (! isset($opciones['to'])) {
			throw new toba_error_def("Debe indicar la URL destino en el campo 'to'");
		}

		toba::logger()->debug("Invocando servicio $id_servicio. Opciones:<br>". var_export($opciones, true));
		$servicio = new toba_servicio_web_cliente_rest($opciones, $id_servicio);
		return $servicio;
	}
	
	/**
	 * @return GuzzleHttp\Client
	 */
	function guzzle()
	{
		if (! isset($this->guzzle)) {
			$options = array('base_uri' => $this->opciones['to']);
			if (isset($this->opciones['auth_tipo'])) {
				if ($this->opciones['auth_tipo'] != 'ssl') {
					$options['auth'] = array($this->opciones['auth_usuario'], 	$this->opciones['auth_password'], $this->opciones['auth_tipo']);
				} else {					
					if (isset($this->opciones['cert_file'])) {								//Busco el certificado del cliente
						$options['cert'] = array(realpath($this->opciones['cert_file']));
					}
					if (isset($this->opciones['cert_pwd'])) {								//Si requiere un pwd se lo agrego
						$options['cert'][] = $this->opciones['cert_pwd'];
					}
					if (isset($this->opciones['key_file'])) {
						$options['ssl_key'] = realpath($this->opciones['key_file']);
					}					
					//Agrego el certificado de la CA para verificar el certificado del servidor
					$options['verify'] = (isset($this->opciones['ca_cert']))? array($this->opciones['ca_cert']) : false;					
				}
			}
			toba::logger()->var_dump($options);
			$this->guzzle = $client = new GuzzleHttp\Client($options);
		}
		return $this->guzzle;	
	}

	static function get_version_api($response)
	{		
		if ($response->hasHeader(self::HEADER_VERSION)) {
			$string_version = $response->getHeader(self::HEADER_VERSION);			
			return new toba_version(current($string_version));
		} else {
			return null;
		}
	}
}
