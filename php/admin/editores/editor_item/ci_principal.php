<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
require_once('admin/db/toba_dbt.php');
//----------------------------------------------------------------

class ci_principal extends objeto_ci
{
	protected $cambio_item;
	protected $id_item;
	protected $db_tablas;
	
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
		if ($this->cambio_item){
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
		//Ver si el padre viene por post
		$padre_i = toba::get_hilo()->obtener_parametro('padre_i');
		$padre_p = toba::get_hilo()->obtener_parametro('padre_p');

		//¿Es un item nuevo?
		if (isset($padre_p) && isset($padre_i)) {
			//Se resetea el dbt para que no recuerde datos anteriores
			$this->get_dbt()->resetear();
			//Para el caso del alta el id es asignado automáticamente 
			$datos = array('item' => "<span style='white-space:nowrap'>A asignar</span>");
			$datos['padre'] = $padre_i;
			$datos['padre_proyecto'] = $padre_p;

		} else {
			$datos = $this->get_dbt()->elemento("base")->get();
		}
	
		//Transfiere los campos accion, buffer y patron a uno comportamiento
		if (isset($datos['actividad_accion']) && $datos['actividad_accion'] != '') {
			$datos['comportamiento'] = 'accion';
		}
		if (isset($datos['actividad_buffer']) && $datos['actividad_buffer'] != 0) {
			$datos['comportamiento'] = 'buffer';
		}
		if (isset($datos['actividad_patron']) && $datos['actividad_patron'] != 'especifico') {
			$datos['comportamiento'] = 'patron';
		}
		return $datos;
	}

	function evt__prop_basicas__modificacion($registro)
	{
		//El campo comportamiento incide en el buffer, patron y accion
		switch ($registro['comportamiento'])
		{
			case 'accion':
				$registro['actividad_buffer'] = 0;
				$registro['actividad_patron'] = 'especifico';
				break;
			case 'buffer':
				$registro['actividad_accion'] = '';
				$registro['actividad_patron'] = 'especifico';				
				break;
			case 'patron':
				$registro['actividad_buffer'] = 0;
				$registro['actividad_accion'] = '';
				break;								
		}
		unset($registro['comportamiento']);
		$this->get_dbt()->elemento("base")->set($registro);
	}
	
	//----------------------------- permisos -----------------------------	
	function evt__permisos__carga()
	{
		$permisos = $this->get_dbt()->elemento('permisos');
		if (isset($permisos)) {
			if ($datos = $permisos->get_registros()) {
				foreach ($datos as $id => $dato) {
					$datos[$id]['nombre'] = 'COMO ';
				}
				ei_arbol($datos);
			}
			return $datos;
		}
	}
	
	// *******************************************************************
	// *******************  PROCESAMIENTO  *******************************
	// *******************************************************************
	
	function evt__procesar()
	{
		//Seteo los datos asociados al uso de este editor
		$this->get_dbt()->elemento('base')->set_registro_valor(0,"proyecto",toba::get_hilo()->obtener_proyecto() );
		//Sincronizo el DBT
		$this->get_dbt()->sincronizar();		
	}

	function evt__eliminar()
	{
		$this->get_dbt()->eliminar();
	}
	// *******************************************************************	


}

?>