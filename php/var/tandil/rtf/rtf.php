<?	//------------------------------------ clase RTF -----------------------------------
	//----	implementa los metodos que permiten generar archivos en formato rtf  -------
	// ---------------------------------------------------------------------------------
	// ----------------------------------- IMPORTANTE  ---------------------------------
	// ---Cuando se termine de generar el string con las funciones de esta clase, se debe
	// --- concatenar una llave que cierre el documento generado. ("}")
class RTF
{
	
	function generarEncabezadoHTML($archivo="Documento",$contentDisposition ="attachment")
	// Retorna el header del documento html que generara el documento rtf
	// $archivo = nombre del archivo rtf a generar
	// $contentDisposition = la manera en la que se quiere generar el documento rtf. (attachment/inline/..)
	// esta funcion debe ser ejecutada con un eval antes de llamar a generarDefinicion
	// ej.: eval (rtf::generarEncabezadoHTML());
	{
		return ' header("Content-type: application/rtf");'.
			'header("Content-Disposition: '.$contentDisposition.';filename= '.$archivo.'.rtf");';
	}

// --------------------------------------------------------------------------------------   	
	function generarDefinicion()
	// Retorna la definición del documento RTF. 
	// Debe ser invocado al inicio de la generacion de RTF.
	{
		return "{\\rtf1\\ansi\\ansicpg1252\\uc1 \\deff0\\deflang1033\\deflangfe3082{\\fonttbl{\\f0\\froman\\fcharset0\\fprq2{\\*\\panose 02020603050405020304}Times New Roman;}{\\f75\\froman\\fcharset238\\fprq2 Times New Roman CE;}{\\f76\\froman\\fcharset204\\fprq2 Times New Roman Cyr;}".
		"{\\f78\\froman\\fcharset161\\fprq2 Times New Roman Greek;}{\\f79\\froman\\fcharset162\\fprq2 Times New Roman Tur;}{\\f80\\froman\\fcharset186\\fprq2 Times New Roman Baltic;}}{\\colortbl;\\red0\\green0\\blue0;\\red0\\green0\\blue255;\\red0\\green255\\blue255;".
		"\\red0\\green255\\blue0;\\red255\\green0\\blue255;\\red255\\green0\\blue0;\\red255\\green255\\blue0;\\red255\\green255\\blue255;\\red0\\green0\\blue128;\\red0\\green128\\blue128;\\red0\\green128\\blue0;\\red128\\green0\\blue128;\\red128\\green0\\blue0;\\red128\\green128\\blue0;".
		"\\red128\\green128\\blue128;\\red192\\green192\\blue192;}{\\stylesheet{\\nowidctlpar\\widctlpar\\adjustright \\fs20\\lang3082\\cgrid \\snext0 Normal;}{\\*\\cs10 \\additive Default Paragraph Font;}}{\\info{\\title prueba}{\\author pabrile}{\\operator pabrile}".
		"{\\creatim\\yr2001\\mo11\\dy14\\hr12\\min5}{\\revtim\\yr2001\\mo11\\dy14\\hr12\\min5}{\\version1}{\\edmins0}{\\nofpages1}{\\nofwords0}{\\nofchars0}{\\nofcharsws0}{\\vern73}}\\paperw15840\\paperh12240\\margl1411\\margr1411\\margt1699\\margb1699 ".
		"\\deftab708\\widowctrl\\ftnbj\\aenddoc\\hyphhotz425\\formshade\\viewkind1\\viewscale100\\pgbrdrhead\\pgbrdrfoot \\fet0\\sectd \\lndscpsxn\\psz1\\linex0\\endnhere\\sectdefaultcl {\\*\\pnseclvl1\\pnucrm\\pnstart1\\pnindent720\\pnhang{\\pntxta .}}{\\*\\pnseclvl2".
		"\\pnucltr\\pnstart1\\pnindent720\\pnhang{\\pntxta .}}{\\*\\pnseclvl3\\pndec\\pnstart1\\pnindent720\\pnhang{\\pntxta .}}{\\*\\pnseclvl4\\pnlcltr\\pnstart1\\pnindent720\\pnhang{\\pntxta )}}{\\*\\pnseclvl5\\pndec\\pnstart1\\pnindent720\\pnhang{\\pntxtb (}{\\pntxta )}}{\\*\\pnseclvl6".
		"\\pnlcltr\\pnstart1\\pnindent720\\pnhang{\\pntxtb (}{\\pntxta )}}{\\*\\pnseclvl7\\pnlcrm\\pnstart1\\pnindent720\\pnhang{\\pntxtb (}{\\pntxta )}}{\\*\\pnseclvl8\\pnlcltr\\pnstart1\\pnindent720\\pnhang{\\pntxtb (}{\\pntxta )}}{\\*\\pnseclvl9\\pnlcrm\\pnstart1\\pnindent720\\pnhang".
		"{\\pntxtb (}{\\pntxta )}}\\pard\\plain \\nowidctlpar\\widctlpar\\adjustright \\fs20\\lang3082\\cgrid ";
	}

// --------------------------------------------------------------------------------------   
	 
