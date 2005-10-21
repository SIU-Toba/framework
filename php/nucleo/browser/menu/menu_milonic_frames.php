<?php

class menu_milonic_frames extends menu_recorrido
{
	protected $pos_izquierda = 130;
	protected $pos_arriba = 3;
	protected $fuente = 'Verdana, Arial, Helvetica, sans-serif';
	protected $color_fondo_activo = '#0A246A';	
	protected $color_fondo_inactivo = '#D4D0C8';
	protected $color_3d = '#808080';
	
	/**
	*	Funcion exclusiva de los menues del tipo frame, para que incluyan su contenido en el frame superior
	*/
	public function mostrar_frame_sup()
	{

		//Se define a que frame se redirecciona la expansion de menu.
		//por defecto se considera a un frame llamado contenido, si se recibe una variable 'frame'
		//pog $_GET se considera ese el nombre del frame
		$qs = "";//Modificacion que hay que realizar en ej .js
		if(isset($_GET["frame"])){
			$source_frame = $_GET["frame"];
		}else{
			$source_frame = "contenido";
		}
		if($source_frame<>"contenido")
			$qs="?frame=$source_frame";
		
?>
<script language='javascript'>
//Esta funcion es un titere que hace que no se rompa el codigo generico del 
//menu, que se comparte con el frame de abajo...
function positiontip(){}


redireccion = "<? echo $source_frame ?>";
function salir(){
	if(confirm('Desea terminar la sesión?')) 
		top.location.href='<? echo toba::get_hilo()->finalizar() ?>';
}
</script>
<?
	$this->pre_arbol();
?>
addmenu(menu=[		// This is the array that contains your menu properties and details
"mainmenu",			// Menu Name - This is needed in order for the menu to be called
<?=$this->pos_arriba?>,				// Menu Top - The Top position of the menu in pixels
<?=$this->pos_izquierda?>,				// Menu Left - The Left position of the menu in pixels
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
'right',					// Position of TOP sub image left:center:right
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
		$rs = $this->items_de_menu(true);
		foreach ($rs as $item) {
			if($item["carpeta"]==1){
				// Agrego CARPETAS al menu, como estoy listando UN proyecto, no tengo problema en el identificador del ITEM
				//ej: ,"News&nbsp;sites&nbsp;&nbsp;","show-menu=news",,"",1		
				echo ",'<span class=\"menu-texto\">" . trim($item["nombre"]) . "</span>','show-menu=" . trim($item["item"]) .
					 " target=contenido;sourceframe=".$source_frame.";',,'',1\n";
			} else {
				// Agrego ITEMS al menu
				//ej: ,"ABC News","http://www.abcnews.com",,,0
				echo ",'" . trim($item["nombre"]) . "','". toba::get_vinculador()->generar_solicitud($item["proyecto"],$item["item"]) . " target=contenido;sourceframe=".$source_frame.";',,'',1\n";
			}
		}
		//Agrego el boton de salir
		//echo ",'Salir',\"javascript:salir()\",,'',1\n";
		echo "])\n\n";
		$this->post_arbol();
	}
	
	
	protected function pre_arbol()
	{
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
		echo js::abrir();
		?>
		if(top.cabecera && top.cabecera.load_ok){
			if(top.cabecera.load_ok == '1'){
				top.cabecera.closeallmenus();	
			}
		}
		
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
		'#000000',			// Mouse Off Font Color
		'<?=$this->color_fondo_inactivo ?>',			// Mouse Off Background Color
		'#FFFFFF',			// Mouse On Font Color
		'<?=$this->color_fondo_activo ?>',			// Mouse On Background Color
		'#000000',			// Menu Border Color 
		11,						// Font Size in pixels
		'normal',				// Font Style (italic or normal)
		'normal',				// Font Weight (bold or normal)
		"<?=$this->fuente?>",					// Font Name
		5,							// Menu Item Padding
		'',						// Sub Menu Image (Leave this blank if not needed)
		1,							// 3D Border & Separator bar
		'#FFFFFF',			// 3D High Color
		'<?=$this->color_3d?>',			// 3D Low Color
		'',			// Current Page Item Font Color (leave this blank to disable)
		'',			// Current Page Item Background Color (leave this blank to disable)
		'',						// Top Bar image (Leave this blank to disable)
		'',			// Menu Header Font Color (Leave blank if headers are not needed)
		'',			// Menu Header Background Color (Leave blank if headers are not needed)
		]
		<?		
	}
	
	function post_arbol()
	{
		echo "dumpmenus()\n";
		echo js::cerrar();
		js::cargar_consumos_globales(array("menu/milonic_frames"));		
	}
	
	function inicio_nodos_hermanos($item)
	{
		//En el modo frame se evitan los nodos de primer nivel
		if ($item['padre'] != '') {		
			$padre = trim($item["padre"]);
			echo "addmenu(menu=['" . $padre ."',-10,,200,1,'',style1,,'left',effect,,,,,,,,,,,,\n";		
		}
	}
	
	function fin_nodos_hermanos($item)
	{
		//En el modo frame se evitan los nodos de primer nivel
		if ($item['padre'] != '') {		
			echo "])\n";
		}
	}
	
	function carpeta($item)
	{
		//En el modo frame se evitan los nodos de primer nivel
		if ($item['padre'] != '') {
			echo ",'"
				."<span class=\"menu-icono\">&nbsp;<img src=\"".recurso::imagen_apl('menu_nodo.gif')
				."\" border=\"0\"></span>"
				."<span class=\"menu-texto\">&nbsp;&nbsp;"
				.trim($item["nombre"])
				."</span>"
				."','show-menu=".trim($item["item"])."',,'',1\n";
		}
	}
	
	function item($item)
	{
		//En el modo frame se evitan los nodos de primer nivel
		if ($item['padre'] != '') {		
			$frame_destino = "contenido";//Parte de un toqueteo extraño necesario para multiframes		
			echo ",'<span class=\"menu-texto\">&nbsp;&nbsp;" . trim($item["nombre"]) . "</span>','".
				toba::get_vinculador()->generar_solicitud($item["proyecto"],$item["item"], null, false, false, null, true).
				 " target=$frame_destino',,'',1\n";
		}
	}
	
}

?>