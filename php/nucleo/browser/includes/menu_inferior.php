<?php
//##############################################################################################
//############################  MENU MILONIC (multi_frames)  ###################################
//##############################################################################################

	//Hago accesible el array de colores y tipografias.
	global $color_serie,$tipografia_serie;

		$frame_destino = "contenido";//Parte de un toqueteo extraño necesario para multiframes
		//$frame_destino = "prop";
/*
 DHTML Menu version 3.3.19
 Written by Andy Woolley
 Copyright 2002 (c) Milonic Solutions. All Rights Reserved.
 Plase vist http://www.milonic.co.uk/menu or e-mail menu3@milonic.com
 You may use this menu on your web site free of charge as long as you place prominent links to http://www.milonic.co.uk/menu and
 your inform us of your intentions with your URL AND ALL copyright notices remain in place in all files including your home page
 Comercial support contracts are available on request if you cannot comply with the above rules.
 This script featured on Dynamic Drive (http://www.dynamicdrive.com)
 */

//Si cambia el formato de los frames, la cabecera puede direccionar a lugares erroneos.
/*<script language='javascript'>
	if(self.name==top.cabecera.redireccion)
	{
		top.cabecera.closeallmenus();//Queda el boton activado
	}else{
		//alert('Error! la cabecera redirecciona a:' + top.cabecera.redireccion);
		top.cabecera.location.href='<? echo $this->vinculador->generar_solicitud("toba","/basicos/cabecera") ?>&frame='+self.name;
	}
</script>*/
?>
<SCRIPT language="JavaScript">
/*
if(top.cabecera.load_ok == '1'){
	top.cabecera.closeallmenus();	
}
*/
menunum=0;menus=new Array();_d=document;function addmenu(){menunum++;menus[menunum]=menu;}function dumpmenus(){mt="<script language=javascript>";for(a=1;a<menus.length;a++){mt+=" menu"+a+"=menus["+a+"];"}mt+="<\/script>";_d.write(mt)}

// Special effect string for IE5.5 or above please visit http://www.milonic.co.uk/menu/filters_sample.php for more filters
if(navigator.appVersion.indexOf("MSIE 6.0")>0)
{
	effect = "Fade(duration=0.2);Alpha(style=0,opacity=88);Shadow(color='#777777', Direction=135, Strength=5)"
}
else
{
	effect = "Shadow(color='#777777', Direction=135, Strength=5)" // Stop IE5.5 bug when using more than one filter
}


timegap=500					// The time delay for menus to remain visible
followspeed=5				// Follow Scrolling speed
followrate=10				// Follow Scrolling Rate
suboffset_top=5;			// Sub menu offset Top position 
suboffset_left=-2;			// Sub menu offset Left position
Frames_Top_Offset=0 		// Frames Page Adjustment for Top
Frames_Left_Offset=-50		// Frames Page Adjustment for Left

style1=[			// style1 is an array of properties. You can have as many property arrays as you need. This means that menus can have their own style.
'<? echo $color_serie["p"][1] ?>',			// Mouse Off Font Color
'<? echo $color_serie["p"][4] ?>',			// Mouse Off Background Color
'<? echo $color_serie["p"][4] ?>',			// Mouse On Font Color
'<? echo $color_serie["p"][1] ?>',			// Mouse On Background Color
'<? echo $color_serie["p"][1] ?>',			// Menu Border Color 
11,						// Font Size in pixels
'normal',				// Font Style (italic or normal)
'normal',				// Font Weight (bold or normal)
"<? echo $tipografia_serie[1] ?>",					// Font Name
5,							// Menu Item Padding
'',						// Sub Menu Image (Leave this blank if not needed)
0,							// 3D Border & Separator bar
'<? echo $color_serie["p"][6] ?>',			// 3D High Color
'<? echo $color_serie["p"][2] ?>',			// 3D Low Color
'<? echo $color_serie["p"][4] ?>',			// Current Page Item Font Color (leave this blank to disable)
'<? echo $color_serie["p"][4] ?>',			// Current Page Item Background Color (leave this blank to disable)
'',						// Top Bar image (Leave this blank to disable)
'<? echo $color_serie["n"][6] ?>',			// Menu Header Font Color (Leave blank if headers are not needed)
'<? echo $color_serie["p"][2] ?>',			// Menu Header Background Color (Leave blank if headers are not needed)
]

<?

	global $db, $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "	SELECT 	i.padre as 		padre,
						i.carpeta as 	carpeta, 
						i.proyecto as	proyecto,
						i.item as 		item,
						i.nombre as 	nombre
				FROM 	apex_item i, apex_usuario_grupo_acc_item u
				WHERE 	(i.item = u.item)
				AND		(i.proyecto = u.proyecto)
				AND		(i.padre <> '')
				AND 	(i.menu = 1)
				AND		(u.usuario_grupo_acc = '".$this->hilo->obtener_usuario_grupo_acceso()."' )
				AND		(i.proyecto = '".$this->hilo->obtener_proyecto()."')
				ORDER BY i.padre,i.orden;";
	$rs = $db["instancia"][apex_db_con]->Execute($sql);
	//echo "/* $sql */";

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
	echo "dumpmenus()\n";
	echo "</SCRIPT>\n";
	echo "<SCRIPT language='JavaScript' src='". recurso::js("menu-js.php") ."' type='text/javascript'></SCRIPT>\n";


?>