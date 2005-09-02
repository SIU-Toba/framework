<?
if (isset($_SERVER['TOBA_DIR'])) {
	$dir = $_SERVER['TOBA_DIR']."/php"; 
	$separador = (substr(PHP_OS, 0, 3) == 'WIN') ? ";.;" : ":.:";
	ini_set("include_path", ini_get("include_path"). $separador . $dir);
}
?> 
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.style1 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
}
-->
</style>
</head>
<body>
<div align='center'>
<table width="450" border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td><div align="center">
			<span class="style1">
				<img src="<? echo "http://" . $_SERVER['SERVER_NAME'] . ereg_replace('/admin.php', '/img/mantenimiento/mantenimiento.gif"', $_SERVER['REQUEST_URI']);?> width="80" height="80"><br><br>
				Estamos actualizando el sistema.<br>Por favor, intente nuevamente m&aacute;s tarde.<br><br>
			</span>
		</div></td>
	</tr>
</table>
</div>
</body>
</html>

  
