<?
	global $color_serie,$tipografia_serie;//Hago que el ARRAY de colores se accesible.
	//Se define a que frame se redirecciona la expansion de menu.
	//por defecto se considera a un frame llamado contenido, si se recibe una variable 'frame'
	//pog $_GET se considera ese el nombre del frame
	$qs = "";//Modificacion que hay que realizar en ej .js
	if(isset($_GET["frame"])){
		$source_frame = $_GET["frame"];
	}else{
		$source_frame = "contenido";
	}
	if($source_frame<>"contenido") $qs="?frame=$source_frame";

?>
<html>
<head>
<title>Secciones</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="<? echo recurso::css() ?>" type="text/css">
<script language='javascript'>
//Esta funcion es un titere que hace que no se rompa el codigo generico del 
//menu, que se comparte con el frame de abajo...
function positiontip(){}

redireccion = "<? echo $source_frame ?>";
function salir(){
if(confirm('Desea terminar la sesion?')) top.location.href='<? echo $this->hilo->finalizar() ?>';
}
</script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" class="menu">
<?
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
?>
<SCRIPT language="JavaScript">
menunum=0;menus=new Array();_d=document;function addmenu(){menunum++;menus[menunum]=menu;}function dumpmenus(){mt="<script language=javascript>";for(a=1;a<menus.length;a++){mt+=" menu"+a+"=menus["+a+"];"}mt+="<\/script>";_d.write(mt)}

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
suboffset_top=0;			// Sub menu offset Top position 
suboffset_left=60;			// Sub menu offset Left position
Frames_Top_Offset=0 		// Frames Page Adjustment for Top
Frames_Left_Offset=0		// Frames Page Adjustment for Left

style1=[			// style1 is an array of properties. You can have as many property arrays as you need. This means that menus can have their own style.
'<? echo $color_serie["p"][1] ?>',			// Mouse Off Font Color
'<? echo $color_serie["p"][4] ?>',			// Mouse Off Background Color
'<? echo $color_serie["p"][4] ?>',			// Mouse On Font Color
'<? echo $color_serie["p"][1] ?>',			// Mouse On Background Color
'<? echo $color_serie["p"][1] ?>',			// Menu Border Color 
11,						// Font Size in pixels
"normal",				// Font Style (italic or normal)
"normal",				// Font Weight (bold or normal)
"<? echo $tipografia_serie[1] ?>",					// Font Name
5,							// Menu Item Padding
"",						// Sub Menu Image (Leave this blank if not needed)
0,							// 3D Border & Separator bar
'<? echo $color_serie["p"][6] ?>',			// 3D High Color
'<? echo $color_serie["p"][2] ?>',			// 3D Low Color
'<? echo $color_serie["p"][4] ?>',			// Current Page Item Font Color (leave this blank to disable)
'<? echo $color_serie["p"][4] ?>',			// Current Page Item Background Color (leave this blank to disable)
"",						// Top Bar image (Leave this blank to disable)
'<? echo $color_serie["n"][6] ?>',			// Menu Header Font Color (Leave blank if headers are not needed)
'<? echo $color_serie["p"][2] ?>',			// Menu Header Background Color (Leave blank if headers are not needed)
]

