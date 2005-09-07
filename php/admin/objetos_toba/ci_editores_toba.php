<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 

abstract class ci_editores_toba extends objeto_ci
{
	protected $id_objeto;
	protected $cambio_objeto;
	private $cargado = false;

	function __construct($id)
	{
		parent::__construct($id);
		//Cargo el editable de la zona		
		$zona = toba::get_solicitud()->zona();
		if (isset($zona) && $editable = $zona->obtener_editable_propagado()){
			$zona->cargar_editable(); 
			list($proyecto, $objeto) = $editable;
		}	
		//Se notifica un objeto y un proyecto	
		if (isset($objeto) && isset($proyecto)) {
			//Se determina si es un nuevo objeto
			$es_nuevo = (!isset($this->id_objeto) || 
						($this->id_objeto['proyecto'] != $proyecto || $this->id_objeto['objeto'] != $objeto));
			if ($es_nuevo) {
				$this->set_objeto( 	array('proyecto'=>$proyecto, 'objeto'=>$objeto) );
				$this->cambio_objeto = true;
			}
		}
	}
	
	function get_entidad()
	//Acceso al DATOS_RELACION
	{
		if (! isset($this->dependencias['datos'])) {
			$this->cargar_dependencia('datos');
		}
		if($this->cambio_objeto && !$this->cargado ){
			toba::get_logger()->debug($this->get_txt() . '*** se cargo la relacion: ' . $this->id_objeto['objeto']); 	
			$this->dependencias['datos']->cargar( $this->id_objeto );
			$this->cargado = true;
		}		
		return $this->dependencias['datos'];
	}

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "id_objeto";
		return $propiedades;
	}	
	
	function set_objeto($id)
	{
		$this->id_objeto = 	$id;
	}
	
	function generar_interface_grafica()
	{
		$zona = toba::get_solicitud()->zona();
		if (isset($zona) && isset($this->id_objeto)) {
			$zona->obtener_html_barra_superior();
		}
		parent::generar_interface_grafica();
		$zona = toba::get_solicitud()->zona();
		if (isset($zona) && isset($this->id_objeto)) {
			$zona->obtener_html_barra_inferior();
		}	
	}
}
?>