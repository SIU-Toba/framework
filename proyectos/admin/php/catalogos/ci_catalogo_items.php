<?php
require_once('catalogos/ci_catalogo.php'); 
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
		
		$this->catalogador = new catalogo_items(editor::get_proyecto_cargado());		
	}
	

	function carpetas_posibles()
	{
		return array();
		$this->cargar_catalogo('');
		//Formatea las carpetas para que se vean mejor en el combo
		foreach($this->catalogador->items() as $carpeta)
		{
			if ($carpeta->es_carpeta()) {
				$nivel = $carpeta->get_nivel_prof() - 1;
				if($nivel >= 0){
					$inden = "&nbsp;" . str_repeat("|" . str_repeat("&nbsp;",8), $nivel) . "|__&nbsp;";
				}else{
					$inden = "";
				}
				$datos[] =  array('proyecto' => editor::get_proyecto_cargado(),
									'id' => $carpeta->get_id(), 
									'nombre' => $inden . $carpeta->nombre());
			}
		}
		return $datos;
	}
	
	
	function obtener_html()
	{
		echo "<style type='text/css'>";
		echo " .ci-cuerpo { 	background-color: #eeeeee; }";
		echo "</style>";
		parent::obtener_html();
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

	function get_nodo_raiz($inicial, $con_excepciones=true)
	{
		$excepciones = array();
		//¿Hay apertura seleccionada?		
		if (isset($this->apertura) && $con_excepciones) {
			$apertura = (isset($this->apertura_selecc)) ? $this->apertura_selecc : $this->apertura;
			$this->dependencia('items')->set_apertura_nodos($apertura);
			foreach ($apertura as $nodo => $incluido) {
				if ($incluido) {
					$excepciones[] = $nodo;	
				}	
			}
		}

		$opciones = isset($this->opciones) ? $this->opciones : array();
		$this->catalogador->cargar($opciones, $inicial, $excepciones);
		
		$this->dependencia('items')->set_frame_destino(apex_frame_centro);

		if (isset($this->opciones)) {
			//Cuando el catalogo carga todo los items es porque va a filtrar algo
			//entonces el resultado se debe mostrar completo, sin colapsados
			if ($this->catalogador->debe_cargar_todo($this->opciones)) {
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
		return $this->get_nodo_raiz($id, false);
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