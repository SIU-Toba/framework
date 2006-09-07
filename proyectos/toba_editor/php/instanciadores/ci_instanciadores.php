<?php
require_once('nucleo/componentes/interface/toba_ci.php'); 

abstract class ci_instanciadores extends toba_ci
{
	protected $id_objeto;

	function __construct($id)
	{
		parent::__construct($id);
		//Cargo el editable de la zona		
		$zona = toba::solicitud()->zona();
		if (isset($zona) && $editable = $zona->get_editable()){
			list($proyecto, $objeto) = $editable;
		}	
		//Se notifica un objeto y un proyecto	
		if (isset($objeto) && isset($proyecto)) {
			$this->id_objeto =  array('proyecto'=>$proyecto, 'objeto'=>$objeto);
			$this->cargar_objeto();
		}
	}

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "id_objeto";
		return $propiedades;
	}	
	
	function obtener_descripcion_pantalla($pantalla)
	{
		switch ($pantalla) {
			case 'simulacion':
				$nombre = toba::solicitud()->zona->editable_info['nombre'];
				$des = "Simulando la ejecución de <strong>$nombre</strong>";
				break;
			default:
				$des = parent::obtener_descripcion_pantalla($pantalla);
		}
		return $des;
	}	
	
	function cargar_objeto()
	{
		$this->agregar_dependencia('objeto', $this->id_objeto['proyecto'], $this->id_objeto['objeto']);		
	}

	
	function conf()
	{
		$this->pantalla()->agregar_dep('objeto');
	}
	
	function generar_interface_grafica()
	{
		$zona = toba::solicitud()->zona();
		if (isset($zona) && isset($this->id_objeto)) {
			$zona->generar_html_barra_superior();
		}
		parent::generar_interface_grafica();
		if (isset($zona) && isset($this->id_objeto)) {
			$zona->generar_html_barra_inferior();
		}	
	}

}
?>