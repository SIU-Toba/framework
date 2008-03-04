<?php

class toba_catalogo_items_perfil extends toba_catalogo_items_base 
{
	private $grupo_acceso;
	
	function __construct($proyecto=null)
	{
		parent::__construct($proyecto);
	}
	
	function cargar($opciones, $id_item_inicial=null, $incluidos_forzados=array())
	{
		if (! isset($id_item_inicial)) { 
			$id_item_inicial = '__raiz__';	
		}
		$en_profundidad = $this->debe_cargar_en_profundidad($id_item_inicial, $opciones);
		$filtro_items = "";		
		if (!$this->debe_cargar_todo($opciones) || $en_profundidad) {
			//--- Se dejan solo los items del primer nivel, excepto que este en las excepciones
			if (isset($id_item_inicial)) {
				$filtro_padre = "(i.padre = '$id_item_inicial' OR i.item= '$id_item_inicial')";
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
		$sql_base = toba_item_perfil_def::get_vista_item($this->proyecto, $this->grupo_acceso);
		$sql = $sql_base['basica']['sql'];
		$sql .=	$filtro_items;
		$sql .= "	AND		(i.solicitud_tipo IS NULL OR i.solicitud_tipo <> 'fantasma')";
		$sql .= "	ORDER BY i.carpeta, i.orden, i.nombre";
		
		$rs = toba_contexto_info::get_db()->consultar($sql);
		
		$this->items = array();
		if (!empty($rs)) {
			foreach ($rs as $fila) {
				$id = array();
				$id['componente'] = $fila['item'];
				$id['proyecto'] = $fila['item_proyecto'];
				$id['grupo_acceso'] = $fila['grupo_acceso'];
				$datos = array('basica' => $fila);
				
				if ($en_profundidad) {
					//$datos = toba_cargador::get_metadatos_perfil($id);
					$datos = toba_cargador::get_metadatos_simples($id, 'toba_item_perfil');
					$obj = new toba_item_perfil( $datos );	
				}else{
					$obj = new toba_item_perfil( $datos, false );	
				}				
				
				$this->items[$fila['item']] = $obj;
			}
			$this->carpeta_inicial = $id_item_inicial;
			$this->mensaje = "";
			$this->ordenar();
			$this->filtrar($opciones);
		}
	}
	
	function set_grupo_acceso($grupo)
	{
		$this->grupo_acceso = $grupo;
	}
	
}

?>