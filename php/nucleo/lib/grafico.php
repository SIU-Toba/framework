<?php

//Clave de GET donde se explicita el ID de memoria donde 
//buscar la definicion de la etapa anterior en memoria
define("apex_int_grafico_get","igrag");

class grafico
{
/*
Atencion! Este objeto vive en dos etapas (Es creado en dos request simultaneos)
La etapa de incrustacion en HTML se consume directamente, la otra es transparente
(la solicitud del GIF al server)
 
FORMATO ACTUAL DEL ARRAY DEFINICION:

	$definicion["tipo"]="";//torta|
	$definicion["titulo"]="";
	$definicion["subtitulo"]="";
	$definicion["alto"]="";
	$definicion["ancho"]="";
	$definicion["eje_a_nombre"]=""
	$definicion["eje_a"]="";
	$definicion["eje_b_nombre"]=""
	$definicion["eje_b"]="";
	$definicion["serie"]="";
	$definicion["debug"]="";
*/
    var $id;
	var $etapa;
    var $definicion;
	var $colores;
    
    function grafico()
    {
		global $solicitud;
        //Esta seteada la clave de LLAMADA A GRAFICAR?
		if( $id = $solicitud->hilo->obtener_parametro(apex_int_grafico_get) )
		{
			//SI: Tengo que crear un GIF
			$this->id = $id;
	        $this->etapa = "GRAFICAR";    //SALIDA GIF
            // Cargo la memoria
			$this->definicion = $solicitud->hilo->recuperar_dato($this->id);
            //FALTA: Controlo que se no hara enviado HTML
            //FALTA: controlo que este seteado el array de parametros
			$this->generar_colores();
		}else{
			//NO: El grafico se encuentra en etapa de
			// DEFINICION - INCRUSTACION
			$this->id = uniqid(rand(),1); //Por si hay mas de uno en la pagina
	        $this->etapa = "INCRUSTAR";   //SALIDA HTML (tag <img>)
		}
    }
//------------------------------------------------------------------

	function generar_colores()
	{
		global $color_serie;
		//Creo el conjunto de colores a utilizar:
		//1) Reuno todos los colores que existen.
		$colores = array_merge($color_serie["s"],$color_serie["p"],$color_serie["n"]);
		if(isset($this->definicion["eje_a"])){
	        $necesarios = count($this->definicion["eje_a"]);
		}else{
			$necesarios = 15; //VAlor arbitrario de set minimo de colores
		}
        //Repito la cadena si no me alcanzan
        while($necesarios > count($colores) ){
            $colores = array_merge($colores, $colores);
        }
		//2) Recorto los necesarios para este grafico
        $this->colores = array_slice($colores,0,$necesarios);
	}
//------------------------------------------------------------------

		
    function cargar_definicion($definicion)
    //(ETAPA 1) Carga la definicion del grafico (no esta en el constructor porque
	// en la etapa dos se obtiene de la memoria)
    {
        $this->definicion = $definicion;
    }
//------------------------------------------------------------------
    
    function incrustar_imagen($leyenda=true)
    //(ETAPA 1) Genera la llamada HTML que va a mostrar el grafico
    {
		global $solicitud;
		$this->generar_colores();
        //ei_arbol($this->definicion);
        //1) Se guardan los datos y parametros en la memoria con clave X
        $solicitud->hilo->persistir_dato_sincronizado($this->id,$this->definicion);		
        //2) Se escribe un link al ITEM graficador, se pasa como parametro X
		$vinculo = $solicitud->vinculador->generar_solicitud("toba","/basicos/graficar",array(apex_int_grafico_get => $this->id));

		//---> TITULO y SUBTITULO
		//echo "<br>\n";
		echo "<div  align='center'>";
		echo "<table class='tabla-0'>\n";
		if (isset($this->definicion["titulo"])) echo "<tr><td class='grafico-titulo'>".$this->definicion["titulo"]."</td></tr>";
		if (isset($this->definicion["subtitulo"])) echo "<tr><td class='grafico-subtitulo'>". $this->definicion["subtitulo"] ."</td></tr>";

		//---> CUERPO (el grafico en si o la pantalla de debug)
		echo "<tr><td class='grafico-vacio'>";
		if($this->definicion["debug"]==1){
		//MODO DEBUG, se crea un IFRAME. El request originado en el src="x" genera HTML
			$alto = $this->definicion["alto"];
			$ancho = $this->definicion["ancho"];
			echo "\n<iframe align='center' width='$ancho' height='$alto' src='$vinculo'></iframe>";
		}else{
		//MODO NORMAL, el request siguiente genera un GIF
			echo "\n<img border='0' src='$vinculo'>";
		}
		echo "</td></tr>";

		//---> Genero la LEYENDA
		$generar_leyenda = "generar_leyenda_" . $this->definicion["tipo"];
		echo "<tr><td class='grafico-vacio'>";
        $this->$generar_leyenda();
		echo "</td></tr>";

		echo "</table>";
		echo "</div>";
		//echo "<br>\n";
    }
//------------------------------------------------------------------------------

