<?php

class toba_catalogo_items extends toba_catalogo_items_base 
{

	function __construct($proyecto=null)
	{
		parent::__construct($proyecto);
	}

	function cargar($opciones, $id_item_inicial=null, $incluidos_forzados=array())
	{
		if (! isset($id_item_inicial)) { 
			$id_item_inicial = toba_info_editores::get_item_raiz($this->proyecto);
		}
		$en_profundidad = $this->debe_cargar_en_profundidad($id_item_inicial, $opciones);
		$filtro_items = "";		
		if (!$this->debe_cargar_todo($opciones) || $en_profundidad) {
			//--- Se dejan solo los items del primer nivel, excepto que este en las excepciones
			if (isset($id_item_inicial)) {
				$id_item_sano =	toba_contexto_info::get_db()->quote($id_item_inicial);
				$filtro_padre = "(i.padre = $id_item_sano OR i.item= $id_item_sano)";
						//OR i.padre IN (SELECT item FROM apex_item WHERE padre='$id_item_inicial'))";
			}
			
			if (! empty($incluidos_forzados) && !$en_profundidad) {
				$forzados = implode("', '", $incluidos_forzados);
				$filtro_incluidos = "( i.padre IN ('".$forzados."')";
				$filtro_incluidos .= " OR i.item IN ('".$forzados."') )";			
			}
			
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
		}
		
		if (isset($opciones['solo_carpetas']) && $opciones['solo_carpetas'] == 1) {
			$filtro_items .= "	AND i.carpeta = 1";
		}
		
		//-- Se utiliza como sql básica aquella que brinda la definición de un componente
		toba_item_def::set_db(toba_contexto_info::get_db());
		$sql_base = toba_item_def::get_vista_extendida($this->proyecto);
		$sql = sql_concatenar_where($sql_base['basica']['sql'], array(" (i.solicitud_tipo IS NULL OR i.solicitud_tipo <> 'fantasma')" . $filtro_items ));
		$sql = sql_agregar_ordenamiento($sql,  array(array('i.carpeta', 'asc'), array('i.orden', 'asc'),array('i.nombre', 'asc')));
		$rs = toba_contexto_info::get_db()->consultar($sql);
		$this->items = array();
		if (!empty($rs)) {
			foreach ($rs as $fila) {
				$id = array();
				$id['componente'] = $fila['item'];
				$id['proyecto'] = $fila['item_proyecto'];
				$datos = array('basica' => $fila);
				if ($en_profundidad) {
					$info = toba_constructor::get_info($id, 'toba_item', true, null, true, true);
				} else {
					$info = toba_constructor::get_info($id, 'toba_item', false, $datos);
				}
				$this->items[$fila['item']] = $info;
			}
			$this->carpeta_inicial = $id_item_inicial;
			$this->mensaje = "";
			$this->ordenar();
			$this->filtrar($opciones);
		}
	}

	//------------------------------------TRABAJOS sobre el arbol --------------------------------------------------------			

	/**
	 * Asigna permisos de un $grupo a toda la $lista_items_permitidos y sus carpetas ancestras.
	 * El resto de los items/carpetas quedan sin permiso
	 */
	function cambiar_permisos($lista_items_permitidos, $grupo)
	{
		$carpeta = $this->buscar_carpeta_inicial();
		if ($carpeta !== false) {	
			toba_contexto_info::get_db()->abrir_transaccion();
			$this->borrar_permisos_actuales($grupo);
			$this->cambiar_permisos_recursivo($carpeta, $lista_items_permitidos, $grupo);
			toba_contexto_info::get_db()->cerrar_transaccion();
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
		toba_contexto_info::get_db()->ejecutar($sql);
	}
	
}
?>