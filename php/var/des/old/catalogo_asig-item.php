<?
global $ADODB_FETCH_MODE;
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

if ($canal->protegidos["id"]!="")
	$id	= $canal->protegidos["id"];
else
	$id	= $_POST["id"];
	
$color	= array("#FFFF00","#FFFF33","#FFFF66","#FFFF99");

//****************** Defino asignación Nivel catalogo/Clase CSS *********************
$cclass=array("columna-1","columna-2","columna-3","columna-4","columna-5");
//*******************************************************************************

//Si viene de un POST
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$lst_usuarios = $_POST["check"];
	//Borro todos los permisos para ese elemento
	$sSQL = "DELETE FROM apl_usuario_catalogo WHERE catalogo = '$id'";	
	$db->Execute($sSQL);
	if (count($lst_usuarios) >= 1)
	{
		foreach($lst_usuarios as $key => $value)
		{
			$sSQL = "INSERT INTO apl_usuario_catalogo(usuario,catalogo) VALUES ('$value','$id')";
			$db->Execute($sSQL);					
			$usr_modificados[]	= "$value";
		}
		$sMensaje = "Se han grabado los permisos para los usuarios : " . implode(",",$usr_modificados);
	}
}

encabezado($catalogo,"catalogo",$color,$id);

$apl_prm_operacion	= 'SIU006';
$apl_prm_action		= 'apl_post';
$apl_prm_tabla		= 'apl_catalogo';
$apl_prm_pk 		= 'catalogo';


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
	global $canal;
	
	include ("lib/html_cabecera.php");
	echo "<SCRIPT LANGUAGE='javascript' SRC='" . javascript . "functions.js'></SCRIPT>\n";
	echo "<body>\n";
	MostrarMensaje($sMensaje);
	echo "<form name='form1' method='POST' action='" . $canal->generar_vinculo("siu_admin_acceso_perm-catalogo-as-item",null) . "'>\n";
	GenerarUnNivel("select * from apl_catalogo WHERE catalogo='$id' order by catalogo_tipo",$color,1);
	echo "<BR>";
	MostrarUsuarios($id);
	pie($id);
} 

function MostrarMensaje($sMensaje)
{
	if ($sMensaje!="")
	{
		echo "<table align='center' width='80%'>\n";
		echo "<tr>\n";
		echo "<td class='columna-1'>\n";
		echo "$sMensaje\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	}
}

function MostrarUsuarios($id)
{
	global $db;
	global $cclass;
	
	$sSQL = "SELECT a.usuario,b.catalogo as cat FROM apl_usuario a LEFT JOIN apl_usuario_catalogo b ON a.usuario=b.usuario AND b.catalogo='$id'";
	$rsusuarios = $db->Execute($sSQL);
	if (!$rsusuarios->EOF)
	{
		echo "<table align='center' width='80%'>\n";
		echo "<tr>\n";
		echo "<td class='claseb'>\n";
		echo "Listado de usuarios a los cuales deseo asignarle los permisos\n";
		echo "<td>\n";
		echo "</tr>\n";
		while(!$rsusuarios->EOF)
		{
			echo "<tr>\n";
			echo "<td class='clasec'>\n";
			$x=1;
			while (($x<4) || (!$rsusuarios->EOF))
			{
				echo "<input name='check[]' ";
				if ($rsusuarios->fields["cat"] != "")
					echo " checked ";
				echo " type='checkbox' value='" . trim($rsusuarios->fields["usuario"]). "'>" . trim($rsusuarios->fields["usuario"]) . "<BR>\n";
				$x++;
				$rsusuarios->MoveNext();
			}
			echo "</td>\n";
			echo "</tr>\n";		
		}
		echo "</table>\n";
	}
	$rsusuarios->Close();
}

function GenerarUnNivel($sql,$color,$y)
{
	global $db;
	global $cclass;

	$lst_usuarios = $_POST["check"];	
	
	$rs = $db->Execute($sql);
	if ($rs->EOF)
	{
		return false;
	}
  
	$catalogo = $rs->GetArray();
	$sizecatalogo = sizeof($catalogo);
	for($x=0;$x<$sizecatalogo;$x++)
  	{      
		//Muevo a la posicion x
 		$fila = array_slice($catalogo,$x);
  		//Obtengo el arreglo de esa posicion
  		$fila = $fila[0];
		$id_elemento = trim($fila[0]);
		$id_padre	 = trim($fila[1]);
		echo "<table width='80%' align='center'>\n";
 		echo "<tr>\n";
		//Defino la clase a asignar segun el nivel
		$pos = $fila[2];
		//Calculo el ancho de la segunda columna, en base al tamaño de la primera
		echo "<td class=$cclass[$pos] valign=\"top\">\n";
		echo trim($fila[3]);
		echo "</td>\n";
		echo "</tr>\n";
	  	echo "</table>\n";
  	}	
}

function pie($id)
{
	echo "<input type='hidden' name='id' value='$id'>";
	echo "<center><input type='Submit' name='Aceptar' value='Aceptar'></center>";
	echo "</form>";
	include ("lib/html_pie.php");
}
?>
</body>
</html>