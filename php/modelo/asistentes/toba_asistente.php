<?php
/*
*	El ASISTENTE lee un PLAN para generar un MOLDE con el cual crear una OPERACION.
*/
abstract class toba_asistente
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
	}	
	
	//-----------------------------------------------------------
	//-- Armar MOLDE: Se construye el modelo de la operacion
	//-----------------------------------------------------------

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
		$this->item = new toba_item_molde($this);
		$this->ci = $this->item->ci();
		$this->item->set_nombre($this->_info['nombre']);
		$this->item->set_carpeta_item($this->_info['carpeta_item']);
	}

	abstract function generar();

	//----------------------------------------------------------------------
	//-- Crear OPERACION: Se transforma el modelo a elementos toba concretos
	//----------------------------------------------------------------------

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
	//-- API para los elementos
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
		$this->log_elementos_creados[$a]['clave'] = $id;
		$a++;
	}

	/**
	*	Guarda el resultado de la generacion
	*/
	protected function guardar_log_elementos_generados()
	{
		$sql = "INSERT INTO apex_plan_operacion_log (proyecto, plan) VALUES ('$this->id_plan_proyecto','$this->id_plan')";
		ejecutar_fuente($sql);
		$id_generacion = recuperar_secuencia('apex_plan_operacion_log_seq');
		foreach( $this->log_elementos_creados as $elemento) {
			$sql = "INSERT INTO apex_plan_operacion_log_elementos (plan, generacion, tipo, proyecto, clave) VALUES ('$this->id_plan','$id_generacion','{$elemento['tipo']}','{$elemento['proyecto']}','{$elemento['clave']}')";
			ejecutar_fuente($sql);
		}
	}

}
?>