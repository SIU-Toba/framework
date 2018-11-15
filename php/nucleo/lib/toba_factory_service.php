<?php

use SIU\InterfacesManejadorSalidaToba\IFactory;

class toba_factory_service 
{
	//private static $_instance;
	private static $_services;
	private static $_config = [
				'PaginaBasica' => 'toba',
				'PaginaTitulo' => 'toba',
				'PaginaNormal' => 'toba',
				'PaginaPopup' => 'toba',
				'PaginaLogon' => 'toba',
				'ElementoInterfaz' => 'toba',
				'Pantalla' => 'toba',
				'Cuadro' => 'toba',
				'CuadroSalidaHtml' => 'toba',
				'Formulario' => 'toba',
				'FormularioMl' => 'toba',
				'EventoUsuario' => 'toba',
				'EventoTab' => 'toba',
				'InputsForm' => 'toba',
				'FiltroColumnas' => 'toba',
				'Menu' => 'toba',
				'Filtro' => 'toba'	];
	
	/**
	 * Devuelve una instancia de un componente
	 * @param string $component nombre del servicio que se desea obtener
	 * @param boolean $default Fuerza el uso del provider por defecto (toba)
	 * @return object instancia del objeto solicitado
	 */	
	function get($component, $default = false)
	{		
		$proyecto = toba_proyecto::get_id();		
		$provider = ($default || $proyecto == 'toba_editor')? 'toba' : self::$_config[$component];
		return new self::$_services[$provider][$component];
	}
	
	/**
	 * Fuerza un provider especifico para un componente
	 * @param string $componet
	 * @param string $provider
	 */
	function setComponentProvider($componet, $provider)
	{
		self::$_config[$componet] = $provider;
	}
	
	/**
	 * Setea el provider activo, fuerza a todos los componentes.
	 * @param string $provider
	 */
	function setProvider($provider)
	{
		foreach (self::$_config as $key => $value) {
			self::$_config[$key] = $provider;
		}
	}
	
	/**
	 * Registra una factory como provider de servicios
	 * @param IFactory $fabrica
	 * @throws toba_error
	 */
	function registrarServicio(IFactory $fabrica)
	{		
		if( ! $fabrica instanceof IFactory) {
			throw new toba_error("El servicio a registrar debe ser una implementación de IFactory");
		}
		
		/* @todo Agregar validacion de nombre */
		$nombre_fabrica = $fabrica->getProvider();
		
		//Registra los componentes implementados y deja el default anterior como fallback para los no implementados
		foreach (get_class_methods($fabrica) as $method) {
			$componente = substr($method, 3); //Le quito el 'get' para obtener el nombre			
			if ($implementacion = $fabrica->$method() != null ) {
				self::$_services[$nombre_fabrica][$componente] = $fabrica->$method();
				//echo self::$_services[$nombre_fabrica][$componente] . PHP_EOL; 
				$this->setComponentProvider($componente, $nombre_fabrica);
			}
		}
	}

	/**
	 * Devuelve la lista de componentes y su provider actual
	 * @return array
	 */
	static function getListaComponentes()
	{
		return self::$_config;
	}
}
