<?php

	function pre($txt)
	{
		echo "<pre>$txt\n\n</pre>";
	}
	
	function ei_vinculo($url, $texto, $imagen=null, $target=null, $css='lista-link')
	{
		if(isset($target)) $target = "target='$target'";
		$html =  "<a href='$url' class='$css' $target>";
		if(isset($imagen)){
			$html .=  recurso::imagen_apl($imagen,true,null,null,$texto);
		}else{
			$html .=  $texto;		
		}
		$html .=  "</a>";
		return $html;
	}

	function ei_separador($titulo="")
/*
	@@acceso: publico
	@@desc: Imprime una barra que divide la pantalla
	@@param: string | Titulo de la barra
*/
	{
		echo "<table width='100%' class='tabla-0'><tr>\n";
		echo "<td class='barra-separador'>$titulo</td>\n";
		echo "</tr></table>\n";
	}
//----------------------------------------------------------------------------------	

	function ei_mensaje($mensaje,$tipo='info',$subtitulo="",$ancho=400)
/*
	@@acceso: publico
	@@desc: Imprime un mensaje en la pantalla
	@@param: string | Texto del mensaje
	@@param: string | Tipo de mensaje (info/error) | info
	@@param: string | Subtitulo del recuadro | vacio
 	@@retorno: string | HTML del mensaje
*/
	{
		if(apex_solicitud_tipo=="consola"){
			echo $mensaje . "\n\n";
			return;
		}		
		$css = $tipo;
		if($tipo=='info'){
			$titulo = "Información";
		}elseif($tipo=='error'){
			$titulo = "ERROR";
		}else{
			$titulo = $tipo;
			$css = "INFO";
		}
		$html = "<table width='$ancho' cellpadding='25' align='center'>
		        <tr><td>
				<table width='100%' class='mensaje-$css'>
		        <tr><td class='mensaje-titulo-$css'>$titulo";
		$html.=	"</td></tr>";
		if($subtitulo!=""){
			$html.=	"<tr><td class='mensaje-subtitulo-$css'>$subtitulo</td></tr>";
		}
		$html.=	"<tr><td class='mensaje-cuerpo-$css'>$mensaje</td></tr>
				</table>
				</td></tr>
				</table>\n";
		return $html;
	}
//----------------------------------------------------------------------------------	

	function ei_nota($texto, $clase='ef-etiqueta')
/*
	@@acceso: publico
	@@desc: Imprime una nota
	@@param: string | Texto a mostrar
*/
	{
		echo 	"<div align='center'><table class='tabla-0' width='100%'>
				<tr>
				<td align='center'  style='padding: 10px 10px 10px 10px;' class='$clase'>
				$texto</td></tr></table></div>";
	}
//----------------------------------------------------------------------------------	

	function ei_texto($texto,$titulo=null)
/*
	@@acceso: publico
	@@desc: Imprime un texto en la pantalla
	@@param: string | Texto a mostrar
	@@param: string | Titulo del texto
*/
	{
		echo "<div align='center'><table border='0' cellspacing='0' cellpadding='10'>";
		if(isset($titulo)) echo "<tr><td align='center'>$titulo</td></tr>";
		echo "<tr><td align='center'><pre>";
		print_r(htmlspecialchars($texto));
		echo "<pre></td></tr></table></div>";
	}
//----------------------------------------------------------------------------------	

	function ei_centrar($html, $ancho="100%")
/*
	@@acceso: publico
	@@desc: Imprime el parametro centrado en la pantalla
	@@param: string | HTML a mostrar
*/
	{
		echo "<table width='$ancho' border='0' cellspacing='0' cellpadding='10' align='center'>";
		echo "<tr><td align='center'>";
		echo $html;		
		echo "</td></tr></table>";
	}
//----------------------------------------------------------------------------------	
    
    function enter()
/*
	@@acceso: publico
	@@desc: Imprime un salto de linea
*/
	{
        echo "<br>\n";
    }

//----------------------------------------------------------------------------------	

	function gif_nulo($ancho=1,$alto=1,$nota="")
