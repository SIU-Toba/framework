<?

class menu_css
{
	private $items;
	private $arbol;
	
	function __construct()
	{
		global $db, $ADODB_FETCH_MODE, $solicitud;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$sql = "	SELECT 	i.padre as 		padre,
							i.carpeta as 	carpeta, 
							i.proyecto as	proyecto,
							i.item as 		item,
							i.nombre as 	nombre
					FROM 	apex_item i, apex_usuario_grupo_acc_item u
					WHERE 	(i.item = u.item)
					AND		(i.proyecto = u.proyecto)
					--AND		(i.padre <> '')
					AND 	(i.menu = 1)
					AND		(u.usuario_grupo_acc = '".$solicitud->hilo->obtener_usuario_grupo_acceso()."' )
					AND		(i.proyecto = '".$solicitud->hilo->obtener_proyecto()."')
					ORDER BY i.padre,i.orden;";
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

	function obtener_html()
	{
		$path = recurso::path_apl() . "/menu/css/";
		?>
		
		<link rel="stylesheet" type="text/css" media="screen" href="<? print $path ?>navmeister.css" />

		<!--[if gte IE 5]>
		<link rel="stylesheet" type="text/css" media="screen" href="<? print $path ?>navmeister_ie.css" />
		<![endif]-->
		
		<!--[if gte IE 5]>
		<link rel="stylesheet" type="text/css" media="screen" href="<? print $path ?>ie5.php?path=<? print $path ?>" />
		<![endif]-->
<?
		
		print $this->arbol;
	}
}











/*

----------------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------------

	while(!$rs->EOF)
	{
		$padre = trim($rs->fields["padre"]);
		// CABECERA (creo la rama del menu)
		//ej: addmenu(menu=["generalnews",-10,,190,1,"",style1,,"left",effect,,,,,,,,,,,,
		echo "addmenu(menu=['" . $padre ."',-10,,170,1,'',style1,,'left',effect,,,,,,,,,,,,\n";
		while((!$rs->EOF)&&($padre == trim($rs->fields["padre"])))
		{
			if($rs->fields["carpeta"]==1){
				// Agrego CARPETAS al menu
				//ej: ,"News&nbsp;sites&nbsp;&nbsp;","show-menu=news",,"",1		
				echo ",'<img src=\"".recurso::imagen_apl('menu_nodo.gif')."\" border=\"0\">&nbsp;&nbsp;" . trim($rs->fields["nombre"]) . "','show-menu=" . trim($rs->fields["item"]) . "',,'',1\n";
			} else {
				// Agrego ITEMS al menu
				//ej: ,"ABC News","http://www.abcnews.com",,,0
				//ej llamada JS: echo ",'<img src=img/menu_item.gif border=0>&nbsp;&nbsp;" . trim($rs->fields["nombre"]) . "',\"javascript:jumpto(". trim($rs->fields["menu"]) . ")\",,'',1\n";
				echo ",'<img src=\"".recurso::imagen_apl('menu_item.gif')."\" border=\"0\">&nbsp;&nbsp;" . trim($rs->fields["nombre"]) . "','".
					$this->vinculador->generar_solicitud( $rs->fields["proyecto"], $rs->fields["item"],null,false,false,null,true )
					. " target=$frame_destino',,'',1\n";
			}
			$rs->movenext();
		}
		echo "])\n";
	}

*/

?>