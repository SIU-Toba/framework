<?

class arbol_items
{
	protected $carpeta_inicial;
	protected $items_originales;
	protected $items_ordenados;
	protected $solicitud;
	protected $mensaje;
	
	function __construct($solicitud, $menu=false)
	{
		$this->solicitud = $solicitud;
		global $db, $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		if ($menu)
			$where = "	AND		(i.menu = 1 OR i.item = '')";
		else
			$where = "";
		$sql = "SELECT 	p.proyecto 						as item_proyecto,
						i.orden							as orden,
						p.descripcion 					as pro_des,
						i.item		 					as item,
						i.padre		 					as padre,
						i.nombre	 					as nombre,
						i.carpeta						as carpeta,
						i.menu							as menu,
						i.usuario						as usuario,
						i.actividad_buffer_proyecto 	as act_buf_p,
						i.actividad_buffer				as act_buf,
						i.actividad_patron_proyecto		as act_pat_p,
						i.actividad_patron				as act_pat,
						i.actividad_accion				as act_acc,
						i.publico						as publico,
						i.solicitud_registrar			as registrar,
						i.solicitud_registrar_cron		as crono,
						i.solicitud_tipo				as solicitud_tipo,
						(SELECT COUNT(*) FROM apex_item_objeto WHERE item = i.item) as objetos
				FROM 	apex_item i,
						apex_proyecto p
				WHERE	i.proyecto = p.proyecto
	            AND     i.proyecto = '".$this->solicitud->hilo->obtener_proyecto()."'
				AND 	solicitud_tipo <> 'fantasma'
				$where
				ORDER BY i.carpeta, i.orden";
		$rs =& $db["instancia"][apex_db_con]->Execute($sql);
		if(!$rs) 
			$this->observar("error","Catogo de ITEMS - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql] $sql",false,true,true);
		if(!$rs->EOF){
			$this->items_originales = $rs->GetArray();
		}else{
			$this->items_originales = array();
		}
		$this->carpeta_inicial = '';//Raiz
		$this->mensaje = "";
	}
	//---------------------------------------------------------------------

	function set_carpeta_inicial($item)
	{
		$this->carpeta_inicial = $item;
	}
	//---------------------------------------------------------------------
	
	function ordenar()		
	{
		$carpeta = $this->buscar_carpeta_inicial();
		if ($carpeta !== false)
		{
			$this->items_ordenados = $this->ordenar_recursivo($carpeta, 0);
		}else{
			$this->items_ordenados = array();		
		}
	}
	//---------------------------------------------------------------------
	
	function obtener_cantidad_items()
	{
		return count($this->items_ordenados);	
	}
	//---------------------------------------------------------------------

	protected function buscar_carpeta_inicial()
	{
		foreach ($this->items_originales as $item)
		{
			if ($item['item'] == $this->carpeta_inicial)
				return $item;
		}
		//El item inicial no esta en el listado
		$this->mensaje = "La carpeta no esta incluida en la vista MENU";
		return false;
	}
	//---------------------------------------------------------------------
	
	protected function es_padre_de($carpeta, $item)
	{
		if ($item['item'] == '')
			return false;
		return $item['padre'] == $carpeta['item'];
	}
	//---------------------------------------------------------------------
	
	protected function es_carpeta($item)
	{
		return $item['carpeta'] == 1;
	}
	//---------------------------------------------------------------------

	/**
	*	Recorrido en profundidad del arbol
	* 	Se muestran primero las carpetas y ordenados por el 'orden' gracias al ORDER BY de la consulta
	*/
	protected function ordenar_recursivo($carpeta, $nivel)
	{
		$items_ordenados = array();
		$carpeta['nivel'] = $nivel;
		$items_ordenados[] = $carpeta;
		foreach ($this->items_originales as $item)
		{
			if ($this->es_padre_de($carpeta, $item))
			{
				if ($this->es_carpeta($item)) //Caso recursivo
				{
					$items_ordenados = array_merge($items_ordenados, $this->ordenar_recursivo($item, $nivel + 1));
				}
				else
				{
					$item['nivel'] = $nivel + 1;
					$items_ordenados[] = $item;
				}
			}
		}
		return $items_ordenados;
	}
	//---------------------------------------------------------------------
}
?>