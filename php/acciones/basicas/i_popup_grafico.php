<?
require_once("lib/debug.php");

//Variables
$tipo = $canal->protegidos["tipo"];
$ancho = $canal->protegidos["ancho"];
$alto = $canal->protegidos["alto"];
$titulo =$canal->protegidos["titulo"];
$subtitulo = $canal->protegidos["subtitulo"];
$series = $canal->protegidos["series"];
$categ_1 = $canal->protegidos["categ_1"];
$categ_2 =  $canal->protegidos["categ_2"];
$nom_variable = $canal->protegidos["nom_variable"];
$nom_categ_1 = $canal->protegidos["nom_categ_1"];
$nom_categ_2 = $canal->protegidos["nom_categ_2"];
$clave = $_GET["clave"];

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

$grafico["tipo"] = "pie_fc";
$grafico["series"] = $serie_fc;
$grafico["categ_2"] = $categ_2;
$grafico["titulo"] = $titulo ;
$grafico["vars"] = $vars;
$grafico["ancho"] = $ancho;
$grafico["alto"]= $alto;

//dump_CANAL();
//dump_GET();
//dump_arbol($grafico,"GRAFICO");

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
<a href="javascript:this.close()">
<? echo servicios::grafico($grafico,false); ?>
</a>
</BODY>
</HTML>