/*
	@@acceso: publico
	@@desc: Imprime un GIF transparente. Util para forzar el posicionamiento de contenido
	@@param: int | ancho | 1
	@@param: int | alto | 1
	@@param: string | Mensaje en el Mouseover | vacio
*/
	{
        $alt = "";
        if($nota!="") $alt = " alt='$nota' ";
		return "<img src='". recurso::imagen_apl("nulo.gif"). "' width='$ancho' height='$alto' $alt>";
	}
//----------------------------------------------------------------------------------	

	function ei_linea($ancho="100%")
/*
	@@acceso: publico
	@@desc: Imprime una barra que divide la pantalla
	@@param: string | Ancho de la linea
*/
	{
		echo "<table width='100%' class='tabla-0'><tr>\n";
		echo "<td class='barra-separador'>".gif_nulo($ancho,1)."</td>\n";
		echo "</tr></table>\n";
	}
//----------------------------------------------------------------------------------	
	
	function ei_cuadro_vertical($datos,$titulo,$col_ver=array(),$col_formato=array(),$ancho="90%")
/*
	@@acceso: protegido
	@@desc: Genera una tabla vertical del tipo propiedad: valor
	@@param: array | Datos que hay que mostrar
	@@param: string | Titulo
	@@param: array | 
	@@param:
	@@param:
 	@@retorno:
*/
	{
		echo "<table width='$ancho' align='center' class='tabla-0'>";
		echo "<tr><td colspan='2' class='lista-titulo'>$titulo&nbsp;&nbsp;</td></tr>";
		foreach($datos as $nombre=>$valor){
			$temp = ucwords(ereg_replace("_"," ",$nombre));
			echo "<tr><td class='lista-col-titulo' width='120'>$temp</td>";
			if(isset($col_formato[$nombre])){//Callback de formateo
				$funcion = $col_formato[$nombre];
				$valor = $funcion($valor);
			}
			if(isset($col_ver[$nombre])){//Estilo CSS
				echo "<td class='lista-{$col_ver[$nombre]}'>$valor</td></tr>";
			}else{
				echo "<td class='lista-t'>$valor</td></tr>";
			}
		}
		echo "</table>";
	}
//----------------------------------------------------------------------------------	

	function ei_tabla($tabla,$identificador="Tabla NN")
/*
	@@acceso: publico
	@@desc: 
	@@param: 
	@@retorno:
*/
	// Dumpea un array de dos dimensiones cuyas claves son NUMERICAS y ascendentes
	{
		$filas = count($tabla);
		$columnas = count($tabla[1]);
		echo "<br><table width='98%' border=1 bgcolor='0000ff' align='center' cellpadding='2'>\n";
		echo "  <tr><td align='center' colspan='".($columnas+1)."' bgcolor='ff0000'><b>$identificador</b></td></tr>\n";		
		echo "<tr>\n";
			echo "   <td align='center' bgcolor='ffcccc'>&nbsp;</td>\n";
		for ($y=0;$y<$columnas;$y++)
		{
			echo "   <td align='center' bgcolor='ffeeaa'>$y</td>\n";
		}
		echo "</tr>\n";
		for ($x=0;$x<$filas;$x++)
		{
			echo "<tr>\n";
			echo "   <td align='right' bgcolor='ffeeaa'>$x</td>\n";
			for ($y=0;$y<$columnas;$y++)
			{
				echo "   <td align='right' bgcolor='ffffff'>" . $tabla[$x][$y] . "</td>\n";
			}
			echo "</tr>\n";
		}
		echo "</table>";
		echo "<br>";
	}
//----------------------------------------------------------------------------------	

	function ei_arbol($arbol,$identificador="DUMPEO de VALORES",$ancho="50%")
