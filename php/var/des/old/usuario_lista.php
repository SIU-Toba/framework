<?
define("padre","siu");
global $ADODB_FETCH_MODE;

$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

$color	= array("#FFFF00","#FFFF33","#FFFF66","#FFFF99");
//****************** Defino asignación Nivel catalogo/Clase CSS *********************
$cclass = array("columna-1","columna-2","columna-3","columna-4","columna-5");
//*******************************************************************************

encabezado($catalogo,"catalogo",$color,$id);

function encabezado($catalogo,$titulo,$color,$id)
{
	global $sMensaje;

	include ("lib/html_cabecera.php");
	echo "<form name='form1' method='POST' action='apl_permiso.php'>\n";
	MostrarUsuarios();
	pie();
} 

function MostrarUsuarios()
{
	global $db;
	global $cclass;
	
	$sSQL = "SELECT usuario FROM apl_usuario\n";
	$rsusuarios = $db->Execute($sSQL);
	if (!$rsusuarios->EOF)
	{
		echo "<table cellspacing=1 cellpading=0 width='90%'>\n";
		while(!$rsusuarios->EOF)
		{
			echo "<tr><td class='$cclass[1]'>\n";
			echo $rsusuarios->fields["usuario"] . "\n";
			echo "</td>\n";
			echo "<td class='$cclass[1]' align='left'>\n";
			echo html_modificacion(trim($rsusuarios->fields["usuario"])) . "&nbsp;";
			echo html_baja(trim($rsusuarios->fields["usuario"])) . "&nbsp;";
			echo html_permisos(trim($rsusuarios->fields["usuario"])) . "&nbsp;";
			echo "</td></tr>\n";
			$rsusuarios->MoveNext();
		}	
	}
}

function html_modificacion($idtemp)
{
	global $canal;
	
	return "<a href='" . $canal->generar_vinculo('siu_admin_objetos_abm_01',array("orden"=>"usuario","idmod"=>$idtemp)) . "' target='accion'><img src='".img_global."m.jpg' alt='Modificación' border=0></a>";
}

function html_baja($idtemp)
{
	global $canal;
	
	return "<a href='" . $canal->generar_vinculo('siu_admin_objetos_abm_01',array("orden"=>"usuario","idmod"=>$idtemp)) . "' target='accion'><img src='".img_global."b.jpg' alt='Baja' border=0></a>";	
	//return "<a href='apl_post.php?id=SIU005&tabla=apl_usuario&orden=usuario&pk=$idtemp&accion=Borrar' target='accion'><img src='".img_global."b.jpg' alt='Baja' border=0></a>";
}

function html_permisos($idtemp)
{
	global $canal;
	
	return "<a href='" . $canal->generar_vinculo('siu_admin_objetos_abm_02',array("id"=>$idtemp)) . "' target='accion'><img src='".img_global."p.jpg' alt='Asignar permisos' border=0></a>";
}

function pie()
{
	echo "</form>";
	include ("lib/html_pie.php");
}
?>
