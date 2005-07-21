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
	
	function __construct($id)
	{
		parent::__construct($id);
		$this->catalogador = new arbol_items(false, toba::get_hilo()->obtener_proyecto());
		$this->catalogador->ordenar();
	}
	
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "item_seleccionado";
		return $propiedades;
	}
	
	function get_lista_eventos()
	{
		$eventos = parent::get_lista_eventos();
		if ($this->get_etapa_actual() == 2) {
		 	$volver = eventos::duplicar(eventos::ci_procesar("<- Volver"), 'volver');
			$eventos += $volver;
		}
		$eventos += eventos::refrescar();
		return $eventos;
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
	

	function evt__filtro__carga()
	{
		if (isset($this->opciones))
			return $this->opciones;
		else
			$this->dependencias['filtro']->colapsar();
	}
	
	function evt__filtro__filtrar($datos)
	{
		$this->opciones = $datos;
	}
	
	function evt__items__carga()
	{
		$this->dependencias['items']->set_item_propiedades(array('toba','/admin/items/composicion_item'));
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
	
	function evt__volver()
	{
		unset($this->item_seleccionado);
	}
	
	function evt__objetos__carga()
	{
		$item = new elemento_item();
//		$this->item->cargar_db('comechingones', '803');
		$item->cargar_db(toba::get_hilo()->obtener_proyecto(), $this->item_seleccionado);	
//		$this->dependencias['objetos']->set_mostrar_raiz(false);
		return $item;
	}	

}

?>