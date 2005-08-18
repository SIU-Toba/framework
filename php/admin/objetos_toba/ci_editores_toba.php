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
		$objeto = toba::get_hilo()->obtener_parametro('objeto');
		$proyecto = toba::get_hilo()->obtener_parametro('proyecto');
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
		
}


?>