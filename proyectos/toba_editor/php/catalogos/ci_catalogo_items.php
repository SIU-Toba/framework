<?php
require_once('catalogos/ci_catalogo.php'); 

class ci_catalogo_items extends ci_catalogo
{
	protected $catalogador; 
	protected $item_seleccionado;
	const foto_inaccesibles = 'Items con problemas de acceso';
	const foto_sin_objetos = 'Items sin objetos asociados';
	
	function ini()
	{
		$this->album_fotos = new album_fotos('cat_item');
		//Si se pidio un item especifico, cargarlo
		$item_selecc = toba::memoria()->get_parametro('item');
		if ($item_selecc != null) {
			$this->s__opciones['inicial'] = $item_selecc;
		}
		
		$this->catalogador = new toba_catalogo_items(toba_editor::get_proyecto_cargado());
		$this->dep('items')->desactivar_chequeo_nodos();
	}

	
	//-------------------------------
	//---- Fotos --------------------
	//-------------------------------
	
	function conf__fotos()
	{
		$fotos = parent::conf__fotos();
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
				$this->s__opciones = array();
				break;
			case self::foto_inaccesibles:
				$this->s__opciones = array();
				$this->s__opciones['inaccesibles'] = true;
				break;
			case self::foto_sin_objetos :
				$this->s__opciones = array();
				$this->s__opciones['sin_objetos'] = true;
				break;
			default:
				parent::evt__fotos__seleccion($nombre);
		}
	}	
		
	//-------------------------------
	//---- Listado de items ----
	//-------------------------------

	function get_nodo_raiz($inicial=null, $con_excepciones=true)
	{
		$excepciones = array();
		//¿Hay apertura seleccionada?		
		if (isset($this->s__apertura) && $con_excepciones) {
			$apertura = (isset($this->apertura_selecc)) ? $this->apertura_selecc : $this->s__apertura;
			$this->dependencia('items')->set_apertura_nodos($apertura);
			foreach ($apertura as $nodo => $incluido) {
				if ($incluido) {
					$excepciones[] = $nodo;	
				}	
			}
		}

		$opciones = isset($this->s__opciones) ? $this->s__opciones : array();
		$this->catalogador->cargar($opciones, $inicial, $excepciones);
		
		$this->dependencia('items')->set_frame_destino(apex_frame_centro);

		if (isset($this->s__opciones)) {
			//Cuando el catalogo carga todo los items es porque va a filtrar algo
			//entonces el resultado se debe mostrar completo, sin colapsados
			if ($this->catalogador->debe_cargar_todo($this->s__opciones)) {
				$this->dependencia('items')->set_todos_abiertos();
			}
		}
		
		$nodo = $this->catalogador->buscar_carpeta_inicial();
		if ($nodo !== false) {
			$nodo->cargar_rama();
			//--- Cuando es un item directo y no una carpeta se aumenta la apertura
			if (!$nodo->es_carpeta()) {
				$this->dependencia('items')->set_nivel_apertura(3);
			}
			return array($nodo);
		}		
	}
	
	function conf__items($arbol)
	{
		if (isset($this->s__opciones['inicial'])) {
			$arbol->set_datos($this->get_nodo_raiz($this->s__opciones['inicial']));
		} else {
			$arbol->set_datos($this->get_nodo_raiz());
		}
	}
	
	function evt__items__cargar_nodo($id)
	{
		return $this->get_nodo_raiz($id, false);
	}

	function evt__items__ver_propiedades($id)
	{
		$this->s__apertura[$id] = 1;
		$this->s__opciones['inicial'] = $id;
	}
	
	function evt__items__cambio_apertura($datos)
	{
		$this->s__apertura = $datos;
	}
	
}

class pant_catalogo_items extends toba_ei_pantalla 
{
	function generar_layout()
	{
		foreach ($this->get_dependencias() as $dep) {
			$dep->generar_html();
		}
	}	
}
?>