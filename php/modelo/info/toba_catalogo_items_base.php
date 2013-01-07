<?php

class toba_catalogo_items_base 
{
	protected $proyecto;
	protected $carpeta_inicial;
	protected $items = array();
	protected $items_ordenados = array();
	protected $mensaje;
	protected $cargar_todo = false;
	protected $usa_niveles = true;
	
	protected $camino; //Durante el recorrido va manteniendo el camino actual
	
	function __construct($proyecto=null)
	{
		$this->proyecto = $proyecto;
	}
	
	//------------------------------------PROPIEDADES --------------------------------------------------------			
	function items()
	{
		return $this->items;
	}
	
	function cantidad_items()
	{
		return count($this->items);	
	}	
	
	function profundidad()
	{
		$max_nivel = 0;
		foreach ($this->items as $item) {
			if ($item->get_nivel_prof() > $max_nivel)
				$max_nivel = $item->get_nivel_prof();
		}
		return $max_nivel+1;
	}
	
	function cargar_todo($opciones=array())
	{
		$this->cargar_todo = true;
		$this->cargar($opciones);
	}
	
	function cargar($opciones, $id_item_inicial=null, $incluidos_forzados=array())
	{}
	
	protected function debe_cargar_en_profundidad($id_item, $opciones)
	{
		if (isset($opciones['sin_componentes'])) {
			return false;
		}
		$proyecto = toba_contexto_info::get_db()->quote($this->proyecto);
		$id_item = toba_contexto_info::get_db()->quote($id_item);
		$sql = "SELECT carpeta FROM apex_item i WHERE
					i.item= $id_item AND i.proyecto= $proyecto ";
		$rs = toba_contexto_info::get_db()->consultar($sql);

		if (!empty($rs)) {
			return $rs[0]['carpeta'] == 0;
		} else {
			return false;
		}	
	}
	
	function debe_cargar_todo($opciones)
	{
		return $this->cargar_todo || (isset($opciones['id']) && $opciones['id'] != '') ||
				(isset($opciones['nombre']) && $opciones['nombre'] != '') ||
				isset($opciones['inaccesibles']) ||
				isset($opciones['sin_objetos']) ||
				(isset($opciones['con_objeto']) && $opciones['con_objeto'] == 1) ||
				isset($opciones['menu']) || 
				isset($opciones['tipo_solicitud']) ||
				isset($opciones['zona']) ||
				isset($opciones['asistente']);
	}
	
	function buscar_carpeta_inicial()
	{
		foreach ($this->items as $item) {
			if ($item->get_id() == $this->carpeta_inicial)
				return $item;
		}
		//El item inicial no esta en el listado
		$this->mensaje = "La carpeta no esta incluida en la vista MENU";
		return false;
	}
	
	//---------------------------------------------------------------------	
	function resetear()
	{
		foreach ($this->items as $item)
		{
			$item->set_padre(null);
			$item->set_sin_hijos();
		}
	}
	
	/**
	*	Recorrido en profundidad del arbol
	* 	Se muestran primero la caperta y luego los items ordenados por posición en menú
	*/	
	function ordenar()
	{
		//--- Se conocen entre padres e hijos
		foreach (array_keys($this->items) as $id) {
			$item = $this->items[$id];
			if (isset($this->items[$item->get_id_padre()])) {
				$padre = $this->items[$item->get_id_padre()];
	 			if ($padre !== $item) {			
					$item->set_padre($padre);
					$padre->agregar_hijo($item);
				}
			}			
		}
		if ($this->usa_niveles) {
			//---Se recorre el arbol para poner los niveles
			$raiz = $this->buscar_carpeta_inicial();
			$this->items_ordenados = array();
			$this->camino = array();
			$this->ordenar_recursivo($raiz, 0);
			$this->items = $this->items_ordenados;
			unset($this->item_ordenados);
		}
	}	
	
