<?php
require_once('contrib/lib/toba_nodo_basico.php');

class toba_item_perfil extends toba_nodo_basico 
{
	protected $subelementos = array();
	protected $proyecto;
	protected $id;
	protected $datos;
	protected $nivel;					//Nivel del item en el arbol de items
	protected $grupos_acceso;			//Grupos que pueden acceder al item
	protected $camino;					//Arreglo de carpetas que componen la rama en donde pertenece el item
	protected $items_hijos=array();		//Arreglo de hijos 
	protected $padre=null;				//Objeto item padre
	protected $info_extra = '';
	protected $carga_profundidad;
	protected $datos_resumidos;
	protected $solo_items = true;
	
	function __construct( $datos, $carga_profundidad=true, $datos_resumidos=false)
	{
		$this->datos = $datos;	
		$this->id = $this->datos['basica']['item'];
		$this->proyecto = $this->datos['basica']['item_proyecto'];
		$this->carga_profundidad = $carga_profundidad;
		$this->datos_resumidos = $datos_resumidos;
		if ($this->carga_profundidad) {
			//TODO: hay que ver el tema de cargar los componentes junto con que operacion se esta ejecutando.
			//Si es editar el perfil de acceso solo tiene que mostrar hasta los ITEMS.
			//$this->cargar_dependencias();
		}
	}
	
	/**
	*	Crea una rama de items comenzando por la raiz
	*	Al asumir que los niveles son pocos se hace una consulta por nivel
	*	Quedan cargado en el objeto los ancestros de la rama
	*/
	function cargar_rama()
	{
		$item_ancestro = $this;
		while (! $item_ancestro->es_raiz()) {
			$id = array('componente' => $item_ancestro->get_id_padre(), 
						'proyecto' => $item_ancestro->get_proyecto());
			//$nodo = toba_constructor::get_info($id, 'toba_item', false);
			$datos = toba_cargador::instancia()->get_metadatos_extendidos( $id, 'toba_item', null, false  );
			$nodo = new toba_item_perfil($datos, false);
			$item_ancestro->set_padre($nodo);
			$item_ancestro = $nodo;
		}
	}
	
	function set_solo_items($mostrar_items)
	{
		$this->solo_items = $mostrar_items;
	}
	
	function tiene_items_hijos()
	{
		return ($this->datos['basica']['cant_item_hijos'] > 0);
	}
	
	function get_id_padre() 
	{	
		return $this->datos['basica']['item_padre']; 
	}

	function set_padre($carpeta)
	{
		$this->padre = $carpeta;
	}
	
	function agregar_hijo($item)
	{
		$this->items_hijos[$item->get_id()] = $item;
	}
	
	function set_nivel($nivel) 
	{ 
		$this->nivel = $nivel; 
	}

	function es_carpeta() 
	{ 
		return $this->datos['basica']['carpeta']; 
	}
	
	function get_hijos()
	{
		if ($this->es_carpeta()) {
			return $this->items_hijos;
		} else {
			return $this->subelementos;
		}
	}	
	
	function set_camino($camino) 
	{
		$this->camino = $camino;
	}
	
	function es_raiz()
	{
		return $this->id == '__raiz__';	
	}
	
	function get_nombre_corto()
	{
		return $this->get_nombre();
	}
	
	function get_nombre_largo()
	{
		return $this->get_nombre();
	}
	
	function get_nombre() 
	{ 
		return $this->datos['basica']['item_nombre']; 
	}
	
	function es_hoja()
	{
		if ($this->solo_items) {
			return $this->datos['basica']['cant_items_hijos'] == 0;
		}else{
			return $this->datos['basica']['cant_items_hijos'] == 0 && $this->cant_objetos() == 0;	
		}
	}
	
	function tiene_hijos_cargados()
	{
		if ($this->es_carpeta() && ! $this->es_hoja()) {
		 	return count($this->items_hijos) == $this->datos['basica']['cant_items_hijos'];
		}
		if (!$this->es_carpeta() && ! $this->carga_profundidad) {
			return false;
		}
		return true;
	}
	
	function cant_objetos() 
	{ 
		return $this->datos['basica']['cant_dependencias']; 
	}
	
