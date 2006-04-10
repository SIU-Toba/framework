<?
require_once("modelo/lib/item_toba.php");

class catalogo_items
{
	protected $proyecto;
	protected $carpeta_inicial;
	protected $items = array();
	protected $items_ordenados = array();
	protected $mensaje;
	
	protected $camino; //Durante el recorrido va manteniendo el camino actual
	
	function __construct($menu=false, $proyecto = null, $id_item_inicial=null, $incluidos_forzados=array())
	{
		if (!$proyecto)
			$this->proyecto = toba::get_hilo()->obtener_proyecto();
		else
			$this->proyecto = $proyecto;

		if ($menu)
			$where = "	AND		(i.menu = 1 OR i.item = '')";
		else
			$where = "";
			
		//--- Se dejan solo los items del primer nivel, excepto que este en las excepciones
		if (isset($id_item_inicial)) {
			$filtro_padre = "(i.padre = '$id_item_inicial' OR i.item= '$id_item_inicial')";
		}
		
		if (! empty($incluidos_forzados)) {
			$forzados = implode("', '", $incluidos_forzados);
			$filtro_incluidos = "( i.padre IN ('".$forzados."')";
			$filtro_incluidos .= " OR i.item IN ('".$forzados."') )";			
		}
		
		$filtro_items = "";
		if (isset($filtro_padre) && isset($filtro_incluidos)) {
			$filtro_items ="	AND ($filtro_padre 
									OR 
								$filtro_incluidos)
				";
		} elseif (isset($filtro_padre)) {
			$filtro_items = "	AND $filtro_padre ";	
		} elseif (isset($filtro_incluidos)) {
			$filtro_items = "	AND $filtro_incluidos ";
		}

		$sql = "SELECT 	p.proyecto 						as item_proyecto,
						p.descripcion 					as pro_des,
						".item_toba::definicion_campos().",
						(SELECT COUNT(*) FROM apex_item_objeto WHERE item = i.item) as objetos,
						(SELECT COUNT(*) FROM apex_item WHERE padre = i.item) as cant_hijos
				FROM 	apex_item i,
						apex_proyecto p
				WHERE	i.proyecto = p.proyecto
	            AND     i.proyecto = '{$this->proyecto}'
				AND 	solicitud_tipo <> 'fantasma'
				$filtro_items
				$where
				ORDER BY i.carpeta, i.orden, i.nombre";
		$rs = toba::get_db('instancia')->consultar($sql);
		foreach ($rs as $fila) {
			$this->items[$fila['item']] = new item_toba($fila);			
		}
		$this->carpeta_inicial = isset($id_item_inicial) ? $id_item_inicial : '';
		$this->mensaje = "";
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
			if ($item->nivel() > $max_nivel)
				$max_nivel = $item->nivel();
		}
		return $max_nivel+1;
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
			$es_raiz = ($item->get_id() == '');
			if ($es_raiz || $item->es_de_menu() == $en_menu) 
				$encontrados[] = $item;
		}
		$this->dejar_ramas_con_items($encontrados);
	}
	
	function dejar_items_con_nombre($nombre)
	{
		$encontrados = array();
		foreach ($this->items as $posicion => $item) {
			if (stripos($item->nombre(),$nombre) !== false) {
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
			if (!$item->es_carpeta() && $item->objetos() == 0) {
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
		foreach ($this->raices_de_objeto as $obj_raiz) {
			$sql = "SELECT item FROM apex_item_objeto WHERE
					objeto = '$obj_raiz' AND proyecto = '{$this->proyecto}'
				";
			$rs = consultar_fuente($sql, 'instancia');
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
	
	/**
	 * Recorre el arbol de dependencias hasta llegar a objetos que no estan contenidos en otros
	 */
	protected function buscar_raices_de_objeto($id_objeto)
	{
		$sql_obj = "SELECT objeto_consumidor FROM apex_objeto_dependencias WHERE
					objeto_proveedor = '$id_objeto' AND proyecto = '{$this->proyecto}'
			";
		$rs = consultar_fuente($sql_obj, 'instancia');
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
			if (!in_array($item, $seleccionados)) {
				$padre = $item->get_padre();
				if ($padre != null)
					$padre->quitar_hijo($item);
			}
		}

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
			if (isset($this->items[$item->id_padre()])) {
				$padre = $this->items[$item->id_padre()];
	 			if ($padre != $item) {			
					$item->set_padre($padre);
					$padre->agregar_hijo($item);
				}
			}			
		}
		
		//---Se recorre el arbol para poner los niveles
		$raiz = $this->buscar_carpeta_inicial();
		$this->items_ordenados = array();
		$this->camino = array();
		$this->ordenar_recursivo($raiz, 0);
		$this->items = $this->items_ordenados;
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
	
	//------------------------------------TRABAJOS sobre el arbol --------------------------------------------------------			

	function cambiar_permisos($lista_items_permitidos, $grupo)
	/*
		@@desc: Asigna permisos de un $grupo a toda la $lista_items_permitidos y sus carpetas ancestras.
				El resto de los items/carpetas quedan sin permiso
	*/
	{
		$carpeta = $this->buscar_carpeta_inicial();
		if ($carpeta !== false) {	
			toba::get_db('instancia')->Execute("BEGIN TRANSACTION");
			$this->borrar_permisos_actuales($grupo);
			$this->cambiar_permisos_recursivo($carpeta, $lista_items_permitidos, $grupo);
			toba::get_db('instancia')->Execute("COMMIT TRANSACTION");
			return true;
		}
		else
			return false;
	}	

	
	protected function cambiar_permisos_recursivo($carpeta, $items_permitidos, $grupo)
	{
		$hay_desc_con_permiso = false;
		foreach ($this->items as $item) {
			if ($item->es_hijo_de($carpeta)) {
				if ($item->es_carpeta()) {//Caso recursivo 
					$rama_con_permiso = $this->cambiar_permisos_recursivo($item, $items_permitidos, $grupo);
					$hay_desc_con_permiso = $hay_desc_con_permiso || $rama_con_permiso;
				} else { //Es un item simple
					if (in_array($item->get_id(), $items_permitidos)) {
						$hay_desc_con_permiso = true;
						$item->otorgar_permiso($grupo);						
					}
				}
			}
		}
		if ($hay_desc_con_permiso)
			$carpeta->otorgar_permiso($grupo);

		return $hay_desc_con_permiso;
	}
	
	//Para poder modificar los permisos sólo de una foresta del arbol hay que cambiar este metodo
	protected function borrar_permisos_actuales($grupo)
	{
		//Borro los permisos existentes de todo el arbol
		$sql = "DELETE FROM apex_usuario_grupo_acc_item WHERE usuario_grupo_acc = '$grupo' AND
							proyecto = '{$this->proyecto}';\n";
		if(toba::get_db('instancia')->Execute($sql) === false)
			throw new excepcion_toba("Ha ocurrido un error ELIMINANDO los permisos - " .toba::get_db('instancia')->ErrorMsg());
	}
	
}
?>
