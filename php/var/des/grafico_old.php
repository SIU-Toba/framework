<?php
require_once("ini.php");
require_once("lib/debug.php");
require_once("3ros/jpgraph110/jpgraph.php");
global $color_serie;//Colores definidos en el INI

//----------------------------------------------------------------------------------
// --------------------------------> Modo DEBUG <------------------------------------
//----------------------------------------------------------------------------------
if($canal->protegidos["debug"]){
	dump_arbol($canal->protegidos,"CANAL");
	echo "<table width='200' align='center'>";
	foreach ($color_serie as $color)
	{
		echo "<tr><td bgcolor='$color' align='center'>$color</td></tr>\n";
	}
	echo "</table>";
	//echo $canal->grafico($canal->protegidos,false);
	exit();
}
//----------------------------------------------------------------------------------
//----------------------------------------------------------------------------------

$tipo=$canal->protegidos["tipo"];
$ancho = $canal->protegidos["ancho"];
$alto = $canal->protegidos["alto"];
$titulo = $canal->protegidos["titulo"];
$subtitulo = $canal->protegidos["subtitulo"];
$series = $canal->protegidos["series"];
$categ_1 = $canal->protegidos["categ_1"];
$categ_2 = $canal->protegidos["categ_2"];
$vars = $canal->protegidos["vars"];
$nom_variable = $canal->protegidos["nom_variable"];
$nom_categ_1 = $canal->protegidos["nom_categ_1"];
$nom_categ_2 = $canal->protegidos["nom_categ_2"];

	if($tipo=="multi_bar")
	//#######################################################################################################
	//############################  Graficos de barras (Multiples categorias) ###############################
	//#######################################################################################################
	{
		include("lib/jpgraph110/jpgraph_bar.php");
		$graph = new Graph($ancho,$alto,"auto");	
		$graph->SetScale("textlin");
		$graph->SetShadow();
		$graph->title->Set($titulo);
		$graph->title->SetFont(FF_FONT1,FS_BOLD);
		$graph->img->SetMargin(60,230,30,30);
		$graph->legend->Pos(0.03,0.5,"right","center");
		$graph->SetBox();
		$graph->SetColor("#ffffff");#fcfeed#ffffff#d9dbff
		$graph->SetMarginColor("#ffffff");
		//Aspectos del grafico dependientes de la cantidad de series...
		if(count($categ_1)>6)
		{	//Graficos con muchas series
			$graph->legend->SetFont(FF_FONT0);
		}
		else
		{	//Graficos con pocas series
			$graph->legend->SetFont(FF_FONT1);
		}
		//Creacion de los plots
		$indice = 0;
		foreach ($series as $serie)
		{
			$ploteos[$indice] = new BarPlot($serie);
			$ploteos[$indice]->SetFillColor($color_serie[$indice]);
			$ploteos[$indice]->SetLegend($categ_1[$indice]);
			$indice++;
		}
		$grupo = new GroupBarPlot($ploteos);
		$graph->Add($grupo);
		//Eje Y
		$graph->yaxis->scale->SetGrace(10);
		$graph->yaxis->title->Set($nom_variable);
		$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
		$graph->yaxis->SetColor('#6028aa','#330066');
		$graph->ygrid->SetColor('#6028aa');
		//Eje x
		$graph->xaxis->title->Set($nom_categ_2);
		$graph->xaxis->SetTickLabels($categ_2);
		$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
		$graph->Stroke();
	}
	elseif($tipo=="bar")
	//#######################################################################################################
	//#######################################  Graficos de barras ###########################################
	//#######################################################################################################
	{
		include("lib/jpgraph110/jpgraph_bar.php");
		$graph = new Graph($ancho,$alto,"auto");	
		$graph->SetScale("textlin");
		//$graph->SetShadow();
		$graph->title->Set($titulo);
		$graph->title->SetFont(FF_FONT1,FS_BOLD);
		$graph->img->SetMargin(50,10,10,20);
		//$graph->legend->Pos(0.03,0.5,"right","center");
		//$graph->SetBox();
		$graph->SetColor("#FFFCF0");#fcfeed#ffffff#d9dbff#bbbbff
		$graph->SetMarginColor("#d9dbff");
		$indice = 0;
		foreach ($series as $serie)
		{
			$ploteos[$indice] = new BarPlot($serie);
			$ploteos[$indice]->SetFillColor($color_serie[$indice]);
//			$ploteos[$indice]->value->Show();
			$indice++;
		}
		$grupo = new GroupBarPlot($ploteos);
		$graph->Add($grupo);
		//Eje Y
//		$graph->yaxis->scale->ticks->SetSize(15,5);
		$graph->yaxis->scale->SetGrace(1);
		$graph->yaxis->title->Set($nom_variable);
		$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
		$graph->yaxis->SetColor('#6028aa','#330066');
		//Eje x
		$graph->xaxis->title->Set($nom_categ_2);
		$graph->xaxis->SetTickLabels($categ_2);
		$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
		//Grilla
		$graph->ygrid->Show(); 
		$graph->ygrid->SetColor('#6028aa');
		$graph->xgrid->SetLineStyle('dashed'); 
		$graph->xgrid->Show();
		$graph->xgrid->SetColor('#6028aa');
		$graph->Stroke();
	}
	elseif($tipo=="lin")
	//#######################################################################################################
	//#######################################  Graficos de lineas  ##########################################
	//#######################################################################################################
	{
		include ("lib/jpgraph110/jpgraph_line.php");
		$graph = new Graph($ancho,$alto,"auto");	
		$graph->SetScale("textlin");
		$graph->SetShadow();
		$graph->title->Set($titulo);
		$graph->title->SetFont(FF_FONT1,FS_BOLD);
		$graph->img->SetMargin(40,30,20,40);
		//Creacion de los plots
		$indice = 0;
		foreach ($series as $serie)
		{
			$ploteos[$indice] = new LinePlot($serie);
			$ploteos[$indice]->SetColor($color_serie[$indice]);
			$ploteos[$indice]->SetWeight(2);
			$ploteos[$indice]->SetLegend($categ_1[$indice]);
			$ploteos[$indice]->mark->SetType(MARK_UTRIANGLE);
			//echo $categ_1[$indice] . "agrego!<br>";
			$indice++;
		}
		for($a=0;$a<count($ploteos);$a++)
		{
			$graph->Add($ploteos[$a]);
		}
		//Eje Y
		$graph->yaxis->title->Set($nom_variable);
		$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
		//Eje x
		$graph->xaxis->title->Set($nom_categ_2);
		$graph->xaxis->SetTickLabels($categ_2);
		$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
		$graph->Stroke();		
	}
	elseif($tipo=="pie")
	//#######################################################################################################
	//#######################################   Grafico de TORTAs   #########################################
	//#######################################################################################################
	{
		include ("lib/jpgraph110/jpgraph_pie.php");
		$graph = new PieGraph($ancho,$alto,"auto");	
//		$graph->SetScale("textlin");
		$graph->SetColor("#ffffff");
		$graph->SetShadow();
		$graph->title->Set($titulo);
		$graph->title->SetFont(FF_FONT1,FS_BOLD);
		$colores = array_slice($color_serie,0,count($series[0]));
		$torta = new PiePlot($series[0]);
		$torta->SetSliceCOlors($colores);
		$torta->value->SetFont(FF_FONT1,FS_BOLD);
		$torta->SetSize(0.3);
		$torta->SetCenter(0.4);		
		$torta->SetLegends($categ_2);
		$graph->Add($torta);
		$graph->Stroke();
	}
	elseif($tipo=="pie_fc")
	//#######################################################################################################
	//#######################################   Grafico de TORTAs FC  #######################################
	//#######################################################################################################
	{
		include ("lib/jpgraph110/jpgraph_pie.php");
		$graph = new PieGraph($ancho,$alto,"auto");	
		$graph->title->Set($titulo);
		$graph->title->SetFont(FF_FONT1,FS_BOLD);
		$graph->legend->Pos(0.05,$vars['rel_leyenda'],"left","top");
		$graph->SetColor("#ffffff");
		$colores = array_slice($color_serie,0,count($series));
		$torta = new PiePlot($series);
		$torta->value->SetFont(FF_FONT1,FS_BOLD);
		$torta->SetSliceCOlors($colores);
		$torta->SetCenter(0.5,$vars['rel_torta']);
		if($vars['letra_chica']){
			$graph->legend->SetFont(FF_FONT0);
		}else{
			$graph->legend->SetFont(FF_FONT1);
		}
		$torta->SetSize(80);
		$torta->SetLegends($categ_2);
		$graph->Add($torta);
		$graph->Stroke();
	}
?>