/*
	@@acceso: publico
	@@desc: 
	@@param: 
	@@retorno:
*/
	{
		//Me estan llamando por consola??
		if(apex_solicitud_tipo=="consola"){
			//echo "<pre>";
			print_r($arbol);
			//echo "</pre>";
			return;
		}		

		//Es un array?
		if(is_array($arbol)){
			echo "<div  align='center'><br>";
			echo "<table class='tabla-0' width='$ancho'>";
			echo "<tr><td class='arbol-titulo'><b>$identificador</b></td></tr>\n";		
			echo "<tr><td class='arbol-valor-array'>\n";
			ei_arbol_nivel($arbol);
			echo "</td></tr>\n";
			echo "</table>\n";
			echo "</div><br>";
		}else{
			echo ei_mensaje($arbol,null,$identificador);
		}
	}

	function ei_arbol_nivel($nivel) 
	{
		$estilo="";
		static $n = 0;
		echo "<table width='100%' class='tabla-0'>\n";
		foreach( $nivel as $valor => $contenido )
		{
			if($estilo=="arbol-etiqueta1"){
				$estilo="arbol-etiqueta2";
			}else{
				$estilo="arbol-etiqueta1";
			}
			echo "<tr><td class='$estilo' width='5%'><b>$valor</b></td>\n";
			if (is_array($contenido))
			{
				echo "<td class='arbol-valor-array'>\n";
				$n++;
				ei_arbol_nivel($contenido);
				$n--;
				echo "</td>\n";
			} else {
				if(is_object($contenido)){
					//El elemento es un objeto.
					echo "<td class='arbol-valor-objeto'>objeto&nbsp;(CLASE&nbsp;<b>" . get_class($contenido) ."</b>)</td>\n";
				}elseif(is_null($contenido)){
					echo "<td class='arbol-valor-null'>null</td>\n";
				}else{
					echo "<td class='arbol-valor'>" . ereg_replace("\n","<br>",$contenido) ."</td>\n";
				}
			}
			echo "</tr>\n";
			
		}
		echo "</table>\n";	
	}
//----------------------------------------------------------------------------------	
   
	function ei_cronometro_solicitud($id_solicitud,$ancho="100%")
	{
		global $db, $ADODB_FETCH_MODE, $solicitud;
        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
        $sql = "SELECT marca, nivel_ejecucion as nivel, texto, tiempo FROM apex_solicitud_cronometro
				        WHERE solicitud = '$id_solicitud' ORDER BY marca";
	    $rs = $db["instancia"][apex_db_con]->Execute($sql);
    	if(!$rs){
	    	$solicitud->observar("error","CRONOMETRO: No se genero un RECORDSET" . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql] $sql",false,true,true);
    	}elseif($rs->EOF){
	    	$solicitud->observar("info","No existe cronometracion para la SOLICITUD especificada",false,true,false);
		}else{
	        $cron = $rs->getArray();
    	    ei_cronometro($cron,$ancho);
		}
	}
//----------------------------------------------------------------------------------	


    function ei_cronometro($datos,$ancho="100%")
