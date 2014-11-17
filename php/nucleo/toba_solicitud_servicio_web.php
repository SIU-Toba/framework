<?php

/**
 * 
 * @package Centrales
 */
class toba_solicitud_servicio_web extends toba_solicitud
{
	private $metodo_invocado = '';
	
	static function mostrar_servicios()
	{
		$id_proyecto = toba::proyecto()->get_id();
		echo "<h2>$id_proyecto - servicios web publicados</h2>";
		$items = toba_info_editores::get_items_servicios_web($id_proyecto);
		echo "<ul>";
		foreach ($items as $item) {
			$url_servicio = toba::vinculador()->get_url_ws($id_proyecto, $item['item']);
			$url_wsdl1 = toba::vinculador()->get_url_ws($id_proyecto, $item['item'], null, array('wsdl' => true));
			$url_wsdl2 = toba::vinculador()->get_url_ws($id_proyecto, $item['item'], null, array('wsdl2' => true));			
			echo "<li><a href='$url_servicio'>{$item['item']}</a>: {$item['nombre']}. 
					<a href='$url_wsdl1'>wsdl 1.1</a> -  <a href='$url_wsdl2'>wsdl 2.0</a></li>";	
		}
		echo "</ul>";
	}
		
	function __construct($info)
	{	
		$this->info = $info;
		parent::__construct(toba::memoria()->get_item_solicitado(), toba::usuario()->get_id());	
		$this->log = toba::logger_ws();
		toba::proyecto()->configurar_logger_ws();
	}	
	
	protected function validar_componente()
	{
		$this->log->seccion("Iniciando componente...", 'toba');
		if (count($this->info['objetos']) == 1) {
			//$i = 0;
			foreach ($this->info['objetos'] as $objeto) {
				if ($objeto['clase'] != 'toba_servicio_web') {
					throw new toba_error_def("Necesita asociar a la operación un componente de clase toba_servicio_web");					
				}
			}
		} else { 
			if (count($this->info['objetos']) == 0) {
				throw new toba_error_def("Necesita asociar a la operación un componente toba_servicio_web");
			} else {
				throw new toba_error_def("Debe asociar a la operación un único componente toba_servicio_web");
			}
		}
	}	
		
	function procesar()
	{		
		toba::logger_ws()->debug('Servicio Llamado: ' . $this->info['basica']['item']);
		toba::logger_ws()->set_checkpoint();
		set_error_handler('toba_logger_ws::manejador_errores_recuperables', E_ALL);
		$this->validar_componente();
		
		//-- Pide los datos para construir el componente, WSF no soporta entregar objetos creados
		$clave = array();
		$clave['proyecto'] = $this->info['objetos'][0]['objeto_proyecto'];
		$clave['componente'] = $this->info['objetos'][0]['objeto'];
		list($tipo, $clase, $datos) = toba_constructor::get_runtime_clase_y_datos($clave, $this->info['objetos'][0]['clase'], false);
		
		agregar_dir_include_path(toba_dir().'/php/3ros/wsf');
		
		$opciones_extension = toba_servicio_web::_get_opciones($this->info['basica']['item'], $clase);
		$wsdl = (strpos($_SERVER['REQUEST_URI'], "?wsdl") !== false);
		
		$sufijo = 'op__';
		$metodos = array();
		$reflexion = new ReflectionClass($clase);
		foreach($reflexion->getMethods() as $metodo) {
			if (strpos($metodo->name, $sufijo) === 0) {
				$servicio = substr($metodo->name, strlen($sufijo));
				$prefijo = $wsdl ? '' : '_';
				$metodos[$servicio] = $prefijo.$metodo->name;
			}	
		}
		$opciones = array();
		$opciones['serviceName'] = $this->info['basica']['item']; 
		$opciones['classes'][$clase]['operations'] = $metodos;
		$opciones = array_merge($opciones, $opciones_extension);
		$this->log->debug("Opciones del servidor: ".var_export($opciones, true), 'toba');
		$opciones['classes'][$clase]['args'] = array($datos);		
		toba::logger_ws()->set_checkpoint();
		$service = new WSService($opciones);
		$service->reply();
		$this->log->debug("Fin de servicio web", 'toba');
	}
	
	function set_metodo_invocado($metodo)
	{
		$this->metodo_invocado = $metodo;
	}
	
	function registrar()
	{
		parent::registrar();
		if ($this->registrar_db) {
			toba::instancia()->registrar_solicitud_web_service(	$this->info['basica']['item_proyecto'], 
															$this->id, 
															$this->metodo_invocado,		//Metodo que se llama
															$_SERVER['REMOTE_ADDR']);
		}
 	}
}
?>
