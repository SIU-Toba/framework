<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
require_once("nucleo/lib/arbol_items.php");
require_once('api/elemento_item.php');

//----------------------------------------------------------------
class ci_catalogo_items extends objeto_ci
{
	protected $catalogador; 
	protected $opciones;
	protected $item_seleccionado;
	protected $apertura_items;				//Ultima apertura de items creada
	protected $apertura_items_selecc;		//Seleccion explicita de apertura
	protected $nombre_ultima_foto = "[ Ultima ]";
	
	function __construct($id)
	{
		toba::get_hilo()->desactivar_reciclado();
		logger::ocultar();
		parent::__construct($id);
		$this->catalogador = new arbol_items(false, toba::get_hilo()->obtener_proyecto());
		$this->catalogador->ordenar();
		//Si es la primera carga, se carga la foto anterior
		if (!isset($this->opciones) && !isset($this->apertura_items)) {
			$this->evt__fotos__seleccion($this->nombre_ultima_foto);
		}
	}
	
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "item_seleccionado";
		$propiedades[] = "apertura_items";
		$propiedades[] = "opciones";
		return $propiedades;
	}

	
	function get_etapa_actual()
	{
		if (isset($this->item_seleccionado))
			return 2;
		else
			return 1;
	}
	
	function carpetas_posibles()
	{
		//Formatea las carpetas para que se vean mejor en el combo
		foreach($this->catalogador->items() as $carpeta)
		{
			if ($carpeta->es_carpeta()) {
				$nivel = $carpeta->nivel() - 1;
				if($nivel >= 0){
					$inden = "&nbsp;" . str_repeat("|" . str_repeat("&nbsp;",8), $nivel) . "|__&nbsp;";
				}else{
					$inden = "";
				}
				$datos[] =  array('id' => $carpeta->id(), 'nombre' => $inden . $carpeta->nombre());
			}
		}
		return $datos;
	}
	
	function obtener_html_dependencias()
	{
		foreach($this->dependencias_gi as $dep)
		{
			$this->dependencias[$dep]->obtener_html();	
		}
	}	
	
	function evt__volver()
	{
		unset($this->item_seleccionado);
	}
		
	//-------------------------------
	//---- Cuadro de fotos ----
	//-------------------------------
	
	function evt__fotos__carga()
	{
		$fotos = $this->catalogador->fotos();
		if (count($fotos) > 0) {
			if (! $this->dependencias['fotos']->hay_seleccion()) {
				$this->dependencias['fotos']->seleccionar($this->nombre_ultima_foto);
			}
			$this->dependencias['fotos']->colapsar();
			return $fotos;
		}
	}
	
	function evt__fotos__seleccion($foto_nombre)
	{
		foreach ($this->catalogador->fotos() as $foto) {
			if ($foto['foto_nombre'] == $foto_nombre) {
				$this->apertura_items = $foto['foto_nodos_visibles'];
				$this->apertura_items_selecc = $this->apertura_items;
				$this->opciones = $foto['foto_opciones'];
			}
		}
	}
	
	function evt__fotos__baja($nombre)
	{
		$this->catalogador->borrar_foto($nombre);
	}	
	
	//-------------------------------
	//---- Filtro de opciones ----
	//-------------------------------
	
	function evt__filtro__carga()
	{
		$this->dependencias['filtro']->colapsar();
		if (isset($this->opciones))
			return $this->opciones;
	}
	
	function evt__filtro__cancelar()
	{
		unset($this->opciones);
		$this->dependencias['fotos']->deseleccionar();
	}
	
	function evt__filtro__filtrar($datos)
	{
		$this->opciones = $datos;
	}
	
	//-------------------------------
	//---- Listado de items ----
	//-------------------------------

	function evt__items__carga()
	{
		$this->dependencias['items']->set_frame_destino(apex_frame_centro);
		$this->dependencias['items']->set_item_propiedades(array('toba','/admin/items/composicion_item'));
		//¿Hay apertura seleccionada?
		if (isset($this->apertura_items)) {
			$apertura = (isset($this->apertura_items_selecc)) ? $this->apertura_items_selecc : $this->apertura_items;
			$this->dependencias['items']->set_apertura_nodos($apertura);
		}
		//Aplicación de los filtros
		if (isset($this->opciones)) {
			if (isset($this->opciones['inicial'])) {
				$this->catalogador->set_carpeta_inicial($this->opciones['inicial']);
			}
			if (isset($this->opciones['nombre'])) {
				$this->catalogador->dejar_items_con_nombre($this->opciones['nombre']);
			}			
			if (isset($this->opciones['menu'])) {
				$solo_menu = ($this->opciones['menu'] == 'SI') ? true : false;
				$this->catalogador->filtrar_items_en_menu($solo_menu);
			}
			if (isset($this->opciones['id'])) {
				$this->catalogador->dejar_items_con_id($this->opciones['id']);			
			}
		}
		$nodo = $this->catalogador->buscar_carpeta_inicial();
		if ($nodo !== false)
			return $nodo;
	}

	function evt__items__ver_propiedades($id)
	{
		$this->item_seleccionado = $id;
	}
	
	function evt__items__sacar_foto($nombre, $datos_foto)
	{
		$this->catalogador->agregar_foto($nombre, $datos_foto, $this->opciones);
		$this->evt__fotos__seleccion($nombre);
	}
	
	function evt__items__cambio_apertura($datos)
	{
		//En cada cambio de apertura se guarda en la base como ultima
		$opciones = (isset($this->opciones)) ? $this->opciones : array();
		$this->catalogador->agregar_foto($this->nombre_ultima_foto, $datos, $opciones);		
		$this->apertura_items = $datos;
	}

	
	
	//------------------------------------------------
	//---- Listado de objetos asociados a un item ----
	//------------------------------------------------

	function evt__objetos__carga()
	{
		$this->dependencias['objetos']->set_frame_destino(apex_frame_centro);
		$this->dependencias['objetos']->set_nivel_apertura(3);		
		$item = new elemento_item();
		$item->cargar_db(toba::get_hilo()->obtener_proyecto(), $this->item_seleccionado);	
		return $item;
	}	

}

?>