<?php
/*
*
*/
class toba_asistente
{
	protected $id_plan_proyecto;
	protected $id_plan;
	protected $item;		//Prototipo de la operacion que se esta creando
	
	function __construct($definicion)
	{
		$this->id_plan_proyecto = $definicion['_info']['proyecto'];
		$this->id_plan = $definicion['_info']['plan'];
		//Cargo las variables internas que forman la definicion
		foreach (array_keys($definicion) as $parte) {
			$this->_definicion_partes[] = $parte;
			$this->$parte = $definicion[$parte];
		}		
		//ei_arbol(array($this->_info, $this->_info_abms, $this->_info_abms_fila));
		$this->item = new toba_item_molde();
		$this->inicializar_operacion();
	}	

	function inicializar_operacion()
	{
		$this->item->set_nombre($this->_info['nombre']);
		$this->item->set_carpeta_item($this->_info['carpeta_item']);
		$this->item->set_carpeta_archivos($this->_info['carpeta_archivos']);
	}

	/**
	*	Hay que definir los modos de regeneracion: no pisar archivos pero si metadatos, todo nuevo, etc.
	*/
	function generar($forzar_regeneracion=false)
	{
		$this->ejecutar_plan();
		if(  $this->existe_generacion_previa() ) {
			if ($forzar_regeneracion) {
				$this->borrar_generacion_previa();
			} else {
				throw new toba_error('');
			}
		}
		$this->item->generar();
		$this->guardar_resultado();
	}

	protected function ejecutar_plan()
	{
		throw new toba_error('ASISTENTE: no se definio una ejecucion del plan');
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

	protected function guardar_resultado()
	{
		//Se guarda el resultado de la generacion
		$this->item->get_ids_generados();
	}
}
?>