<?
//Esta es la unica ACCION que no se procesa dentro de una SOLICITUD (A esta altura no existen)
//La contraparte de esta pagina es la clase estatica SESION, que recibe parametros de ACA.

require_once("nucleo/browser/interface/ef.php");
require_once("nucleo/browser/interface/ei.php");


//El mensaje viaja por querystring para no perderlo si la sesion se rompe en un FRAME!!
define("apex_logon_mensaje","logon_msg");
if(isset($_GET[apex_logon_mensaje])){
	$mensaje=$_GET[apex_logon_mensaje];
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><? echo apex_pa_validacion_titulo ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<? echo recurso::css(apex_pa_estilo) ?>" rel="stylesheet" type="text/css">
<script language='javascript'>

// si se da un timenout, esta pagina puede cargarse en un frame...
// esta funcion detecta este caso y lo soluciona

if(self.name!=top.name)
{
	top.location.href='<? 
	if($mensaje!=""){
		echo $_SERVER["PHP_SELF"] . "?" . apex_logon_mensaje . "=" . urlencode($mensaje);
	}else{
		echo $_SERVER["PHP_SELF"];
	}
?>';
}

function validar_login(form)
{
	if (form.<? echo apex_sesion_post_usuario ?>.value == "")
	{
		alert("Debe ingresar un nombre de usuario");
		form.<? echo apex_sesion_post_usuario ?>.focus();
		return false;
	}
	if (form.<? echo apex_sesion_post_clave ?>.value == "")
	{
		alert("Debe ingresar una contraseña");
		form.<? echo apex_sesion_post_clave ?>.focus();
		return false;
	}
	
	return true;
}

<? if(apex_pa_usuario_anonimo){	?>
function login_anonimo(){
	document.formulario.<? echo apex_sesion_post_usuario ?>.value='<? echo apex_pa_usuario_anonimo ?>';
	document.formulario.submit();
}
<? } ?>

<? if(apex_pa_validacion_debug){	?>
function autologin(usuario,clave){
	document.formulario.<? echo apex_sesion_post_usuario ?>.value=usuario;
	document.formulario.<? echo apex_sesion_post_clave ?>.value=clave;
	document.formulario.submit();
}
<? } ?>

</script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload="document.formulario.<? echo apex_sesion_post_usuario ?>.focus()">
<table width="400" align="center" cellpadding="0" cellspacing="0" border='0'>
<? include_once("logon_inc_arriba.php") ?>
<? if ($mensaje != ""){ 
	echo "<tr><td>";
	echo ei_mensaje($mensaje);
	echo "</td></tr>";
 } ?>
    <tr> 
      <td align="center"><img src="<? echo recurso::imagen_apl("nulo.gif") ?>" width="1" height="10"></td>
    </tr>
    <tr> 
      <td> 
<? echo form::abrir("formulario",$_SERVER["PHP_SELF"]," onSubmit='return validar_login(this)' ") ?>
	<table width='60%' align='center'>
<?
	if(apex_pa_proyecto=="multi"){
        $parametros["sql"] = "SELECT proyecto, descripcion_corta FROM apex_proyecto WHERE listar_multiproyecto = 1 ORDER BY orden;";
		$proy =& new ef_combo_db(null,"",apex_sesion_post_proyecto,apex_sesion_post_proyecto,
                                    "Seleccione el proyecto en el que desea ingresar.",
                                    "","",$parametros);
?>
	<tr><td class='texto-2'>Proyecto</td>
	<td ><? echo $proy->obtener_input() ?></td>
	</tr>
	<tr>
<?	} ?>
	<tr><td class='texto-2'>Usuario</td>
	<td ><? echo form::text(apex_sesion_post_usuario,"",false,20,20) ?></td>
	</tr>
	<tr>
	<td  class='texto-2'>Clave</td>
	<td ><? echo form::password(apex_sesion_post_clave) ?></td></tr>
	<tr><td colspan=2><center>
<? 
	echo form::submit("procesar","Aceptar");
	echo "&nbsp;&nbsp;";
	if(apex_pa_usuario_anonimo){	
		echo form::button("boton","Usuario ANONIMO","onclick='login_anonimo()'");
	}
?>
	</center></td></tr>
	</table>
<? echo form::cerrar() ?>
      </td>
    </tr>
    <tr> 
      <td align="center"><? echo recurso::imagen_apl("nulo.gif",true,1,10) ?></td>
    </tr>
<? if( apex_pa_validacion_debug ){ 

	$sql = "SELECT 	u.usuario as usuario, 
					u.nombre as nombre
			FROM 	apex_usuario u
			ORDER BY 1;";
	$rs = toba::get_db("instancia")->consultar($sql);
	if(!$rs){
        echo "<tr><td align='center'>";
		echo ei_mensaje("No es posible acceder a la lista de usuarios.","error");
        echo "</td></tr>";
    }else{
    	if(count($rs)>0){
?>
<tr><td align="center">
<table width="300" align="center" class="tabla-0">
<tr><td colspan='2' class='lista-titulo'><? echo recurso::imagen_apl("usuarios/usuario.gif",true) ?>&nbsp;&nbsp;AUTOLOGIN</td></tr>
<tr>
	<td class='lista-col-titulo'>Usuario</td>
	<td class='lista-col-titulo' >Nombre</td>
</tr>
<?
	foreach($rs as $registro)
	{
		echo "<tr>";
	    echo "	<td class='lista-e'><a class='basico' href='#' onclick=\"javascript:autologin('".$registro["usuario"]."','clave');return false;\">".$registro["usuario"]."</a></td>";
		echo "	<td class='lista-t'>&nbsp;".$registro["nombre"]."</td>";
		echo "</tr>";
	}
}
?>
</table>
</td>
</tr>
<tr>
<td align="center"><? echo recurso::imagen_apl("nulo.gif",true,1,20) ?></td>
</tr>
<? 
    }
}
include_once("logon_inc_abajo.php");
?>
</table>
</body>
</html> 