    function info()
    //(ETAPA 1) Carga la definicion del grafico (no esta en el constructor porque
	// en la etapa dos se obtiene de la memoria)
    {
        ei_arbol($this->definicion,"DEFINICION del GRAFICO");
    }
//------------------------------------------------------------------


    function generar_imagen()
	//(ETAPA 2) Genera el GIF!
    {
		include_once("3ros/jpgraph110/jpgraph.php");
		$generar_gif = "generar_grafico_" . $this->definicion["tipo"];
		if($this->definicion["debug"]==1){
			echo recurso::link_css();
	        //$this->$generar_gif();
			ei_arbol($this->definicion,"GRAFICADOR (definicion recibida)");
			foreach($this->colores as $color)
			{
				$col[] = "<table><tr><td bgcolor='$color'>".gif_nulo(20,10)."</td></tr></table>&nbsp;$color";
			}
			ei_arbol($col,"COLORES");
		}else{
	        $this->$generar_gif();//Disparo la funcion particular al TIPO de grafico
		}
		//ATENCION: No tendria que limpiar la variable de sesion que memorizo todo????
		//Deberia ser una funcion a llamar en el HILO...
    }

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//----------------------  GRAFICOS PARTICULARES --------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------

	function generar_leyenda_torta()
	//(ETAPA 1) Genera la leyenda del grafico
	{
		$colores = array_reverse($this->colores);//No se por que JPGRAPH trabaja al reves
		echo "<table class='tabla-0' width='100%'>";
		for($a=0;$a< count($this->definicion["eje_a"]);$a++)
		{
			echo "<tr><td  class='grafico-leyenda-muestra' bgcolor='".$colores[$a]."'>".gif_nulo(10,5)."</td>";
			echo "<td width='100%' class='grafico-leyenda-texto'>".$this->definicion["eje_a"][$a]."</td></tr>";			
		}
		echo "</table>";
		//ei_arbol($this->definicion);
	}

    function generar_grafico_torta()
    //(ETAPA 2) Generar un GRAFICO tipo TORTA.
	//Supone que hay una sola serie
    {
		global $color_serie;
		include_once("3ros/jpgraph110/jpgraph_pie.php");	
		$alto = $this->definicion["alto"];
		$ancho = $this->definicion["ancho"];
		$graph =& new PieGraph($ancho,$alto,"auto");	
//		$graph->SetScale("textlin");
		$graph->SetColor($color_serie["n"][6]);
		$graph->SetMarginColor($color_serie["n"][6]);
//		$graph->SetShadow();
//		$graph->title->Set($this->definicion["titulo"]);
//		$graph->title->SetFont(FF_FONT1,FS_BOLD);
		//Armo la lista de colores a usar
		$torta =& new PiePlot($this->definicion["serie"]);
		$torta->SetSliceCOlors($this->colores);
		$torta->value->SetFont(FF_FONT1,FS_BOLD);
		$torta->SetSize(0.4);
		$torta->SetCenter(0.5);		
//		$torta->SetLegends($this->definicion["eje_a"]);//Mejor lo muestro en HTML (mas versatil)
		$graph->Add($torta);
		$graph->Stroke();
     }
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------

	function generar_leyenda_bar()
	//(ETAPA 1) Genera la leyenda del grafico
	{
		$colores = array_reverse($this->colores);//No se por que JPGRAPH trabaja al reves
		echo "<table class='tabla-0' width='100%'>";
		for($a=0;$a< count($this->definicion["eje_a"]);$a++)
		{
			echo "<tr><td  class='grafico-leyenda-muestra' bgcolor='".$colores[$a]."'>".gif_nulo(10,5)."</td>";
			echo "<td width='100%' class='grafico-leyenda-texto'>".$this->definicion["eje_a"][$a]."</td></tr>";			
		}
		echo "</table>";
		//ei_arbol($this->definicion);
	}

