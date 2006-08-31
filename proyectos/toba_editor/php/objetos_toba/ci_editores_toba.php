<?php
require_once('nucleo/componentes/interface/toba_ci.php'); 
require_once('admin_util.php');

abstract class ci_editores_toba extends toba_ci
{
	protected $id_objeto;
	protected $cambio_objeto;
	protected $cargado = false;
	protected $etapa_particular;
	private $falla_carga = false;
	private $elemento_eliminado = false;

	function ini()
	{
		//Cargo el editable de la zona		
		$zona = toba::solicitud()->zona();
		if ($editable = $zona->get_editable()){
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
			} else {
				$this->cambio_objeto = false;	
			}
		}
		//Llegada a un TAB especifico desde el arbol
		$etapa = toba::hilo()->obtener_parametro('etapa');
		if( isset($etapa) ) $this->set_pantalla($etapa);
		//Llegada desde un evento
		$evento = toba::hilo()->obtener_parametro('evento');
		if (isset($evento)) {
			$this->set_pantalla(3);
			$this->dependencia('eventos')->set_evento_editado($evento);
		}		
	}
	
	function get_entidad()
	//Acceso al DATOS_RELACION
	{
		if($this->cambio_objeto && !$this->falla_carga){
			toba::logger()->debug($this->get_txt() . '*** se cargo la relacion: ' . $this->id_objeto['objeto']); 	
			if( $this->dependencia('datos')->cargar( $this->id_objeto ) ){
				$this->cargado = true;
				$this->cambio_objeto = false;//Sino sigue entrando aca por cada vez que se solicita la entidad
			}else{
				toba::notificacion()->agregar("El elemento seleccionado no existe.","error");
				$this->falla_carga = true;	
			}
		}		
		return $this->dependencia('datos');
	}

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "id_objeto";
		$propiedades[] = "cargado";
		return $propiedades;
	}	
	
	function set_objeto($id)
	{
		$this->id_objeto = 	$id;
	}

	function conf()
	{
		if(!$this->cargado){
			$this->pantalla()->eliminar_evento('eliminar');
		}
	}

	function evt__eliminar()
	{
		$this->get_entidad()->eliminar();
		$this->elemento_eliminado = true;
		$zona = toba::solicitud()->zona();
		$zona->resetear();
		toba::notificacion()->agregar("El elemento ha sido eliminado.", "error");		
		admin_util::refrescar_editor_item();
	}
	

	//*******************************************************************
	//*****************  PROPIEDADES BASICAS  ***************************
	//*******************************************************************

		
	function conf__base($form)
	{
		if (! in_array($this->get_clase_actual(), dao_editores::get_clases_con_fuente_datos())) {
			//Oculta la fuente
			$form->desactivar_efs(array('fuente_datos'));
		}
		$reg = $this->get_entidad()->tabla("base")->get();
		if (!isset($reg)) {
			//--- Si es un nuevo objeto, se sugiere un nombre para el mismo
			$nombre = "";
			if (isset($this->controlador)
					 && method_exists($this->controlador, 'get_nombre_destino')
					 && $this->controlador->hay_destino()) {
				$nombre_dest = $this->controlador->get_nombre_destino();				 	
				if ($this->controlador->destino_es_item()) {
					$nombre = $nombre_dest;
				} else {
					$nombre = "$nombre_dest - ".$this->controlador->get_nombre_rol();	
				}
			} else {
				$nombre = $this->get_abreviacion_clase_actual();				
			}
			$reg = array();
			$reg['nombre'] = $nombre;
		}
		return $reg;
	}

	function evt__base__modificacion($datos)
	{
		if (!isset($datos['fuente_datos'])) {
			$datos['fuente_datos'] = NULL;	
		}
		if (!isset($datos['fuente_datos_proyecto'])) {
			$datos['fuente_datos_proyecto'] = NULL;	
		}
		$this->get_entidad()->tabla("base")->set($datos);
	}
	
	// *******************************************************************
	// *******************  PROCESAMIENTO  *******************************
	// *******************************************************************
	
	function evt__procesar()
	{
		if (!$this->cargado) {
			//Seteo los datos asociados al uso de este editor
			$fijos = array('proyecto' => toba_editor::get_proyecto_cargado(),
							'clase_proyecto' => 'toba',
							'clase' => $this->get_clase_actual());
			$this->get_entidad()->tabla('base')->set($fijos);
		}
		//Sincronizo el DBT
		$this->get_entidad()->sincronizar();
	}
	
	//---------------------------------------------------------------
	//-------------------------- Consultas --------------------------
	//---------------------------------------------------------------

	function get_clase_actual()
	{
		if (isset($this->clase_actual)) {
			return $this->clase_actual;
		} else {
			throw new toba_excepcion("El editor actual no tiene definida sobre que clase de objeto trabaja");
		}
	}
	
	function get_abreviacion_clase_actual()
	{
		$tipo = catalogo_toba::convertir_tipo( $this->get_clase_actual() );
		$clase = catalogo_toba::get_nombre_clase_definicion($tipo);
		return call_user_func(array($clase, "get_tipo_abreviado"));
	}
		
	/*
		Todos los EI que tienen un tab de eventos necesitan implementar estos metodos.
		Actualmente solo se utilizan en el CI
	*/
	function eliminar_evento($id){}
	function modificar_evento($id_anterior, $id_nuevo){}
	
}
?>