	function generarHeader($titulo,$orientacion="portrait")
	// Retorna el Header del documento. $titulo = titulo del documento;$orientacion= landscape/portrait
	// Debe ser invocado despues de Definicion (2do).
	{
		$tamanio = 108;//portrait
	   	$ort = "";
	 	if ($orientacion=="landscape"){
			 $tamanio = 138;
			 $ort = "\\lndscpsxn";
		}
		return "{\\header". $ort .
		"\\trowd \\trqc\\trrh280\\trleft16 \\cellx2036 \\cellx".$tamanio."36 \\f2\\fs20\\lang3082\\pard\\intbl\n ".
	//	"\\trowd \\trqc\\trrh280\\trleft16 \\cellx".$tamanio."36 \\f2\\fs20\\lang3082\n ".
		"\\ql{\\pict\\wmetafile8\\picw3175\\pich1234\\picscaley99\\picwgoal1800\\pichgoal700\n".
		"010009000003290100000000F80000000000050000000B0200000000050000000C02D204670C03\n".
		"0000001E00050000000C0223005A00050000000B020000000005000000070104000000F8000000\n".
		"430F2000CC00000023005A000000000023005A0000000000280000005A00000023000000010001\n".
		"0000000000A4010000120B0000120B00000200000000000000FFFFFF0000000000FFFFFFFFFFFF\n".
		"FFFFFFFFFFC0FFFFFFFFFFFFFFFFFFFFFFC0000000000000000000000000000000000000000000\n".
		"00000001F0F000FFFFFF0003FFFFC003F0F800FFFFFE0007FFFFC00FF0FE00FFFFFC201FFFFFC0\n".
		"1FF0FF00FFFFF8603FFFFFC03FF0FF80FFFFF0E03FFFFFC03FF0FFC0FFFFE1E07FFFFFC07FF0FF\n".
		"C0FFFFC3E07FFFFFC0FFF0FFC0FFFF87E0FFFFFFC0FFF0FFE0FFFF0FE0FFFFFFC0FFF0FFE0FFFE\n".
		"1FE1FFFFFFC0FFF0FFE0FFF83FE1FFFFFFC0FFF0FFE0FFF07FE000000000FFF0FFE0FFE0FFE000\n".
		"000000FFF0FFE0FFC3FFE000000000FFF0FFE0FF87FFE000000000FFF0FFE0FF0FFFE1FFFFFFC0\n".
		"FFF0FFE0FE1FFFE1FFFFFFC0FFF0FFE0FC3FFFE0FFFFFFC0FFF0FFE0F87FFFE0FFFFFFC0FFF0FF\n".
		"E0F0FFFFE07FFFFFC0FFF0FFE0E1FFFFE07FFFFFC0FFF0FFE0C3FFFFE03FFFFFC0FFF0FFE087FF\n".
		"FFE01FFFFFC0FFF0FFE00FFFFFE00FFFFFC0FFF0FFE01FFFFFE003FFFFC0FFF0FFE03FFFFFE000\n".
		"FFFFC0000000000000000000000000000000000000000000000000000000000000000000000000\n".
		"FFFFFFFFFFFFFFFFFFFFFFC0FFFFFFFFFFFFFFFFFFFFFFC0050000000701010000000400000027\n".
		"01FFFF030000000000}\n".
		"  \\par \\cell \\pard \\intbl   ".
//		"\\plain\\f2\\fs25\\b ".$titulo." \n".
		"\\plain\\f2\\fs25\\b \\line \\qc ".$titulo." \n".
		"\\par \\cell \\pard \\intbl  \\row".
		"}\n";
	  }

// --------------------------------------------------------------------------------------   
	function generarFooter($orientacion="portrait",$texto="")
	// Retorna el footer del documento. 
	//$orientacion= portrait/landscape.
	//$texto = es el que texto que se imprime al pie de pagina, arriba del nro de pagina.
	// Debe ser invocado luego de generarEncabezado y antes de generarEncabezado o alguan otra salida rtf
	{
		$tamanio = 108;
	 	if ($orientacion=="landscape") $tamanio = 138;
	
		//$fecha = date("d-m-Y");	//Fecha actual
		
		return "{\\footer \\pard\\plain  \n". 
		"\\trowd \\trqc\\trrh280\\trleft16\\trhdr".
		//"\\clbrdrt\\brdrdb ".
		"\\clvertalc\\cellx".$tamanio."36\\pard\\intbl\\shading2000".
		"\\f2\\fs15\\lang3082\\qr {\\tab \\b ".$texto."\\par}".
		"\\f2\\fs15\\lang3082\\qr {\\tab Página \\chpgn\n"."\\par}".
		"\\shading2000\\cell\\pard\\intbl\\row }\n ";
	}	  

// --------------------------------------------------------------------------------------   

