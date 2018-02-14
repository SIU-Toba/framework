<?php

use SIU\InterfacesManejadorSalidaToba\IFactory;

class toba_factory_service 
{
	private static $_instance;
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
								'Filtro' => 'toba'
	];
	
	/**
	 * @param string $component nombre del servicio que se desea obtener
	 * @return object instancia del objeto solicitado
	 */	
	function get($component, $default = false)
	{
		$provider = $default?'toba':self::$_config[$component];
		$proyecto = toba_proyecto::get_id();
		$provider = $proyecto == 'toba_editor'?"toba":$provider;
		return new self::$_services[$provider][$component];
	}
	
	function setComponentProvider($componet, $provider)
	{
		self::$_config[$componet] = $provider;
	}
	
	function setProvider($provider)
	{
		foreach (self::$_config as $key => $value) {
			self::$_config[$key] = $provider;
		}
	}
	
	function registrarServicio(IFactory $fabrica)
	{		
		if( ! $fabrica instanceof IFactory) {
			throw new toba_error("El servicio a registrar debe ser una implementación de IFactory");
		}
		
		/* @todo Agregar validaciï¿½n de nombre */
		$nombre_fabrica = $fabrica->getProvider();
		
		foreach (get_class_methods($fabrica) as $method) {
			$componente = substr($method, 3); //Le quito el 'get' para obtener el nombre			
			if ($implementacion = $fabrica->$method() != null ) {
				self::$_services[$nombre_fabrica][$componente] = $fabrica->$method();
				//echo self::$_services[$nombre_fabrica][$componente]; 
			}
		}				
	}	
}
