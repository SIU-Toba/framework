<?

class docbook
{
	private $items;
	private $arbol;
	
	function __construct($proyecto)
	{
		global $db, $ADODB_FETCH_MODE, $solicitud;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$sql = "	SELECT 	i.padre as 		padre,
							i.carpeta as 	carpeta, 
							i.proyecto as	proyecto,
							i.item as 		item,
							i.nombre as 	nombre
					FROM 	apex_item i
					WHERE 	(i.proyecto = '$proyecto')
					ORDER BY i.padre,i.orden;";
		//echo $sql;
		$rs = $db["instancia"][apex_db_con]->Execute($sql);
		$this->items = $rs->getArray();
	}
	//-----------------------------------------------------------
	
	function preparar_arbol ()
	{
		$raices = NULL;
		for($i=0;$i<count($this->items);$i++)
		{
			if($this->items[ $i ]['padre'] == NULL)
			{
				$this->arbol .= "\n<ul class=\"navmeister\">\n";
				$this->get_padres($i);
				$this->arbol .= "</ul>\n";
			}
		}
	}
	
	function get_padres($nodo)
	{
		static $x=0;
		$inden = str_repeat("	",$x );
		//Transformar $this->item en ARBOL
		//ei_arbol($this->items, 'item');
		
		$this->arbol .= $inden . "<li><a href='' title='".$this->items[$nodo]['nombre']."' target=''>".$this->items[$nodo]['nombre']."</a>\n";
		
		if (!$this->items[$nodo]['carpeta'])
		{
			$this->arbol .= $inden . "</li>\n";
		}else{
			$this->arbol .= $inden . "<ul>\n";
			$rs = $this->get_hijos ($nodo);
			for($i=0;$i<count($rs);$i++)
			{
				$x++;
				$this->get_padres($rs[ $i ]);
				$x--;
			}
			$this->arbol .= $inden . "</ul>\n";
			$this->arbol .= $inden . "</li>\n";
		}
	}
	
	function get_hijos($nodo)
	{
		$hijos = NULL;
		for($i=0;$i<count($this->items);$i++)
		{
			if($this->items[ $i ]['padre'] == $this->items[ $nodo ][ 'item' ])
			{
				$hijos[] = $i;
			}
		}
		return $hijos;
	}
	
	//-----------------------------------------------------------

	function obtener_xml()
	{
		//print $this->arbol;
		ei_arbol($this->items);
	}
}




?>