	function ordenar_recursivo($carpeta, $nivel)
	{
		$this->items_ordenados[] = $carpeta;
		$carpeta->set_nivel($nivel);		
		$this->camino[] = $carpeta->get_id();
		if ($carpeta->es_carpeta()) {
			foreach ($carpeta->get_hijos() as $hijo) {
				$hijo->set_camino($this->camino);
				//Caso recursivo			
				if ($hijo->es_carpeta()) { 
					$this->ordenar_recursivo($hijo, $nivel + 1);
				} else {
					$this->items_ordenados[] = $hijo;
					$hijo->set_nivel($nivel + 1);
				}
			}
		}
		array_pop($this->camino);
	}
	
	function filtrar($opciones)
	{
		if (isset($opciones['menu'])) {
			$solo_menu = ($opciones['menu'] == 'SI') ? true : false;
			$this->filtrar_items_en_menu($solo_menu);
		}
		
		//--- ID
		if (isset($opciones['id']) && $opciones['id'] != '') {
			$this->dejar_items_con_id($opciones['id']);			
		}

		//--- Nombre
		if (isset($opciones['nombre']) && $opciones['nombre'] != '') {
			$this->dejar_items_con_nombre($opciones['nombre']);
		}			

		//--- Inaccesibles
		if (isset($opciones['inaccesibles'])) {
			$this->dejar_items_inaccesibles();
		}		
		
		//--- Con/Sin Objetos
		if (isset($opciones['sin_objetos'])) {
			$this->dejar_items_sin_objetos();
		}
		if (isset($opciones['con_objeto']) && $opciones['con_objeto'] == 1) {
			if (isset($opciones['objeto'])) {
				$this->dejar_items_con_objeto($opciones['objeto']);
			}
		}
		
		//--- Tipo de Solicitud
		if (isset($opciones['tipo_solicitud'])) {
			$this->dejar_items_con_tipo_solicitud($opciones['tipo_solicitud']);
		}		
		
		//--- Zona
		if (isset($opciones['zona'])) {
			$this->dejar_items_con_zona($opciones['zona']);
		}				
		
		//--- Asistente
		if (isset($opciones['asistente'])) {
			$this->dejar_items_con_asistente($opciones['asistente']);
		}		
	}

	//---------------------------FILTRADO DE ITEMS------------------------------------------
	function set_carpeta_inicial($id_item)
	{
		$this->carpeta_inicial = $id_item;
	}

	function sacar_publicos()
	{
		foreach ($this->items as $posicion => $item) {
			if ($item->es_publico()) 
				unset($this->items[$posicion]);
		}
	}

	/**
	 * Del conjunto de items disponibles, sólo mantiene aquellos que tiene grupo de acceso $grupo
	 * Este filtro afecta al recorrido ya que toma tanto carpetas como items. Una carpeta que no es del grupo de 
	 * acceso $grupo bloquea el recorrido de todos sus hijos. Es recomendable utilizarlo luego de un recorrido.
	 */
	function dejar_grupo_acceso($grupo)
	{
		foreach ($this->items as $posicion => $item) {
			if (!in_array($grupo, $item->grupos_acceso())) 
				unset($this->items[$posicion]);
		}
	}	
	
	function filtrar_items_en_menu($en_menu)
	{
		$encontrados = array();
		foreach ($this->items as $posicion => $item) {
			if ($item->es_raiz() || $item->es_de_menu() == $en_menu) 
				$encontrados[] = $item;
		}
		$this->dejar_ramas_con_items($encontrados);
	}
	
	function dejar_items_con_nombre($nombre)
	{
		$encontrados = array();
		foreach ($this->items as $posicion => $item) {
			if (stripos($item->get_nombre(),$nombre) !== false) {
				$encontrados[] = $item;
			}
		}
		$this->dejar_ramas_con_items($encontrados);	
	}	
	
	function dejar_items_con_id($id)
	{
		$encontrados = array();
		foreach ($this->items as $item) {
			if (stripos($item->get_id(),$id) !== false) {
				$encontrados[] = $item;
			}
		}

		$this->dejar_ramas_con_items($encontrados);
	}
	
