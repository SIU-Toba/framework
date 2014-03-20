<?php
require_once('contrib/lib/toba_nodo_basico.php');
require_once('contrib/catalogo_items_menu/toba_item_menu.php');
require_once('contrib/catalogo_items_menu/toba_carpeta_menu.php');

class toba_catalogo_items_menu extends toba_catalogo_items_base 
{
	protected $usa_niveles = false;
	
	function cargar($opciones, $raiz=null, $incluidos_forzados=array())
	{
		if (! is_null($raiz)) {
			$rs = toba::proyecto()->get_items_menu();
			//ei_arbol($rs);
			$this->items = array();
			if (!empty($rs)) {
				foreach ($rs as $fila) {
					if ($fila['carpeta']) {
						$obj = new toba_carpeta_menu( $fila['nombre'], null, $fila['item'], $fila['padre']);

					}else{
						$obj = new toba_item_menu( $fila['nombre'], null, $fila['item'], $fila['padre']);	
					}				
					$obj->set_imagen($fila['imagen_recurso_origen'], $fila['imagen']);				
					$this->items[$fila['item']] = $obj;
				}
				$this->carpeta_inicial = $raiz;
				$this->mensaje = "";
				$this->ordenar();
			}
		}
	}

	function cargar_todo($opciones=array())
	{
		$this->carpeta_inicial = toba_info_editores::get_item_raiz($this->proyecto);
		
		$grupo = '';
		$proyecto = toba_contexto_info::get_db()->quote($this->proyecto);
		$sql = "	SELECT 	i.item as item,
							i.proyecto as proyecto,
							i.imagen_recurso_origen,
							i.imagen,
							nombre,
							carpeta,
							padre,
							descripcion,
							ia.usuario_grupo_acc as acceso
					FROM apex_item i
						LEFT OUTER JOIN apex_usuario_grupo_acc_item ia
							ON i.item = ia.item AND i.proyecto = ia.proyecto
					WHERE 	
							i.proyecto = $proyecto
						AND	i.menu = 1
						AND	(publico IS NULL OR publico = 0)
						OR i.item =" . quote($this->carpeta_inicial)."
					ORDER BY i.carpeta, i.orden, i.nombre";
		toba::logger()->debug($sql);
		$rs = toba_contexto_info::get_db()->consultar($sql);		
		$this->items = array();
		if (!empty($rs)) {
			foreach ($rs as $fila) {
				if ($fila['carpeta']) {
						$obj = new toba_carpeta_menu( $fila['nombre'], null, $fila['item'], $fila['padre']);

					}else{
						$obj = new toba_item_menu( $fila['nombre'], null, $fila['item'], $fila['padre']);	
				}				
				$this->items[$fila['item']] = $obj;
			}
			$this->carpeta_inicial = toba_info_editores::get_item_raiz($this->proyecto);
			$this->mensaje = "";
			$this->ordenar();
		}
	}
	
	/**
	 * Retorna un arreglo con los arboles que componen los hijos de un nodo raiz dado
	 */
	function get_hijos($raiz)
	{
		$hijos = array();
		foreach ($this->items as $item) {
			if ($item->get_id_padre() == $raiz)
				$hijos[] = $item;
		}
		return $hijos;
	}
	
	
}

?>