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
			$this->guzzle = $client = new GuzzleHttp\Client(['base_url' => $this->opciones['to']]);

			if (isset($this->opciones['auth_tipo'])) {
				$this->guzzle->setDefaultOption('auth',
						array(	$this->opciones['auth_usuario'], 
								$this->opciones['auth_password'], 
								$this->opciones['auth_tipo']));
			}		
		}
		return $this->guzzle;	
	}

	static function get_version_api($response)
	{		
		if ($response->hasHeader(self::HEADER_VERSION)) {
			$string_version = $response->getHeader(self::HEADER_VERSION);
			return new toba_version($string_version);
		} else {
			return null;
		}
	}
}