addmenu(menu=[		// This is the array that contains your menu properties and details
"mainmenu",			// Menu Name - This is needed in order for the menu to be called
3,					// Menu Top - The Top position of the menu in pixels
130,				// Menu Left - The Left position of the menu in pixels
,					// Menu Width - Menus width in pixels
1,					// Menu Border Width 
"",			// Screen Position - here you can use "center;left;right;middle;top;bottom" or a combination of "center:middle"
style1,				// Properties Array - this is set higher up, as above
1,					// Always Visible - allows the menu item to be visible at all time (1=on/0=off)
"left",				// Alignment - sets the menu elements text alignment, values valid here are: left, right or center
effect,				// Filter - Text variable for setting transitional effects on menu activation - see above for more info
0,					// Follow Scrolling - Tells the menu item to follow the user down the screen (visible at all times) (1=on/0=off)
1, 					// Horizontal Menu - Tells the menu to become horizontal instead of top to bottom style (1=on/0=off)
,					// Keep Alive - Keeps the menu visible until the user moves over another menu or clicks elsewhere on the page (1=on/0=off)
,					// Position of TOP sub image left:center:right
,					// Set the Overall Width of Horizontal Menu to 100% and height to the specified amount (Leave blank to disable)
,					// Right To Left - Used in Hebrew for example. (1=on/0=off)
,					// Open the Menus OnClick - leave blank for OnMouseover (1=on/0=off)
,				// ID of the div you want to hide on MouseOver (useful for hiding form elements)
,					// Reserved for future use
,					// Reserved for future use
,					// Reserved for future use
// "Description Text", "URL", "Alternate URL", "Status", "Separator Bar"
<?
	//----------------------------------------------------------------------------------------------
	//--------------------------- Nivel 0 (Barra horizontal siempre visible) -----------------------
	//----------------------------------------------------------------------------------------------

	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "	SELECT 	i.proyecto as	proyecto,
						i.carpeta as 	carpeta, 
						i.item as 		item,
						i.nombre as 	nombre
				FROM 	apex_item i, apex_usuario_grupo_acc_item u
				WHERE 	(i.item = u.item)
				AND		(i.proyecto = u.proyecto )
				AND		(i.padre = '')
				AND 	(i.item <> '')
				AND 	(i.menu = 1)
				AND		(u.usuario_grupo_acc = '".$this->hilo->obtener_usuario_grupo_acceso()."' )
				AND		(i.proyecto = '".$this->hilo->obtener_proyecto()."')
				ORDER BY i.padre,i.orden;";
	$rs = $db["instancia"][apex_db_con]->Execute($sql);
	//echo "/* $sql */";
	while(!$rs->EOF)
	{
		if($rs->fields["carpeta"]==1){
			// Agrego CARPETAS al menu, como estoy listando UN proyecto, no tengo problema en el identificador del ITEM
			//ej: ,"News&nbsp;sites&nbsp;&nbsp;","show-menu=news",,"",1		
			echo ",'" . trim($rs->fields["nombre"]) . "','show-menu=" . trim($rs->fields["item"]) . " target=contenido;sourceframe=".$source_frame.";',,'',1\n";
		} else {
			// Agrego ITEMS al menu
			//ej: ,"ABC News","http://www.abcnews.com",,,0
			echo ",'" . trim($rs->fields["nombre"]) . "','". $this->vinculador->generar_solicitud($rs->fields["proyecto"],$rs->fields["item"]) . " target=contenido;sourceframe=".$source_frame.";',,'',1\n";
		}
		$rs->movenext();
	}
	//Agrego el boton de salir
	//echo ",'Salir',\"javascript:salir()\",,'',1\n";
	echo "])\n\n";
?>
dumpmenus()
</SCRIPT>
<SCRIPT language="JavaScript" src="<? echo recurso::js("menu-js.php$qs") ?>" type="text/javascript"></SCRIPT>
<table width='100%' class='tabla-0'><tr>
<td class='menu-0'><img src='<? echo recurso::imagen_pro('logo.gif') ?>'></td>
<td class='menu-0'  width='100%'>&nbsp;</td>
<td class='menu-1'>
	<table width='50' cellpadding='3' cellspacing='0' border='0'>
	<tr>
	<td class='menu-0'>
<?
	if(apex_pa_proyecto=="multi")
	{
		echo "<td class='listado-barra-superior-tabi'>";
		echo recurso::imagen_apl("proyecto.gif",true);
		echo "</td>";
		include_once("nucleo/browser/interface/ef.php");
		//Si estoy en modo MULTIPROYECTO muestro un combo para cambiar a otro proyecto,
		//sino muestro el nombre del proyecto ACTUAL
		echo form::abrir("multiproyecto",$this->hilo->cambiar_proyecto(),"target = '_top'");
		echo "<td class='listado-barra-superior-tabi2'>";
		$parametros["sql"] = "SELECT 	p.proyecto, 
                						p.descripcion_corta
                				FROM 	apex_proyecto p,
                						apex_usuario_proyecto up
                				WHERE 	p.proyecto = up.proyecto
								AND  	listar_multiproyecto = 1 
								AND		up.usuario = '".$this->hilo->obtener_usuario()."'
								ORDER BY orden;";
		$proy =& new ef_combo_db(null,"",apex_sesion_post_proyecto,apex_sesion_post_proyecto,
                                "Seleccione el proyecto en el que desea ingresar.","","",$parametros);
		$proy->cargar_estado($this->hilo->obtener_proyecto());//Que el elemento seteado
		echo $proy->obtener_input();
		echo "</td>";
		echo "<td class='listado-barra-superior-tabi'>";
        echo form::image('cambiar',recurso::imagen_apl('cambiar_proyecto.gif'));
        echo "</td>";
		echo form::cerrar();
	}
?>
	</td>
	<td class='menu-0'>&nbsp;<? echo $this->hilo->obtener_usuario() ?></td>
	<td class='menu-0'><a href="#" onclick='javascript:salir()'><img src='<? echo recurso::imagen_apl('finalizar_sesion.gif') ?>' border='0'></a></td>
	</tr>
	</table>
</td>
</tr></table>
<script language='javascript'>
load_ok=1;
</script>
</body>
</html>