    function generar_grafico_bar()
    //(ETAPA 2) Generar un GRAFICO tipo TORTA.
	//Supone que hay una sola serie
    {
		global $color_serie;
		include_once("3ros/jpgraph110/jpgraph_bar.php");
		$alto = $this->definicion["alto"];
		$ancho = $this->definicion["ancho"];
		$graph = new Graph($ancho,$alto,"auto");	
		$graph->SetScale("textlin");
		//$graph->SetShadow();
		//$graph->title->Set($titulo);
		//$graph->title->SetFont(FF_FONT1,FS_BOLD);
		$graph->img->SetMargin(40,10,10,20);
		//$graph->legend->Pos(0.03,0.5,"right","center");
		//$graph->SetBox();
		$graph->SetColor($color_serie["n"][6]);#fcfeed#ffffff#d9dbff#bbbbff
		$graph->SetMarginColor($color_serie["p"][6]);
		$barras = new BarPlot($this->definicion["serie"]);
		$barras->SetFillColor($this->colores);
		$graph->Add($barras);
		//Eje Y
//		$graph->yaxis->scale->ticks->SetSize(15,5);
//		$graph->yaxis->scale->SetGrace(1);
		$graph->yaxis->title->Set($this->definicion["eje_b_nombre"]);
		$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
		$graph->yaxis->SetColor($color_serie["p"][6],$color_serie["p"][2]);
		//Eje x
//		$graph->xaxis->title->Set($this->definicion["eje_a_nombre"]);
//		$graph->xaxis->SetTickLabels($this->definicion["eje_a"]);
//		$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
		//Grilla
		$graph->ygrid->Show(); 
		$graph->ygrid->SetColor($color_serie["p"][4]);
		$graph->xgrid->SetLineStyle('dashed'); 
		$graph->xgrid->Show();
		$graph->xgrid->SetColor($color_serie["p"][4]);
		$graph->Stroke();
     }
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------

	function generar_leyenda_gantt(){}
	
	function generar_grafico_gantt()
	{
		global $color_serie;
		include_once ("3ros/jpgraph110/jpgraph_gantt.php");
		//Creo el grafico
		$graph = new GanttGraph(0,0,"auto");
		$graph->SetColor(  $color_serie["s"][6] );
		$graph->SetMarginColor($color_serie["p"][4]);
		//Escala SUPERIOR
		$graph->ShowHeaders(GANTT_HDAY | GANTT_HWEEK | GANTT_HMONTH);
		$graph->scale->SetTableTitleBackground($color_serie["p"][4]);
		$graph->scale->setDateLocale("");
		$graph->scale->week->SetStyle( WEEKSTYLE_FIRSTDAY );
		$graph->scale->week->SetBackGroundColor( $color_serie["p"][5] );
		$graph->scale->month->SetStyle( MONTHSTYLE_SHORTNAMEYEAR4 );
		$graph->scale->month->SetBackGroundColor( $color_serie["p"][5] );
		$graph->scale->SetVertLayout(GANTT_EVEN );

		//---  Genero las actividades  -----------------
		for( $a=0; $a<count($this->definicion['actividades']); $a++)
		{
			$posicion = $this->definicion['actividades'][$a]['posicion'];
			$titulo = $this->definicion['actividades'][$a]['titulo'];
			$fecha_inicio = $this->definicion['actividades'][$a]['fecha_inicio'];
			$fecha_fin = $this->definicion['actividades'][$a]['fecha_fin'];									
			$anotacion = $this->definicion['actividades'][$a]['anotacion'];
			$altura = $this->definicion['actividades'][$a]['altura'];	
			$actividad = new GanttBar($posicion, $titulo, $fecha_inicio, $fecha_fin, $anotacion, $altura);
			$actividad->SetPattern( GANTT_SOLID, $this->colores[$a] );
			$actividad->SetColor( $color_serie["p"][1] );
			$actividad->SetFillColor( $this->colores[$a] );
//			$actividad->SetShadow();
			$graph->Add($actividad);
		}
		//---  Genero los HITOS  ------------------------
		for( $a=0; $a<count($this->definicion['hitos']); $a++)
		{
			$posicion = $this->definicion['hitos'][$a]['posicion'];
			$titulo = $this->definicion['hitos'][$a]['titulo'];
			$comienzo = $this->definicion['hitos'][$a]['fecha'];		
			$anotacion = $this->definicion['hitos'][$a]['anotacion'];		
			$hito = new MileStone($posicion, $titulo, $comienzo, $anotacion);
			$graph->Add($hito);
		}
		//---  Genero las lineas VERTICALES  ------------------------
		for( $a=0; $a<count($this->definicion['lineas']); $a++)
		{
			$dia = $this->definicion['lineas'][$a]['fecha'];
			$titulo = $this->definicion['lineas'][$a]['titulo'];
			$color = $this->definicion['lineas'][$a]['color'];		
			$ancho = $this->definicion['lineas'][$a]['ancho'];		
			$estilo = $this->definicion['lineas'][$a]['estilo'];	
			$linea_vertical = new GanttVLine($dia, $titulo, $color, $ancho, $estilo);
			if(isset($this->definicion['lineas'][$a]['corrimiento'])){
				$linea_vertical->SetDayOffset($this->definicion['lineas'][$a]['corrimiento']);
			}
			$graph->Add($linea_vertical);
		}
				
		//Genero el GRAFICO
		$graph->Stroke();
	}
}
?>
