<?
require_once("item_toba.php");

class arbol_items
{
	protected $proyecto;
	protected $carpeta_inicial;
	protected $items;
	protected $mensaje;
	
	protected $camino; //Durante el recorrido va manteniendo el camino actual
	
	function __construct($menu=false, $proyecto = null)
	{
		if (!$proyecto)
			$this->proyecto = toba::get_hilo()->obtener_proyecto();
		else
			$this->proyecto = $proyecto;

		if ($menu)
			$where = "	AND		(i.menu = 1 OR i.item = '')";
		else
			$where = "";
		$sql = "SELECT 	p.proyecto 						as item_proyecto,
						p.descripcion 					as pro_des,
						".item_toba::definicion_campos().",
						(SELECT COUNT(*) FROM apex_item_objeto WHERE item = i.item) as objetos
				FROM 	apex_item i,
						apex_proyecto p
				WHERE	i.proyecto = p.proyecto
	            AND     i.proyecto = '{$this->proyecto}'
				AND 	solicitud_tipo <> 'fantasma'
				$where
				ORDER BY i.carpeta, i.orden, i.nombre";
		$rs = toba::get_db('instancia')->consultar($sql);
		$this->items = array();
		foreach ($rs as $fila) {
			$this->items[] = new item_toba($fila);			
		}
		$this->carpeta_inicial = '';//Raiz
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
	
	function dejar_grupo_acceso($grupo)
	/*
		Del conjunto de items disponibles, sólo mantiene aquellos que tiene grupo de acceso $grupo
		Este filtro afecta al recorrido ya que toma tanto carpetas como items. Una carpeta que no es del grupo de 
		acceso $grupo bloquea el recorrido de todos sus hijos. Es recomendable utilizarlo luego de un recorrido.
	*/
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
			$es_raiz = ($item->id() == '');
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
		foreach ($this->items as $posicion => $item) {
			if (stripos($item->id(),$id) !== false) {
				$encontrados[] = $item;
			}
		}
		$this->dejar_ramas_con_items($encontrados);
	}
	
	protected function dejar_ramas_con_items($items)
	{
		//Selecciona las carpetas que pertenecen a las ramas
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
			if ($item->id() == $this->carpeta_inicial)
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
		$carpeta = $this->buscar_carpeta_inicial();
		if ($carpeta !== false) {
			$this->items = $this->ordenar_recursivo($carpeta, 0);
		}else{
			$this->items = array();		
		}
	}

	protected function ordenar_recursivo($carpeta, $nivel)
	{
		$items = array();
		$carpeta->set_nivel($nivel);
		$items[] = $carpeta;
		$this->camino[] = $carpeta->id();
		foreach ($this->items as $item) {
			if ($item->es_hijo_de($carpeta)) {
				$item->set_camino($this->camino);
				$item->set_padre($carpeta);
				$carpeta->agregar_hijo($item);
				if ($item->es_carpeta()) //Caso recursivo
					$items = array_merge($items, $this->ordenar_recursivo($item, $nivel + 1));
				else {
					$item->set_nivel($nivel + 1);
					$items[] = $item;
				}
			}
		}
		array_pop($this->camino);
		return $items;
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
					if (in_array($item->id(), $items_permitidos)) {
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