/*
	@@acceso: publico
	@@desc: 
	@@param: 
	@@retorno:
*/
    {
        //Calculo los porcentuales
        $tiempo_total = 0;
        for($a=0;$a<count($datos);$a++){
            $tiempo_total += $datos[$a]["tiempo"];
        }
        for($a=0;$a<count($datos);$a++){
            $datos[$a]["porcentaje"] =(($datos[$a]["tiempo"] * 100) / $tiempo_total);
        }
        //Genero HTML
        $ancho_grafico = 200;
        $porcentaje_total = 0;
        $barra_mayor = 30;
        $alto_barra = 10;
        $margen = 10;    
   		echo "<table width='$ancho' align='center' class='lista-tabla'>";
		echo"<tr><td class='lista-titulo'>".recurso::imagen_apl("cronometro.gif",true)."&nbsp;$tiempo_total segundos</td>";
		echo"<tr>
			    <td>
				<TABLE width='100%' class='tabla-0'>\n";
		echo"   <TR>\n";
		echo"     <td  class='lista-col-titulo'>#</td>\n";
		echo"     <td  class='lista-col-titulo'  width='90%'>Obs.</td>\n";
		echo"     <td  class='lista-col-titulo'>Tiempo</td>\n";
		echo"     <td  class='lista-col-titulo'>%</td>\n";
		echo"     <td  class='lista-col-titulo'>&nbsp;</td>\n";
  		echo"  </TR>\n";
        for($a=0;$a<count($datos);$a++){
            $porcentaje = number_format($datos[$a]['porcentaje'],2,',','.');
            if(!(($datos[$a]['texto']=="basura")&&($porcentaje < 1)))
            {
	    		if($datos[$a]['texto']=="basura"){
               		$texto = "NO ETIQUETADO";
                }else{
                	$texto = $datos[$a]['texto'];
                }
           		echo"   <TR>\n";
                if(!($datos[$a]["porcentaje"] > $barra_mayor)){
            		echo"     <td  class='lista-e'>{$datos[$a]['marca']}</td>\n";
        	        echo"     <td  class='lista-t' width='90%'>$texto</td>\n";
    	        	echo"     <td  class='lista-n'>{$datos[$a]['tiempo']}&nbsp;s</td>\n";
    	        	echo"     <td  class='lista-n'>$porcentaje&nbsp;%</td>\n";
                }else{
            		echo"     <td  class='lista-e2'>{$datos[$a]['marca']}</b></td>\n";
	        	    echo"     <td  class='lista-t' width='90%'><b>$texto</b></td>\n";
    	        	echo"     <td  class='lista-n'><b>{$datos[$a]['tiempo']}&nbsp;s</b></td>\n";
    	        	echo"     <td  class='lista-n'><b>$porcentaje&nbsp;%</b></td>\n";
                    $barra_mayor = $porcentaje;
                }
                $ancho_barra = ($porcentaje /100 )* $ancho_grafico;
            	echo"     <td  class='cron-base'>\n";
                if ($porcentaje >= 1.00){
    				echo"  <TABLE class='tabla-0'>\n";
        	    	echo"  <TR>\n";
                    if($datos[$a]['texto']=="basura"){
                		echo"  <td  class='cron-basura'>";
                    }else{
                		echo"  <td  class='cron-{$datos[$a]['nivel']}'>";
                    }
                    echo gif_nulo($ancho_barra,$alto_barra,"NIVEL: " .$datos[$a]['nivel']);
                    echo "</td>\n";
          	    	echo"  </TR>\n";
	            	echo"  </TABLE>\n";
                }
                echo"     </td>\n";
          		echo"  </TR>\n";
            }
            $porcentaje_total += $datos[$a]['porcentaje'];
        }
		echo"   <TR>\n";
		echo"     <td  class='lista-col-titulo'></td>\n";
		echo"     <td  class='lista-col-titulo'>TOTAL</td>\n";
		echo"     <td  class='lista-e'>". number_format($tiempo_total,2,',','.') ."&nbsp;s</td>\n";
		echo"     <td  class='lista-e'>". number_format($porcentaje_total,2,',','.') ."&nbsp;%</td>\n";
      	echo"     <td  class='lista-col-titulo'>\n";
        echo gif_nulo(((($barra_mayor /100 )* $ancho_grafico) + $margen),10);
        echo"     </td>\n";
  		echo"  </TR>\n";
		echo"  </TABLE>\n";
        echo"  </td>\n";
  		echo"  </TR>\n";
		echo"  </TABLE>\n";
    }
//----------------------------------------------------------------------------------		
		
	function ei_html_cabecera($titulo, $css="", $estilo_body=null)
