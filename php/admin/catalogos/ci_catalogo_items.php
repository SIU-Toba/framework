<?php
require_once('admin/catalogos/ci_catalogo.php'); 
require_once("modelo/lib/arbol_items.php");

//----------------------------------------------------------------
class ci_catalogo_items extends ci_catalogo
{
	protected $catalogador; 
	protected $item_seleccionado;
	
	function __construct($id)
	{
		parent::__construct($id);
		$this->catalogador = new arbol_items(false, toba::get_hilo()->obtener_proyecto());
		$this->catalogador->ordenar();
		
		$this->album_fotos = new album_fotos('cat_item');

		//Si se pidio un item especifico, cargarlo
		$item_selecc = toba::get_hilo()->obtener_parametro('item');
		if ($item_selecc != null) {
			$this->item_seleccionado = $item_selecc;
		}
	}
	
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "item_seleccionado";
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
				$datos[] =  array('proyecto' => toba::get_hilo()->obtener_proyecto(),
									'id' => $carpeta->id(), 
									'nombre' => $inden . $carpeta->nombre());
			}
		}
		return $datos;
	}
	
	function evt__volver()
	{
		unset($this->item_seleccionado);
	}
	
	//-------------------------------
	//---- Fotos --------------------
	//-------------------------------
	function agregar_foto_inicial()
	{
		$this->album_fotos->agregar_foto(apex_foto_inicial, array(), array(), false);
	}
		
	//-------------------------------
	//---- Listado de items ----
	//-------------------------------

	function evt__items__carga()
	{
		$this->dependencia('items')->set_frame_destino(apex_frame_centro);
		$this->dependencia('items')->set_item_propiedades(array('toba','/admin/items/composicion_item'));
		//¿Hay apertura seleccionada?
		if (isset($this->apertura)) {
			$apertura = (isset($this->apertura_selecc)) ? $this->apertura_selecc : $this->apertura;
			$this->dependencia('items')->set_apertura_nodos($apertura);
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
		if ($nodo !== false) {
			return array($nodo);
		}
	}

	function evt__items__ver_propiedades($id)
	{
		$this->item_seleccionado = $id;
	}
	
	function evt__items__cambio_apertura($datos)
	{
		$this->apertura = $datos;
	}

	
	//------------------------------------------------
	//---- Listado de objetos asociados a un item ----
	//------------------------------------------------

	function evt__objetos__carga()
	{
		$this->dependencia('objetos')->set_frame_destino(apex_frame_centro);
		$this->dependencia('objetos')->set_nivel_apertura(3);
		$clave['componente'] = $this->item_seleccionado;
		$clave['proyecto'] = toba::get_hilo()->obtener_proyecto();
		$item = constructor_toba::get_info($clave, 'item');
		return array($item);
	}	
}
?>