	function dejar_items_inaccesibles()
	{
		$encontrados = array();
		foreach ($this->items as $item) {
			if (!$item->es_carpeta() && $item->es_inaccesible()) {
				$encontrados[] = $item;
			}
		}
		$this->dejar_ramas_con_items($encontrados);
	}
	
	function dejar_items_sin_objetos()
	{
		$encontrados = array();
		foreach ($this->items as $item) {
			if (!$item->es_carpeta() && $item->cant_objetos() == 0) {
				$encontrados[] = $item;
			}
		}
		$this->dejar_ramas_con_items($encontrados);		
	}
	
	function dejar_items_con_objeto($id_objeto)
	{
		//--- Se hacen SQL personalizados si no hay que traer la base completa a memoria
		$ids_encontrados = array();
		
		$this->raices_de_objeto = array();
		$this->buscar_raices_de_objeto($id_objeto);
		$proyecto = toba_contexto_info::get_db()->quote($this->proyecto);
		foreach ($this->raices_de_objeto as $obj_raiz) {
			$raiz = toba_contexto_info::get_db()->quote($obj_raiz);
			$sql = "SELECT item FROM apex_item_objeto WHERE
					objeto = $raiz  AND proyecto = $proyecto";
			$rs = toba_contexto_info::get_db()->consultar($sql);
			foreach ($rs as $item) {
				if (! in_array($item['item'], $ids_encontrados)) {
					$ids_encontrados[] = $item['item'];
				}
			}
		}
		
		$encontrados = array();
		foreach ($this->items as $item) {
			if (in_array($item->get_id(), $ids_encontrados)) {
				$encontrados[] = $item;
			}
		}
		$this->dejar_ramas_con_items($encontrados);				
	}
	
	function dejar_items_con_tipo_solicitud($tipo)
	{
		$encontrados = array();
		foreach ($this->items as $item) {
			if ($item->get_tipo_solicitud() == $tipo) {
				$encontrados[] = $item;
			}
		}
		$this->dejar_ramas_con_items($encontrados);			
	}
	
	function dejar_items_con_zona($zona)
	{
		$encontrados = array();
		foreach ($this->items as $item) {
			if ($item->get_zona() == $zona) {
				$encontrados[] = $item;
			}
		}
		$this->dejar_ramas_con_items($encontrados);			
	}
	
	function dejar_items_con_asistente($asistente)
	{
		$encontrados = array();
		foreach ($this->items as $item) {
			if ($item->generado_con_wizard()) {
				if ($item->tipo_asistente_utilizado() == $asistente) {
					$encontrados[] = $item;
				}				
			}
		}
		$this->dejar_ramas_con_items($encontrados);			
	}	
	/**
	 * Recorre el arbol de dependencias hasta llegar a objetos que no estan contenidos en otros
	 */
	protected function buscar_raices_de_objeto($id_objeto)
	{
		$id_sano = toba_contexto_info::get_db()->quote($id_objeto);
		$proyecto = toba_contexto_info::get_db()->quote($this->proyecto);
		$sql_obj = "SELECT objeto_consumidor FROM apex_objeto_dependencias WHERE
					objeto_proveedor = $id_sano AND proyecto = $proyecto";
		$rs = toba_contexto_info::get_db()->consultar($sql_obj);
		if (empty($rs)) {
			if (! in_array($id_objeto, $this->raices_de_objeto)) {
				$this->raices_de_objeto[] = $id_objeto;
			}
		} else {
			foreach ($rs as $padre) {
				$this->buscar_raices_de_objeto($padre['objeto_consumidor']);
			}
		}
	}

	protected function dejar_ramas_con_items($items)
	{
		//--- Selecciona las carpetas que pertenecen a las ramas
		$seleccionados = $items;
		foreach ($items as $item) {
			$padre = $item->get_padre();
			while ($padre != null) {
				$seleccionados[] = $padre;
				$padre = $padre->get_padre();
			}
		}
		foreach ($this->items as $pos => $item) {
			if (!in_array($item, $seleccionados, true)) {
				$padre = $item->get_padre();
				if ($padre != null)
					$padre->quitar_hijo($item);
			}
		}
	}
	
}

?>