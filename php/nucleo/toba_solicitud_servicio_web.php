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
	
	protected function validar_componente()
	{
		toba::logger()->seccion("Iniciando componente...", 'toba');
		if (count($this->info['objetos']) == 1) {
			$i = 0;
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
		$this->validar_componente();
		
		//-- Pide los datos para construir el componente, WSF no soporta entregar objetos creados
		$clave['proyecto'] = $this->info['objetos'][0]['objeto_proyecto'];
		$clave['componente'] = $this->info['objetos'][0]['objeto'];
		list($tipo, $clase, $datos)  = toba_constructor::get_runtime_clase_y_datos($clave, $this->info['objetos'][0]['clase'], false);
		
		$sufijo = 'op__';
		$metodos = array();
		$reflexion = new ReflectionClass($clase);
		foreach($reflexion->getMethods() as $metodo) {
			if (strpos($metodo->name, $sufijo) === 0) {
				$metodos[substr($metodo->name, strlen($sufijo))] = $metodo->name;
			}	
		}
		$opciones = array();
		$opciones['classes'][$clase]['operations'] = $metodos;
		$opciones = array_merge($opciones, call_user_func(array($clase, 'get_opciones')));
		toba::logger()->debug("Opciones del servidor: ".var_export($opciones, true), 'toba');
		$opciones['classes'][$clase]['args'] = array($datos);
		$service = new WSService($opciones);
		$service->reply();
	}
}


?>