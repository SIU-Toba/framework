<?
global $ADODB_FETCH_MODE;
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

include('lib/arbol.php')
?>
<html>
<head>
<link rel="stylesheet" href="<? echo hoja_estilos ?>" type="text/css">
</head>
<body>
<?

$color	= array("#FFFF00","#FFFF33","#FFFF66","#FFFF99");

//****************** Defino asignación Nivel catalogo/Clase CSS *********************

//*******************************************************************************

$cronometro->marcar("lista para encabezado");
encabezado($catalogo,"catalogo",$color,$id);
$cronometro->marcar("generar encabezado");

function encabezado($catalogo,$titulo,$color,$id)
{
	global $canal;

	$obj_arbol 					= new arbol();
	 
	$obj_arbol->tabla			= "apl_catalogo";
	$obj_arbol->campo_padre		= "padre";
	$obj_arbol->valor_padre		= "siu";
	$obj_arbol->campo_clave		= "catalogo";
	$obj_arbol->campo_mostrar	= "nombre";
	$obj_arbol->campo_orden		= "orden,catalogo_tipo";	
	$obj_arbol->segunda_columna = "addColumna";	
	
	html_modo();
	
	echo "<form name='form1' method='POST' action='" . $canal->generar_vinculo("siu_admin_acceso_perm-catalogo-as-mas",null) . "'>";
	//Verifico el modo de visualizacion
	switch($canal->protegidos["modo"])
	{
		case 'menu' :
			$obj_arbol->sql			=  "SELECT * FROM apl_catalogo WHERE catalogo='" . arbol_padre ."' "; 
			$obj_arbol->sql 		.= " AND menu=1 ";
			$obj_arbol->condicion	=  " AND menu=1 ";
			
			break;
		default : 
			$obj_arbol->sql = "SELECT * FROM apl_catalogo WHERE catalogo='" . arbol_padre ."' "; 
			break;			
	}

	$obj_arbol->sql .= " order by orden,catalogo_tipo";
	
	$obj_arbol->GeneraArbol(1);
	
	pie();
} 


function addColumna($fila)
{
	global $arbol_clase;
	
	echo "<td class='$arbol_clase[$pos]' width=65 align='right'>\n";
	switch($fila["catalogo_tipo"])
		{
		case 0:
			echo html_alta($fila);
			echo "&nbsp;";
			echo html_modificacion($fila);
			echo "&nbsp;";
			echo html_permisos_gral($fila);
			break;
		case 1:
			echo html_modificacion($fila);
			echo "&nbsp;";
			echo html_alta($fila);
			echo "&nbsp;";
			echo html_permisos($fila);
			break;
		case 2:
			echo html_modificacion($fila);
			echo "&nbsp;";
			echo html_permisos($fila);
			break;
		case 3:
			echo html_modificacion($fila);
			echo "&nbsp;";
			echo html_permisos($fila);
			break;
		case 4:
			echo html_modificacion($fila);
			echo "&nbsp;";
			echo html_permisos($fila);
			break;
	}
	echo "</td>\n";
}
		
function html_modo()
{
	global $arbol_clase;
	global $canal;
	
	echo "<table cellspacing=1 cellpading=0 align='center'>\n";
	echo "<tr>\n";
	echo "<td class='" . $arbol_clase[1] . "'>\n";
	echo "<a href='" . $canal->generar_vinculo('siu_admin_acceso_perm-catalogo-arbol',array("modo"=>"menu")) . "'>Ver Menú</a>";
	echo "</td>\n";
	echo "<td class='" . $arbol_clase[1] . "'>\n";
	echo "<a href='" . $canal->generar_vinculo('siu_admin_acceso_perm-catalogo-arbol',array("modo"=>"catalogo")) . "'>Ver Catalogo</a>";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}

function html_modificacion($fila)
{
	global $canal;
	$idtemp = trim($fila["catalogo"]);
	return "<a href='" . $canal->generar_vinculo("siu_admin_objetos_abm_alta-catalogo",array("orden"=>"catalogo","idmod"=>$idtemp)) . "' target='accion'><img src='".img_global."m.jpg' alt='Modificación' border=0></a>";
}

function html_alta($fila)
{
	global $canal;
	$idtemp = trim($fila["catalogo"]);
	return "<a href='". $canal->generar_vinculo("siu_admin_objetos_abm_alta-catalogo",array("id"=>$idtemp)) ."' target='accion'><img src='".img_global."a.jpg' alt='Alta' border=0></a>";
}

function html_baja($fila)
{
	$idtemp = trim($fila["catalogo"]);
	return "<a href='apl_post.php?id=SIU006&tabla=apl_catalogo&orden=catalogo&pk=$idtemp&accion=Borrar' target='accion'><img src='".img_global."b.jpg' alt='Eliminar' border=0></a>";
}

function html_permisos($fila)
{
	global $canal;
	$idtemp = trim($fila["catalogo"]);
	return "<a href='". $canal->generar_vinculo("siu_admin_acceso_perm-catalogo-as-item",array("id"=>$idtemp))."' target='accion'><img src='".img_global."p.jpg' alt='Asignar permisos' border=0></a>";
}

function html_permisos_gral($fila)
{
	global $canal;
	$idtemp = trim($fila["catalogo"]);
	return "<a href='" . $canal->generar_vinculo("siu_admin_acceso_perm-catalogo-as-mas",array("id"=>$idtemp))."' target='accion'><img src='".img_global."pg.jpg' alt='Asignar permisos masivos' border=0></a>";
}

function pie()
{
	echo "</form>";
	echo "</body>";
	echo "</html>";
}
?>
