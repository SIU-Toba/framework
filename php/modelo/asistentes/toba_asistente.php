<?php
/*
*	El ASISTENTE carga un PLAN y lo utiliza para generar el MOLDE de una operacion.
*/
class toba_asistente
{
	protected $id_plan_proyecto;
	protected $id_plan;
	protected $item;		// Molde del item
	protected $ci;			// Shortcut al molde del CI
	
	
	function __construct($definicion)
	{
		$this->id_plan_proyecto = $definicion['_info']['proyecto'];
		$this->id_plan = $definicion['_info']['plan'];
		//Cargo las variables internas que forman la definicion
		foreach (array_keys($definicion) as $parte) {
			$this->_definicion_partes[] = $parte;
			$this->$parte = $definicion[$parte];
		}
		$a = new toba_item_molde();
		//$this->item = new toba_item_molde();
		//$this->ci = $this->item->ci();
	}	
	
	//---------------------------------------------------
	//-- GUARDAR 
	//---------------------------------------------------

	/**
	* Se crea el molde 
	*/
	function generar_molde()
	{
		$this->generar_base();
		$this->generar();
	}

	/**
	*	Crea el item universal
	*/
	function generar_base()
	{
		$this->item->set_nombre($this->_info['nombre']);
		$this->item->set_carpeta_item($this->_info['carpeta_item']);
		$this->item->set_carpeta_archivos($this->_info['carpeta_archivos']);
	}

	protected function generar()
	{
		throw new toba_error('ASISTENTE: no se definio una ejecucion del plan');
	}

	//---------------------------------------------------
	//-- GUARDAR 
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
		//Abre transaccion
		$this->item->generar();
		$this->log_elementos_generados();
		//Cerrar transaccion
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

	protected function log_elementos_generados()
	{
		//Se guarda el resultado de la generacion
		//$this->item->get_ids_generados();
	}
}
?>