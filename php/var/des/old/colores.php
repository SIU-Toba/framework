<?php

	error_reporting(E_ALL ^ E_NOTICE);
	if(!$colores=$_POST["colores"]) $colores = 20;
	if(!$r_i=$_POST["r_i"]) $r_i = 0;
	if(!$r_s=$_POST["r_s"]) $r_s = 255;
	if(!$g_i=$_POST["g_i"]) $g_i = 0;
	if(!$g_s=$_POST["g_s"]) $g_s = 255;
	if(!$b_i=$_POST["b_i"]) $b_i = 0;
	if(!$b_s=$_POST["b_s"]) $b_s = 255;

	for ($a=0;$a<$colores;$a++)
	{
		$red = (int)($r_i+(($r_s-$r_i)/$colores)*$a);
		$green = (int)($g_i+(($g_s-$g_i)/$colores)*$a);
		$blue = (int)($b_i+(($b_s-$b_i)/$colores)*$a);		
		$color_serie["dec"][$a] = "$red,$green,$blue";
		$color_serie["hex"][$a] = "#" . str_pad(dechex($red), 2, "0", STR_PAD_LEFT).
										str_pad(dechex($green), 2, "0", STR_PAD_LEFT).
										str_pad(dechex($blue), 2, "0", STR_PAD_LEFT);
		$cut_paste .= "s/%col_sp_$a%/" . $color_serie["hex"][$a] . "/g\n";
	}
	$html.= "<TABLE width='300' align='center' border='1'>\n";
	$html.= "<TD colspan='2' align='center'>Tabla de colores</TD>\n";
	for($b=0;$b<count($color_serie["hex"]);$b++)
	{
		$html.= "<TR>\n";
			$html.= "<TD width='150' align='center'>" . $color_serie["hex"][$b]. "</TD>\n";
			$html.= "<TD width='150' bgcolor='" . $color_serie["hex"][$b]. "'>&nbsp;&nbsp;&nbsp;&nbsp;</TD>\n";
		$html.= "</TR>\n";
	}
	$html.= "</TABLE>\n";
?>
<html>
<body>
<form name="form2" method="post" action="">
  <table width="400" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr align="center"> 
      <td>&nbsp;</td>
      <td>INFERIOR</td>
      <td>SUPERIOR</td>
    </tr>
    <tr align="center"> 
      <td>Rojo</td>
      <td><input name="r_i" type="text" id="r_i" size="3" maxlength="3" value="<? echo $r_i ?>"> </td>
      <td><input name="r_s" type="text" id="r_s" size="3" maxlength="3" value="<? echo $r_s ?>"></td>
    </tr>
    <tr align="center"> 
      <td>Verde</td>
      <td><input name="g_i" type="text" id="g_i" size="3" maxlength="3" value="<? echo $g_i ?>"></td>
      <td><input name="g_s" type="text" id="g_s" size="3" maxlength="3" value="<? echo $g_s ?>"></td>
    </tr>
    <tr align="center"> 
      <td>Azul</td>
      <td><input name="b_i" type="text" id="b_i" size="3" maxlength="3" value="<? echo $b_i ?>"></td>
      <td><input name="b_s" type="text" id="b_s" size="3" maxlength="3" value="<? echo $b_s ?>"></td>
    </tr>
    <tr align="center"> 
      <td>Colores</td>
      <td colspan="2" align="left"> 
        <input name="colores" type="text" id="b_i3" size="2" maxlength="2" value="<? echo $colores ?>"></td>
    </tr>
    <tr align="center"> 
      <td colspan="3"><input type="submit" name="Submit" value="Ver"></td>
    </tr>
  </table>
<table align='center'>
<tr>
<td><?	echo $html;?></td>
<td><textarea rows='<? echo ($colores+1) ?>' cols='30'><? echo $cut_paste ?></textarea></td>
</tr>
</table>
</form>
<? 
	include_once("cliente_apex.php");
	solicitar_servicio("/siu/prueba_cliente");
?>
</body>
</html>