	function generarEncabezado($encabezado,$justificado,$ancho)
	// Retorna el encabezado de una tabla (los titulos de las columnas).
	// $encabezado= array(string) con los nombres de las columnas
	// $justificado = array(string) justificado de cada una de las columnas (qc/ql/qr/qj)
	// $ancho = array(string) posiciona a partir de la cual se inicia la columna (en portrait, el ancho maximo es de 108 y en landscape 138)
	// ej: $encabezado[0]="Apellido y Nombre"; $justificado[0]="qc"; $ancho[0]="30"; (a partir de la posicion 30 se pone "Apellido y Nombre", centrado)
	{
		$ret="\\trowd \\trqc\\trleft36\\trhdr\n"; 
		$i=0;
		for ($i=0, $size = count($encabezado); $i<$size;$i++){
			$ret.= "\\clbrdrt\\brdrhair\\clbrdrl\\brdrhair\\clbrdrb\\brdrhair\\clbrdrr\\brdrhair\n".
			"\\clvertalc\\cellx".$ancho[$i]."36";
		}
	
		$ret.="\\intbl\\f2\\fs18\\lang3082";
		for ($i=0, $size = count($encabezado); $i<$size;$i++){
//			$ret.=  "\\b\\shading2000\\".$justificado[$i]." ".$encabezado[$i]."\\cell\\pard\\intbl ";
			$ret.=  "\\b\\shading2000\\".$justificado[$i]."  \\line ".$encabezado[$i]." \\line\\cell\\pard\\intbl ";
		}
		$ret.="\\row \\plain";    
		return $ret;
		
	}


// --------------------------------------------------------------------------------------   
	function generarRegistro($registro,$justificado,$ancho)
	// Retorna una fila de la tabla. 
	// $registro = array(string) con el registro a mostrar como una fila de la tabla. 
	// $justificado = array(string) justificado de cada una de las columnas (qc/ql/qd)
	// $ancho = array(string) posiciona a partir de la cual se inicia la columna (en portrait, el ancho maximo es de 108 y en landscape 138)
	{
//		echo"entro a generar registro";
		$ret= "\\deflang2058\\pard\\plain\\f2\\fs18 ".
		"\\trowd\\trrh280\\trleft36\\trqc\\clvertalc\n";
		 
		$i=0;
		for ($i=0, $size = count($registro); $i<$size;$i++){
			$ret.=   "\\clbrdrb\\brdrhair \\clvertalc\\cellx".$ancho[$i]."36 ";
		}
		$ret.="\\f2\\fs18\\lang3082\\pard\\intbl ";
		for ($i=0, $size = count($registro); $i<$size;$i++){
			$ret.= "\n".$registro[$i]."\\".$justificado[$i]."\\cell\\pard\\intbl";
		}
		$ret.="\\row";    
		//echo"salio de generar registro";
		return $ret;
	}  
	

// --------------------------------------------------------------------------------------   
	///REVISAR  NO ANDA DEL TODO BIEN(NO SOMBREA EL REGISTRO)
	function generarTotal($registro,$justificado,$ancho)
	// Retorna la fila de los totales de la tabla 
	// $registro = array(string) con el registro a mostrar como la ultima fila de la tabla. 
	// $justificado = array(string) justificado de cada una de las columnas (qc/ql/qd)
	// $ancho = array(string) posiciona a partir de la cual se inicia la columna (en portrait, el ancho maximo es de 108 y en landscape 138)
	{
		for ($i=0,$size = count($registro); $i<$size; $i++)
			$nuevoRegistro[$i]="\\shading2000 ".$registro[$i];
	   	return RTF::generarRegistro($registro,$justificado,$ancho);
	}

/* -----------------------------------------------------------------------

function RTFMensajePorDefecto(){
 return "{\\rtf1\\ansi\\deff0\\deftab720{\\fonttbl{\\f0\\fswiss MS Sans Serif;}{\\f1\\froman\\fcharset2 Symbol;}{\\f2\\fswiss\\fprq2 Arial;}{\\f3\\froman Times New Roman;}}".
  "{\\colortbl\\red0\\green0\\blue0;}".
  "\\deflang2058\\pard\\plain\\f2\\fs20 El informe solicitado no contiene datos.\\plain\\f3\\fs20 ".
  "\\par }";
  }
  */
}

?>