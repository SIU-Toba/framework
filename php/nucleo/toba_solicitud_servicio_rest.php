<?php

/**
 * Solicitud pensada para ejecutar las llamadas a WS REST
 * @package Centrales
 */
class toba_solicitud_servicio_rest extends toba_solicitud
{
	private $metodo_invocado = '';
	private $toba_rest;
	private $app;
			
	function __construct()
	{	
		$this->info = array('objetos' => array(), 
					      'basica' => array('item_proyecto' => apex_pa_proyecto, 
									'item' => 0, 
									'item_solic_tipo' => 'rest', 
									'item_nombre' => 'rest'));
		$item = array(apex_pa_proyecto, 0);
		parent::__construct($item, 'rest');
		$this->set_metodo($_SERVER['REQUEST_METHOD']);
		$this->toba_rest = new toba_rest();
	}	
	
	protected function validar_componente()
	{
		//Ignora la validacion, de eso se encarga la libreria REST, ademas no hay componentes aca
	}	
		
	function procesar()
	{		
		$en_mantenimiento = (toba::proyecto()->get_parametro('proyecto', 'modo_mantenimiento', false) == 1) ;
		if ($en_mantenimiento) {			
			header(sprintf('HTTP/%s %s', '1.1', '503 Service Unavailable'), true, 503);
			$msg = toba::proyecto()->get_parametro('proyecto', 'mantenimiento_mensaje');
			echo rest_encode($msg);
			return;
		}		
		$nombre_api = '';	// esto esta en $this->recuperar_nombre_api();
		if (! $this->toba_rest->es_pedido_documentacion()) {
			$this->toba_rest->conf__inicial($nombre_api);
			$this->app = $this->toba_rest->get_instancia_rest();
			if (method_exists(toba::contexto_ejecucion(), "conf__rest")) {
				toba::contexto_ejecucion()->conf__rest($this->app);
			}
		}
		$this->toba_rest->ejecutar();
	}
	
	function get_app()
	{
		return $this->app;
	}

	function set_metodo($metodo)
	{
		$this->metodo_invocado = $metodo;
	}
	
	function registrar()
	{
		parent::registrar();
		if ($this->registrar_db) {
			toba::instancia()->registrar_solicitud_web_service(	$this->info['basica']['item_proyecto'], 
															$this->id, 
															$this->metodo_invocado . ' ' .toba_http::get_uri(),		//Esto quedaria algo asi POST /alias_proyecto/rest/servicio
															$_SERVER['REMOTE_ADDR']);
		}
 	}
	
	protected function recuperar_nombre_api()
	{
		if (isset($_SERVER['REDIRECT_API_NOMBRE'])) {
			return $_SERVER['REDIRECT_API_NOMBRE'];
		}
		return '';
	}
}
?>