/*
	@@acceso: publico
	@@desc: 
	@@param: 
	@@retorno:
*/
	{
		global $color_serie;
		if(isset($estilo_body)) $estilo_body = "class='$estilo_body'";
?>		
<HTML>
<HEAD>
<title><? echo $titulo ?></title>
<? if ( $css != "" ) echo "<link href='". $css . "' rel='stylesheet' type='text/css'>"; ?>
<style type="text/css">
#dhtmltooltip{
position: absolute;
width: 130px;
border: 1px solid <? echo $color_serie["p"][1] ?>;
padding: 2px;
background-color: <? echo $color_serie["s"][6] ?>;
visibility: hidden;
z-index: 1;
font-size: 10;
color: <? echo $color_serie["p"][1] ?>;
}
</style>
</HEAD>
<BODY leftmargin='0' topmargin='0' marginwidth='0' marginheight='0' onLoad='firstFocus()' <? echo $estilo_body ?>>
<div id="dhtmltooltip"></div>
<script type="text/javascript">
/***********************************************
* Cool DHTML tooltip script- © Dynamic Drive DHTML code library (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
***********************************************/
var offsetxpoint=-120; //Customize x offset of tooltip
var offsetypoint=20; //Customize y offset of tooltip
var ie=document.all;
var ns6=document.getElementById && !document.all;
var enabletip=false;
if (ie||ns6)
var tipobj=document.all? document.all["dhtmltooltip"] : document.getElementById? document.getElementById("dhtmltooltip") : "";

function ietruebody(){
return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body;
}

function ddrivetip(thetext, thecolor, thewidth){
if (ns6||ie){
if (typeof thewidth!="undefined") tipobj.style.width=thewidth+"px";
if (typeof thecolor!="undefined" && thecolor!="") tipobj.style.backgroundColor=thecolor;
tipobj.innerHTML=thetext;
enabletip=true;
return false;
}
}

function positiontip(e){
if (enabletip){
var curX=(ns6)?e.pageX : event.x+ietruebody().scrollLeft;
var curY=(ns6)?e.pageY : event.y+ietruebody().scrollTop;
//Find out how close the mouse is to the corner of the window
var rightedge=ie&&!window.opera? ietruebody().clientWidth-event.clientX-offsetxpoint : window.innerWidth-e.clientX-offsetxpoint-20;
var bottomedge=ie&&!window.opera? ietruebody().clientHeight-event.clientY-offsetypoint : window.innerHeight-e.clientY-offsetypoint-20;

var leftedge=(offsetxpoint<0)? offsetxpoint*(-1) : -1000;

//if the horizontal distance isn't enough to accomodate the width of the context menu
if (rightedge<tipobj.offsetWidth)
//move the horizontal position of the menu to the left by it's width
tipobj.style.left=ie? ietruebody().scrollLeft+event.clientX-tipobj.offsetWidth+"px" : window.pageXOffset+e.clientX-tipobj.offsetWidth+"px";
else if (curX<leftedge)
tipobj.style.left="5px";
else
//position the horizontal position of the menu where the mouse is positioned
tipobj.style.left=curX+offsetxpoint+"px";

//same concept with the vertical position
if (bottomedge<tipobj.offsetHeight)
tipobj.style.top=ie? ietruebody().scrollTop+event.clientY-tipobj.offsetHeight-offsetypoint+"px" : window.pageYOffset+e.clientY-tipobj.offsetHeight-offsetypoint+"px";
else
tipobj.style.top=curY+offsetypoint+"px";
tipobj.style.visibility="visible";
}
}

function hideddrivetip(){
if (ns6||ie){
enabletip=false;
tipobj.style.visibility="hidden";
tipobj.style.left="-1000px";
tipobj.style.backgroundColor='';
tipobj.style.width='';
}
}
document.onmousemove=positiontip;
</script>

<script type="text/javascript">	

/*
 onLoad='firstFocus()'
*/
function firstFocus()
{
	for (var i=0; i< document.forms.length; i++)
	{
		var formulario = document.forms[i];
		for (j=0;j<formulario.length;j++)
		{
			var elemento = formulario.elements[j];
			var display = elemento.style.display;
			if ((elemento.type=="text") && (!elemento.disabled) 
				&& ( display != 'none') && ( display != 'hidden') )
			{
				//alert('El display es: '  + display  );
			   elemento.focus();
			   return;
			}
		}
	}
}
</script>
<?
	}
//----------------------------------------------------------------------------------		

	function ei_html_pie()
/*
	@@acceso: publico
	@@desc: 
	@@param: 
	@@retorno:
*/
	{
		echo "</BODY>\n";
		echo "</HTML>\n";
	}
?>