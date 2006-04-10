<?php
require_once('admin/catalogos/ci_catalogo.php'); 
require_once("modelo/lib/catalogo_items.php");

//----------------------------------------------------------------
class ci_catalogo_items extends ci_catalogo
{
	protected $catalogador; 
	protected $item_seleccionado;
	const foto_inaccesibles = "Items con problemas de acceso";
	const foto_sin_objetos = "Items sin objetos asociados";
	
	function evt__inicializar()
	{
		$this->album_fotos = new album_fotos('cat_item');

		//Si se pidio un item especifico, cargarlo
		$item_selecc = toba::get_hilo()->obtener_parametro('item');
		if ($item_selecc != null) {
			$this->opciones['inicial'] = $item_selecc;
		}
	}
	

	function carpetas_posibles()
	{
		return array();
		$this->cargar_catalogo('');
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
									'id' => $carpeta->get_id(), 
									'nombre' => $inden . $carpeta->nombre());
			}
		}
		return $datos;
	}
	
	//-------------------------------
	//---- Fotos --------------------
	//-------------------------------
	
	function evt__fotos__carga()
	{
		$fotos = parent::evt__fotos__carga();
		$predefinidas = array();
		$predefinidas[] = self::foto_inaccesibles;
		$predefinidas[] = self::foto_sin_objetos;
		$predefinidas[] = apex_foto_inicial;		
		foreach ($predefinidas as $id) {
			$foto = array();
			$foto['foto_nombre'] = $id;
			$foto['predeterminada'] = 0;
			$foto['defecto'] = 'nulo.gif';
			$fotos[] = $foto;
		}
		$this->dependencia('fotos')->set_fotos_predefinidas($predefinidas);
		return $fotos;
	}
	
	function evt__fotos__seleccion($nombre)
	{
		switch ( $nombre['foto_nombre']) {
			case apex_foto_inicial:
				$this->opciones =array();
				break;
			case self::foto_inaccesibles:
				$this->opciones = array();
				$this->opciones['inaccesibles'] = true;
				break;
			case self::foto_sin_objetos :
				$this->opciones = array();
				$this->opciones['sin_objetos'] = true;
				break;
			default:
				parent::evt__fotos__seleccion($nombre);
		}
	}	
		
	//-------------------------------
	//---- Listado de items ----
	//-------------------------------

	function get_nodo_raiz($inicial)
	{
		$excepciones = array();
		//¿Hay apertura seleccionada?		
		if (isset($this->apertura)) {
			$apertura = (isset($this->apertura_selecc)) ? $this->apertura_selecc : $this->apertura;
			$this->dependencia('items')->set_apertura_nodos($apertura);
			foreach ($apertura as $nodo => $incluido) {
				if ($incluido) {
					$excepciones[] = $nodo;	
				}	
			}
		}
				
		$this->catalogador = new catalogo_items(false, toba::get_hilo()->obtener_proyecto(), 
												$inicial, $excepciones);
		$this->catalogador->ordenar();
		$this->dependencia('items')->set_frame_destino(apex_frame_centro);
		$this->dependencia('items')->set_item_propiedades(array('toba','/admin/items/composicion_item'));

		//Aplicación de los filtros
		if (isset($this->opciones)) {
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
			if (isset($this->opciones['inaccesibles'])) {
				$this->dependencia('items')->set_todos_abiertos();
				$this->catalogador->dejar_items_inaccesibles();
			}
			if (isset($this->opciones['sin_objetos'])) {
				$this->dependencia('items')->set_todos_abiertos();
				$this->catalogador->dejar_items_sin_objetos();
			}
			if (isset($this->opciones['con_objeto']) && $this->opciones['con_objeto'] == 1) {
				$this->dependencia('items')->set_todos_abiertos();				
				if (isset($this->opciones['objeto'])) {
					$this->catalogador->dejar_items_con_objeto($this->opciones['objeto']);
				}
			}
		}
		$nodo = $this->catalogador->buscar_carpeta_inicial();
		if ($nodo !== false) {
			$nodo->cargar_rama();
			//--- Cuando es un item directo y no una carpeta se cargan por adelantado sus objetos
			if (!$nodo->es_carpeta()) {
				$nodo->cargar_info();
				$this->dependencia('items')->set_nivel_apertura(3);
			}
			return array($nodo);
		}		
	}
	
	function evt__items__carga()
	{
		$inicial = '';
		if (isset($this->opciones['inicial'])) {
			$inicial = $this->opciones['inicial'];
		}
		return $this->get_nodo_raiz($inicial);
	}
	
	function evt__items__cargar_nodo($id)
	{
		return $this->get_nodo_raiz($id);
	}

	function evt__items__ver_propiedades($id)
	{
		$this->apertura[$id] = 1;
		$this->opciones['inicial'] = $id;
	}
	
	function evt__items__cambio_apertura($datos)
	{
		$this->apertura = $datos;
	}
	
}
?>