<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 

abstract class ci_editores_toba extends objeto_ci
{
	protected $id_objeto;
	protected $db_tablas;
	protected $cambio_objeto;

	function __construct($id)
	{
		parent::__construct($id);
		//Cargo el editable de la zona		
		$zona = toba::get_solicitud()->zona();
		if ($editable = $zona->obtener_editable_propagado()){
			$zona->cargar_editable(); 
			list($proyecto, $objeto) = $editable;
		}		
		if (isset($objeto) && isset($proyecto)) {
			$this->cambio_objeto = true;
			$this->set_objeto( 	array(	'proyecto'=>$proyecto, 'objeto'=>$objeto) );
		}
	}
	
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "db_tablas";
		$propiedades[] = "id_objeto";
		return $propiedades;
	}	
	
	function set_objeto($id)
	{
		$this->id_objeto = 	$id;
	}
	
	abstract function get_dbt();
	
	function generar_interface_grafica()
	{
		$zona = toba::get_solicitud()->zona();
		$zona->obtener_html_barra_superior();
		parent::generar_interface_grafica();
		$zona->obtener_html_barra_inferior();
	}
		
}


?>