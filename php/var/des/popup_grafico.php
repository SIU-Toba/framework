<?
require_once("apl_debug.php");

	function debug(){
		global $series,$categ_1,$categ_2;
		//Muestra el estado de las variables
		dump_arbol_t($_GET,"GET");
		dump_arbol_t($series,"series");
		dump_arbol_t($categ_1,"categ_1");
		dump_arbol_t($categ_2,"categ_2");
	}

//Variables
$tipo = "pie_fc";
$ancho = $_GET["ancho"];
$alto = $_GET["alto"];
$titulo = $_GET["titulo"];
$subtitulo = $_GET["subtitulo"];
$clave = $_GET["clave"];
$series = unserialize(bzdecompress(base64_decode($_GET["series"])));
$categ_1 = unserialize(bzdecompress(base64_decode($_GET["categ_1"])));
$categ_2 = unserialize(bzdecompress(base64_decode($_GET["categ_2"])));
$nom_variable = $_GET["nom_variable"];
$nom_categ_1 = $_GET["nom_categ_1"];
$nom_categ_2 = $_GET["nom_categ_2"];
//Estructuras que hay que mostrar
$serie_fc = $series[$clave];
$categoria = $categ_2;
$titulo = $categ_1[$clave];

$ancho = 300;
$alto_['b_1'] = 15;
$alto_['titulo'] = 22 + (22 * substr_count($titulo,"\n"));
$alto_['torta'] = 240;
$alto_['leyenda'] = (count($serie_fc)*20);
$alto = 0;
foreach($alto_ as $parcial){
	$alto += $parcial;
}
$alto_['total'] = $alto;
$vars['letra_chica'] = 0;
foreach($categoria as $x){
	if(substr_count($x,"\n")>0){
		$vars['letra_chica'] = 1;
		break;
	}
}
$vars['rel_torta'] = ((($alto-$alto_['leyenda'])/$alto)/2);
$vars['rel_leyenda'] = ((($alto-$alto_['leyenda'])/$alto)-0.15);
//dump_arbol_t($vars,"relativos");
//dump_arbol_t($alto_,"Altos Parciales");
//dump_arbol_t($serie_fc,"serie a mostrar");
//dump_arbol_t($categoria,"Categoria");
//debug();

	$qs = "tipo=". $tipo .
		"&series=" .base64_encode(bzcompress(serialize($serie_fc))) . 
		"&categ_2=" . base64_encode(bzcompress(serialize($categ_2))) . 
		"&titulo=" . urlencode($titulo).
		"&vars=" .  base64_encode(bzcompress(serialize($vars))) . 
		"&alto=$alto&ancho=$ancho";
?>
<HTML>
<HEAD>
<title>Analizar Serie</title>
<META http-equiv=Content-Type content="text/html; charset=windows-1252">
<link href="css/violetas.css" rel="stylesheet" type="text/css">
<script language='JavaScript'>
	window.resizeTo(<? echo "$ancho+10,$alto+30" ?>);
</script>
</HEAD>
<BODY leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<a href="javascript:this.close()"><img src='apl_graficador.php?<? echo $qs ?>' alt="Haga CLICK para <? echo "\n" ?> cerrar la ventana" border='0'></a>
</BODY>
</HTML>