	function get_proyecto() 
	{ 
		return $this->datos['basica']['item_proyecto']; 
	}
	
	function get_tipo_solicitud() 
	{ 
		return $this->datos['basica']['solicitud_tipo']; 
	}
	
	function crono() 
	{ 
		if (isset($this->datos['crono']))
			return $this->datos['crono'] == 1; 
	}
	
	function es_de_consola()
	{
		return $this->get_tipo_solicitud() == 'consola';	
	}
	
	function es_publico() 
	{ 
		return $this->datos['basica']['publico']; 
	}

	function puede_redireccionar() 
	{ 
		return $this->datos['basica']['redirecciona']; 
	}

	function registra_solicitud()
	{ 
		if (isset($this->datos['basica']['registrar']))
			return $this->datos['basica']["registrar"]; 
	}
	
	function generado_con_wizard()
	{
		return isset($this->datos['basica']['molde']);	
	}
	
	function get_iconos()
	{
		$iconos = array();
		$img_item = null;
		if (isset($this->datos['basica']['item_imagen']) && $this->datos['basica']['item_imagen'] != ''
					&& $this->datos['basica']['item_imagen_recurso_origen'] != '') {
			if ($this->datos['basica']['item_imagen_recurso_origen'] == 'apex') {
				$img_item = toba_recurso::imagen_toba($this->datos['basica']['item_imagen']);	
			} else {
				$img_item = toba_recurso::url_proyecto($this->datos['basica']['item_proyecto']).'/img/'.
								$this->datos['basica']['item_imagen'];
			}
		}
		if ($this->es_carpeta()) {
			$iconos[] = array(
				'imagen' => isset($img_item) ? $img_item : toba_recurso::imagen_toba("nucleo/carpeta.gif", false),
				'ayuda' => "Carpeta que contiene operaciones.",
				);
		} else {
			$iconos[] = array(
				'imagen' => isset($img_item) ? $img_item : toba_recurso::imagen_toba("item.gif", false),
				'ayuda' => "Una [wiki:Referencia/Operacion Operación] representa la unidad accesible por el usuario.",
				);
				
			if ($this->es_de_consola()) {
				$iconos[] = array(
								'imagen' => toba_recurso::imagen_proyecto("solic_consola.gif",false),
								'ayuda' => 'Solicitud de Consola'
							);
			} elseif($this->get_tipo_solicitud()=="wddx") {
				$iconos[] = array(
								'imagen' => toba_recurso::imagen_proyecto("solic_wddx.gif",false),
								'ayuda' => 'Solicitud WDDX'
							);
			}
			if($this->crono()){		
				$iconos[] = array(
					'imagen' => toba_recurso::imagen_toba("cronometro.gif", false),
					'ayuda'=> "La operación se cronometra"
				);			
			}
			if($this->es_publico()){
				$iconos[] = array(
					'imagen' => toba_recurso::imagen_toba("usuarios/usuario.gif", false),
					'ayuda'=> "Operación pública"
				);				
			}
			if($this->puede_redireccionar()){
				$iconos[] = array(
					'imagen' => toba_recurso::imagen_toba("refrescar.png", false),
					'ayuda'=> "La operación puede redireccionar hacia otra."
				);				
			}
			if($this->registra_solicitud() == 1){
				$iconos[] = array(
					'imagen' => toba_recurso::imagen_toba("solicitudes.gif", false),
					'ayuda'=> "La operación se registra en el log"
				);				
			}			
			if($this->generado_con_wizard()){
				$iconos[] = array(
					'imagen' => toba_recurso::imagen_toba("wizard.png", false),
					'ayuda'=> "La operación fue generada con un ASISTENTE",
					'vinculo' => toba::vinculador()->generar_solicitud(toba_editor::get_id(),"1000110", 
									array("padre_p"=>$this->get_proyecto(), "padre_i"=>$this->get_id(),
											apex_hilo_qs_zona => $this->proyecto .apex_qs_separador. $this->id)
									,false,false,null,true, "central" ),
					'plegado' => false								
				);						
		
			}
		}
		return $iconos;
	}

}

?>
	