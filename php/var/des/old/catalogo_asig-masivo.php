<?
global $ADODB_FETCH_MODE;
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

if ($canal->protegidos["id"]!="")
	$id	= $canal->protegidos["id"];
else
	$id	= $_POST["id"];
	
$color	= array("#FFFF00","#FFFF33","#FFFF66","#FFFF99");

//Si viene de un POST
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
 	//Borro los permisos para todos los usuarios para el bloque que elegio el usuario
	$lst_usuarios = $_POST["check"];
	$f_usuario	  = $lst_usuarios[1];
	//Ingreso el acceso por cada usuario que se eligio		
	for ($x=0;$x<sizeof($lst_usuarios);$x++)
	{	
		$sSQL = "DELETE FROM apl_usuario_catalogo WHERE catalogo LIKE '$id%' and usuario='" . $lst_usuarios[$x] . "'";
		$f_usuario	  = $lst_usuarios[$x];
		$db->Execute($sSQL);
	}	
	
	foreach($_POST as $par => $valor )
	{
		if (stristr($par,"permiso"))
		{
			$check_elemento = explode("|",$par);
			//Ingreso el acceso por cada usuario que se eligio		
			for ($x=0;$x<sizeof($lst_usuarios);$x++)
			{	
				$sSQL = "INSERT INTO apl_usuario_catalogo(usuario,catalogo) VALUES ('" . $lst_usuarios[$x] . "','$valor')";
				//Verifica si su padre hasta el raiz estan activos tambien
				VerificaPadre($lst_usuarios[$x],$valor);
				$db->Execute($sSQL);					
			}				
		}
	}
	
	$sMensaje = "Se han grabado los permisos para los usuarios : " . implode(",",$lst_usuarios);
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
	while(trim($padre) != trim(arbol_padre))
	{
		$sSQL 		= "SELECT padre from apl_catalogo WHERE catalogo='$padre'";
		$rspadre	= $db->Execute($sSQL);
		$padre		= trim($rspadre->fields["padre"]);
		$rspadre->Close();
		if (!RSExiste("SELECT '1' FROM apl_usuario_catalogo WHERE usuario='$usuario' AND catalogo='$padre'"))
		{
			
			$sSQL = "INSERT INTO apl_usuario_catalogo(usuario,catalogo) VALUES ('$usuario','$padre')";
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
	global $canal;

	include ("lib/html_cabecera.php");
	echo "<SCRIPT LANGUAGE='javascript' SRC='" . javascript . "functions.js'></SCRIPT>\n";
	echo "</head>\n";
	echo "<body>\n";
	MostrarMensaje($sMensaje);
	echo "<form name='form1' method='POST' action='" . $canal->generar_vinculo("siu_admin_acceso_perm-catalogo-as-mas",null) . "'>\n";
	MostrarUsuarios();
	echo "<BR><BR>";
	GenerarNivel("select * from apl_catalogo WHERE catalogo='$id' order by catalogo_tipo",$color,1);
	pie($id);
} 

function MostrarMensaje($sMensaje)
{
	global $arbol_clase;
	if ($sMensaje!="")
	{
		echo "<table align='center' width='80%'>\n";
		echo "<tr>\n";
		echo "<td class=$arbol_clase[1]>\n";
		echo "$sMensaje\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	}
}

function MostrarUsuarios()
{
	global $db;
	
	$sSQL = "SELECT usuario FROM apl_usuario";
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
				echo "<input name='check[]' " . VerificaCheckUser(trim($rsusuarios->fields["usuario"])) . " type='checkbox' value='" . trim($rsusuarios->fields["usuario"]). "'>" . trim($rsusuarios->fields["usuario"]) . "<BR>\n";
				$x++;
				$rsusuarios->Movenext();
			}
			echo "</td>\n";
			echo "</tr>\n";		
		}
		echo "</table>\n";
	}
	$rsusuarios->Close();
}

function VerificaCheckUser($valor)
{

	$lst_usuarios = $_POST["check"];	
	for ($x=0;$x<sizeof($lst_usuarios);$x++)
	{	
		if (trim($lst_usuarios[$x]) == trim($valor))
		{
			return " checked ";
		}
	}
}

function GenerarNivel($sql,$color,$y)
{
	global $db;
	global $arbol_clase;
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

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
 		echo "<tr>\n";
		//Saco el ancho segun el nivel jerarquico
		$ancho_total = $y * $ancho; 		
		echo "<td width='$ancho_total'>\n";
		echo "</td>\n";		
		//Defino la clase a asignar segun el nivel
		$pos = $fila["catalogo_tipo"];
		echo "<td width='20' class=$arbol_clase[$pos] valign=\"top\">\n";
		// nivel jerarquico esta en $y
		// $x + 1 esta al mismo nivel.
		echo "<input type='checkbox' " . VerCheck($id_elemento,$lst_usuarios[0]) . " name='permiso|$id_elemento' onClick='TildaCheck(this)' value='$id_elemento'>\n";
		echo "</td>\n";
		//Calculo el ancho de la segunda columna, en base al tamaño de la primera
		$ancho_celda = 400 - $ancho_total;
		echo "<td width='$ancho_celda' class=$arbol_clase[$pos] valign=\"top\">\n";
		echo trim($fila["nombre"]);
		echo "</td>\n";
		echo "</tr>\n";
		GenerarNivel("select * from apl_catalogo WHERE padre='" . trim($fila["catalogo"]) . "' AND catalogo <> '" . trim($fila["catalogo"]) . "'",$color,$y+1);
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

function pie($id)
{
	echo "<input type='hidden' name='id' value='$id'>";
	echo "<center><input type='Submit' name='Aceptar' value='Aceptar'></center>";
	echo "</form>";
	echo "</html>";
}
?>
</body>
</html>