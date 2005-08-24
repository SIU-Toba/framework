<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
require_once('admin/db/toba_dbt.php');
//----------------------------------------------------------------

class ci_principal extends objeto_ci
{
	protected $cambio_item;
	protected $id_item;
	
	function __construct($id)
	{
		parent::__construct($id);
		//Cargo el editable de la zona		
		$zona = toba::get_solicitud()->zona();
		if ($editable = $zona->obtener_editable_propagado()){
			$zona->cargar_editable(); 
			list($proyecto, $item) = $editable;
		}	
		//Se notifica un item y un proyecto	
		if (isset($item) && isset($proyecto)) {
			//Se determina si es un nuevo item
			$es_nuevo = (!isset($this->id_item) || 
						($this->id_item['proyecto'] != $proyecto || $this->id_item['item'] != $item));
			if ($es_nuevo) {
				$this->set_item( array('proyecto'=>$proyecto, 'item'=>$item) );
				$this->cambio_item = true;
			}
		}
		//PRUEBA
		$this->set_item( array('proyecto'=>'toba', 'item'=>'/pruebas/elemento_toba') );
	}
	
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "db_tablas";
		$propiedades[] = "id_item";
		return $propiedades;
	}	

	function get_dbt()
	//Acceso al db_tablas
	{
		if (! isset($this->db_tablas)) {
			$this->db_tablas = toba_dbt::item();
		}
		if($this->cambio_item){	
			$this->db_tablas->cargar( $this->id_item );
		}
		return $this->db_tablas;
	}	

	function set_item($id)
	{
		$this->id_item = $id;
	}
	
	function generar_interface_grafica()
	{
		$zona = toba::get_solicitud()->zona();
		if (isset($this->id_item)) {
			$zona->obtener_html_barra_superior();
		}
		parent::generar_interface_grafica();
		if (isset($this->id_item)) {		
			$zona->obtener_html_barra_inferior();
		}
	}	
	
	//-------------------------------------------------------------------
	//--- Eventos
	//-------------------------------------------------------------------

	//----------------------------- prop_basicas -----------------------------
	function evt__prop_basicas__carga()
	{
		return $this->get_dbt()->elemento("base")->get();
	}

	function evt__prop_basicas__modificacion($registro)
	{
	}


}

?>