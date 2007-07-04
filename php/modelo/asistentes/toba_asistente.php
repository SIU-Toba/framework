<?php
/*
*	El ASISTENTE lee un PLAN para generar un MOLDE con el cual crear una OPERACION.
*/
class toba_asistente
{
	protected $id_plan_proyecto;
	protected $id_plan;
	protected $item;		// Molde del item
	protected $ci;			// Shortcut al molde del CI
	protected $log_elementos_creados;
	
	function __construct($plan)
	{
		$this->id_plan_proyecto = $plan['_info']['proyecto'];
		$this->id_plan = $plan['_info']['plan'];
		//Cargo el plan
		foreach (array_keys($plan) as $parte) {
			$this->$parte = $plan[$parte];
		}
		$this->crear_elementos_basicos();
	}	
	
	function crear_elementos_basicos()
	{
		//$a = new toba_item_molde();
		$this->item = new toba_item_molde($this);
		$this->ci = $this->item->ci();
	}
	
	//---------------------------------------------------
	//-- Armar molde 
	//---------------------------------------------------

	/**
	* Se crea el molde 
	*/
	function generar_molde()
	{
		$this->generar_base();
		$this->generar();
	}

	function generar_base()
	{
		$this->item->set_nombre($this->_info['nombre']);
		$this->item->set_carpeta_item($this->_info['carpeta_item']);
	}

	protected function generar()
	{
		throw new toba_error('ASISTENTE: no se definio una ejecucion del plan');
	}

	//---------------------------------------------------
	//-- Crear la operacion 
	//---------------------------------------------------

	/**
	*	Usa el molde para generar una operacion.
	*	Hay que definir los modos de regeneracion: no pisar archivos pero si metadatos, todo nuevo, etc.
	*/
	function crear_operacion($forzar_regeneracion=false)
	{
		if(  $this->existe_generacion_previa() ) {
			if ($forzar_regeneracion) {
				$this->borrar_generacion_previa();
			} else {
				throw new toba_error('');
			}
		}
		$this->generar_elementos();
	}

	function generar_elementos()
	{
		try {
			abrir_transaccion();
			$this->item->generar();
			$this->guardar_log_elementos_generados();
			cerrar_transaccion();
		} catch (toba_error $e) {
			toba::notificacion()->agregar($e->getMessage());
			abortar_transaccion();
		}
	}

	function existe_generacion_previa()
	{
		//a nivel a archivos hay que preguntarle a la operacion que va a crear
		//Leer en this->_info_plan_resultado
		return false;	
	}

	protected function borrar_generacion_previa()
	{
		
	}

	//---------------------------------------------------
	//-- API para elementos del molde
	//---------------------------------------------------

	function get_proyecto()
	{
		return $this->id_plan_proyecto;	
	}
	
	function get_carpeta_archivos()
	{
		return $this->_info['carpeta_archivos'];
	}

	//---------------------------------------------------
	//-- LOG de elementos creados
	//---------------------------------------------------

	function registrar_elemento_creado($tipo, $proyecto, $id )
	{
		static $a = 0;
		$this->log_elementos_creados[$a]['tipo'] = $tipo;
		$this->log_elementos_creados[$a]['proyecto'] = $proyecto;
		$this->log_elementos_creados[$a]['identificador'] = $id;
		$a++;
	}

	protected function guardar_log_elementos_generados()
	{
		ei_arbol($this->log_elementos_creados);
		//Se guarda el resultado de la generacion
	}

}
?>