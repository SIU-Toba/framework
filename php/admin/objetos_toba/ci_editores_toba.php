<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
require_once('admin/admin_util.php');

abstract class ci_editores_toba extends objeto_ci
{
	protected $id_objeto;
	protected $cambio_objeto;
	protected $cargado = false;
	protected $falla_carga = false;
	private $elemento_eliminado = false;

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
		//Necesito cargar la entidad antes de mostrar la pantalla
	}
	
	function get_entidad()
	//Acceso al DATOS_RELACION
	{
		if (! isset($this->dependencias['datos'])) {
			$this->cargar_dependencia('datos');
		}
		if($this->cambio_objeto && !$this->cargado && !$this->falla_carga){
			toba::get_logger()->debug($this->get_txt() . '*** se cargo la relacion: ' . $this->id_objeto['objeto']); 	
			if( $this->dependencias['datos']->cargar( $this->id_objeto ) ){
				$this->cargado = true;
			}else{
				$this->falla_carga = true;	
			}
		}		
		return $this->dependencias['datos'];
	}

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "id_objeto";
		$propiedades[] = "cargado";
		$propiedades[] = "falla_carga";
		return $propiedades;
	}	
	
	function set_objeto($id)
	{
		$this->id_objeto = 	$id;
	}
	
	function generar_interface_grafica()
	{
		$this->get_entidad();
		if($this->falla_carga === true){
			echo ei_mensaje("El elemento seleccionado no existe.","error");
			return;
		}
		if($this->elemento_eliminado){
			echo ei_mensaje("El elemento ha sido eliminado.");
			return;
		}
		$zona = toba::get_solicitud()->zona();
		if (isset($zona) && isset($this->id_objeto)) {
			$zona->obtener_html_barra_superior();
		}
		parent::generar_interface_grafica();
		if (isset($zona) && isset($this->id_objeto)) {
			$zona->obtener_html_barra_inferior();
		}	
	}

	function get_lista_eventos()
	{
		$eventos = parent::get_lista_eventos();
		if(!$this->cargado){
			unset($eventos['eliminar']);
		}
		return $eventos;
	}	

	function evt__eliminar()
	{
		$this->get_entidad()->eliminar();
		$this->elemento_eliminado = true;
		admin_util::refrescar_editor_item();
	}
}
?>