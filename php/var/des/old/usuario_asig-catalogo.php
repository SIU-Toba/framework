<?
global $ADODB_FETCH_MODE;
global $cclass;
global $id;
define("padre","siu");

$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;


//me fijo si el usuarios viene por POST o por GET
if ($canal->protegidos["id"] != "")
	$id		= $canal->protegidos["id"];
else
	$id		= $_POST["id"];
	
$color	= array("#FFFF00","#FFFF33","#FFFF66","#FFFF99");

//****************** Defino asignación Nivel catalogo/Clase CSS *********************
$cclass = array("columna-1","columna-2","columna-3","columna-4","columna-5");
//*******************************************************************************

//Si viene de un POST
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$sSQL = "DELETE FROM apl_usuario_catalogo WHERE usuario='$id'";	
	$db->Execute($sSQL);

	foreach($_POST as $par => $valor )
	{
		if (stristr($par,"permiso"))
		{
			$sSQL = "INSERT INTO apl_usuario_catalogo(usuario,catalogo) VALUES ('$id','$valor')";
			//Verifica si su padre hasta el raiz estan activos tambien
			VerificaPadre($id,$valor);
			$db->Execute($sSQL);					
		}
	}
	
	$sMensaje = "Se han grabado los permisos para el usuario : $id";
}

encabezado($catalogo,"catalogo",$color,$id);

$apl_prm_operacion	= 'SIU006';
$apl_prm_action		= 'apl_post';
$apl_prm_tabla		= 'apl_catalogo';
$apl_prm_pk 		= 'catalogo';


function VerificaPadre($usuario,$item)
{
	global $db;
	
	//Lo igualo al principio para poder encontrar al primer padre
	$padre = $item;
	while(trim($padre) != trim(padre))
	{
		$sSQL 		= "SELECT padre from apl_catalogo WHERE catalogo='$padre'";
		$rspadre	= $db->Execute($sSQL);
		$padre		= trim($rspadre->fields["padre"]);
		$rspadre->Close();
		if (!RSExiste("SELECT '1' FROM apl_usuario_catalogo WHERE usuario='$usuario' AND catalogo='$padre'"))
		{
			$sSQL		= "INSERT INTO apl_usuario_catalogo(usuario,catalogo) VALUES ('$usuario','$padre')";
			$db->Execute($sSQL);
		}
	}
}

function RSExiste($sql)
{
	global $db;

	$rsexiste = $db->Execute($sql);
	if (!$rsexiste->EOF)
		return true;
	else
		return false;
}	

function encabezado($catalogo,$titulo,$color,$id)
{
	global $sMensaje;
	global $cclass;
	global $canal;
	
	$menu = true;
	include ("lib/html_cabecera.php");

	echo "<SCRIPT LANGUAGE='javascript' SRC='" . javascript . "functions.js'></SCRIPT>\n";

	MostrarMensaje($sMensaje);
	echo "<form name='form1' method='POST' action='" . $canal->generar_vinculo('siu_admin_objetos_abm_02',null) . "'>\n";
	echo "<BR><BR>";
	echo "<table width='400'>\n";
	echo "<tr>\n";
	echo "<td class=$cclass[1]>Usuario : $id</td>\n";
	echo "</tr>\n";
	echo "</table>";		
	echo "<BR>";
	$sSQL = "select * from apl_catalogo WHERE catalogo='" . padre . "' order by catalogo_tipo";
	GenerarNivel($sSQL,$color,1);
	pie();
} 

function MostrarMensaje($sMensaje)
{
	global $cclass;
	
	if ($sMensaje!="")
	{
		echo "<table align='center' width='80%'>\n";
		echo "<tr>\n";
		echo "<td class=$cclass[1]>\n";
		echo "$sMensaje\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	}
}

function GenerarNivel($sql,$color,$y)
{
	global $db;
	global $cclass;
	global $id;
	
	$lst_usuarios = $_POST["check"];	

	$rs = $db->Execute($sql);
	if ($rs->EOF)
	{
		return false;
	}
  
	$catalogo = $rs->GetArray();
	
	$sizecatalogo = sizeof($catalogo);
	$ancho	  = 25;
  	$ancho_total = 0;
	for($x=0;$x<$sizecatalogo;$x++)
  	{      
		//Muevo a la posicion x
 		$fila = array_slice($catalogo,$x);
  		//Obtengo el arreglo de esa posicion
  		$fila = $fila[0];
		$id_elemento = trim($fila["catalogo"]);
		$id_padre	 = trim($fila["padre"]);
		echo "<table>\n";
		//Saco el ancho segun el nivel jerarquico
		$ancho_total = $y * $ancho; 		
		echo "<td width='$ancho_total'>\n";
		echo "</td>\n";		
		//Defino la clase a asignar segun el nivel
		$pos = $fila["catalogo_tipo"];
		echo "<td width='20' class=$cclass[$pos] valign=\"top\">\n";
		// nivel jerarquico esta en $y
		// $x + 1 esta al mismo nivel.
		echo "<input type='checkbox' " . VerCheck($id_elemento,$id) . " name='permiso|$id_elemento' onClick='TildaCheck(this)' value='$id_elemento'>\n";
		echo "</td>\n";
		//Calculo el ancho de la segunda columna, en base al tamaño de la primera
		$ancho_celda = 400 - $ancho_total;
		echo "<td width='$ancho_celda' class=$cclass[$pos] valign=\"top\">\n";
		echo trim($fila["nombre"]);
		echo "</td>\n";
		echo "</tr>\n";
		$sSQL = "select * from apl_catalogo WHERE padre='" . trim($fila["catalogo"]) . "' AND catalogo <> '" . trim($fila["catalogo"]) . "'";
		GenerarNivel($sSQL,$color,$y+1);
	  	echo "</table>\n";
  	}	
}

function VerCheck($elemento,$usuario)
{
	global $db;

	$sSQL =  "SELECT '1' FROM apl_usuario_catalogo WHERE usuario='$usuario' and catalogo='$elemento'";
	$rscheck = $db->Execute($sSQL);
	if (!$rscheck->EOF)
		return " checked ";
		$rscheck->Close();
}

function pie()
{
	global $id;
	
	echo "<input type='hidden' name='id' value='$id'>";
	echo "<center><input type='Submit' name='Aceptar' value='Aceptar'></center>";
	echo "</form>";
	include ("lib/html_pie.php");
}
?>
</body>
</html>