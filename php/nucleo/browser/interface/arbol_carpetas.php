<?

class arbol_carpetas
{
	protected $carpeta_inicial;
	protected $items_originales;
	
	function __construct()
	{
		global $db, $ADODB_FETCH_MODE, $solicitud;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$sql = "SELECT 	p.proyecto 						as item_proyecto,
						i.orden							as orden,
						p.descripcion 					as pro_des,
						i.item		 					as item,
						i.padre		 					as padre,
						i.nombre	 					as nombre,
						i.carpeta						as carpeta
				FROM 	apex_item i,
						apex_proyecto p
				WHERE	i.proyecto = p.proyecto
	            AND     i.proyecto = '".$solicitud->hilo->obtener_proyecto()."'
				AND 	solicitud_tipo <> 'fantasma'
				AND		i.carpeta = 1
				ORDER BY i.carpeta, i.orden";
		$rs =& $db["instancia"][apex_db_con]->Execute($sql);
		if(!$rs) 
			$this->observar("error","Catogo de ITEMS - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql] $sql",false,true,true);
		if(!$rs->EOF){
			$this->items_originales = $rs->GetArray();
		}else{
			$this->items_originales = array();
		}
	}
	//---------------------------------------------------------------------

	function obtener_combo()
	{

		$carpeta = $this->buscar_carpeta_inicial('');
		if ($carpeta !== false)
		{
			$arbol_carpetas = $this->ordenar_recursivo($carpeta, 0);
		}else{
			echo "ERROR!";
		}
		$datos = array();
		foreach($arbol_carpetas as $carpeta)
		{
			$nivel = $carpeta['nivel'] - 1;
			if($nivel >= 0){
				$inden = "&nbsp;" . str_repeat("|" . str_repeat("&nbsp;",8), $nivel) . "|__&nbsp;";
			}else{
				$inden = "";
			}
			$datos[$carpeta['item']] = $inden . $carpeta['nombre'];
		}
		return $datos;
	}
	//---------------------------------------------------------------------
	
	function obtener_combo2()
	{

		$carpeta = $this->buscar_carpeta_inicial('');
		if ($carpeta !== false)
		{
			$arbol_carpetas = $this->ordenar_recursivo($carpeta, 0);
		}else{
			echo "ERROR!";
		}
		$datos = array();
		$pos = 0;
		foreach($arbol_carpetas as $carpeta)
		{
			$nivel = $carpeta['nivel'] - 1;
			if($nivel >= 0){
				$inden = "&nbsp;" . str_repeat("|" . str_repeat("&nbsp;",8), $nivel) . "|__&nbsp;";
			}else{
				$inden = "";
			}
			$datos[$pos]['proyecto'] = $carpeta['item_proyecto'];
			$datos[$pos]['item'] = $carpeta['item'];
			$datos[$pos]['desc'] = $inden . $carpeta['nombre'];
			$pos++;
		}
		return $datos;
	}
	//---------------------------------------------------------------------

	protected function buscar_carpeta_inicial()
	{
		foreach ($this->items_originales as $item)
		{
			if ($item['item'] == $this->carpeta_inicial)
				return $item;
		}
	}
	//---------------------------------------------------------------------
	
	protected function es_padre_de($carpeta, $item)
	{
		if ($item['item'] == '')
			return false;
		return $item['padre'] == $carpeta['item'];
	}
	//---------------------------------------------------------------------
	
	protected function ordenar_recursivo($carpeta, $nivel)
	{
		$items_ordenados = array();
		$carpeta['nivel'] = $nivel;
		$items_ordenados[] = $carpeta;
		foreach ($this->items_originales as $item)
		{
			if ($this->es_padre_de($carpeta, $item))
			{
				$items_ordenados = array_merge($items_ordenados, $this->ordenar_recursivo($item, $nivel + 1));
			}
		}
		return $items_ordenados;
	}
	//---------------------------------------------------------------------
}
?>