<?
require_once("item.php");

class arbol_items
{
	protected $carpeta_inicial;
	protected $items;
	protected $mensaje;
	
	protected $camino; //Durante el recorrido va manteniendo el camino actual
	
	function __construct($menu=false)
	{
		global $db, $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		if ($menu)
			$where = "	AND		(i.menu = 1 OR i.item = '')";
		else
			$where = "";
		$sql = "SELECT 	p.proyecto 						as item_proyecto,
						p.descripcion 					as pro_des,
						".item::definicion_campos().",
						(SELECT COUNT(*) FROM apex_item_objeto WHERE item = i.item) as objetos
				FROM 	apex_item i,
						apex_proyecto p
				WHERE	i.proyecto = p.proyecto
	            AND     i.proyecto = '".toba::get_hilo()->obtener_proyecto()."'
				AND 	solicitud_tipo <> 'fantasma'
				$where
				ORDER BY i.carpeta, i.orden";
		$rs =& $db["instancia"][apex_db_con]->Execute($sql);
		if(!$rs) 
			throw new excepcion_toba("Catogo de ITEMS - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql] $sql");
		if(!$rs->EOF){
			while (!$rs->EOF) {
				$this->items[] = new item($rs->fields);
				$rs->MoveNext();
			}
		}else{
			$this->items = array();
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
	{
		foreach ($this->items as $posicion => $item) {
			if (!in_array($grupo, $item->grupos_acceso())) 
				unset($this->items[$posicion]);
		}
	}
	
	protected function buscar_carpeta_inicial()
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
		$sql = "DELETE FROM apex_usuario_grupo_acc_item WHERE usuario_grupo_acc = '".
				$grupo."' AND proyecto = '".toba::get_hilo()->obtener_proyecto()."';\n";
		if(toba::get_db('instancia')->Execute($sql) === false)
			throw new excepcion_toba("Ha ocurrido un error ELIMINANDO los permisos - " .toba::get_db('instancia')->ErrorMsg());
	}
	
}
?>