<?
	
	$indice_zona = apex_hilo_qs_edc;
	if(isset($_GET[$indice_zona])){
		$this->contexto['elemento']=$_GET[$indice_zona];
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$sql = 	"	SELECT	*
					FROM	apex_clase
					WHERE	clase='".$this->contexto['elemento']."';";
		$rs =& $db["instancia"][apex_db_con]->Execute($sql);
		if(!$rs){
			monitor::evento("bug","Generador de PDF para CLASE: NO se pudo cargar definicion: $this->contexto['elemento']. - [SQL]  $sql - [ERROR] " . $db["instancia"][apex_db_con]->ErrorMsg() );
		}elseif($rs->EOF){
			die("arreglar esta salida");
		}else{
			/************************* genero la salida *******************************/

			$pdf = PDF_new();

			/*  open new PDF file; insert a file name to create the PDF on disk */
			if (PDF_open_file($pdf, "") == 0) {
			    die("Error: " . PDF_get_errmsg($p));
			}

			pdf_set_info($pdf, "Author", "Uwe Steinmann");
			pdf_set_info($pdf, "Title", "Test for PHP wrapper of PDFlib 2.0");
			pdf_set_info($pdf, "Creator", "See Author");
			pdf_set_info($pdf, "Subject", "Testing");

			pdf_begin_page($pdf, 595, 842);
				pdf_add_outline($pdf, "Page 1");
				$font = pdf_findfont($pdf, "Times New Roman", "winansi", 1);
				pdf_setfont($pdf, $font, 14);
				pdf_set_value($pdf, "textrendering", 1);
				pdf_show_xy($pdf, $rs->fields["descripcion"] . " (" . $rs->fields["archivo"] . ")", 50, 750);
				pdf_moveto($pdf, 50, 740);
				pdf_lineto($pdf, 330, 740);
				pdf_stroke($pdf);

/*
				if ((image =PDF_open_image_file(p,"png","image.jpg","",0))==-1)
				{
					monitor::evento("error","No se pudo abrir la imagen");
				}else {
					PDF_place_image(p,image,(float)0.0,(float)0.0,(float)1.0);
					PDF_close_image(p,image);
				}
*/
			pdf_end_page($pdf);
	
			pdf_close($pdf);

			$buf = PDF_get_buffer($pdf);
			$len = strlen($buf);

			header("Content-type: application/pdf");
			header("Content-Length: $len");
			header("Content-Disposition: inline; filename=hello.pdf");
			print $buf;

			PDF_delete($pdf);		

		}
	}else{
		echo ei_mensaje("No se explicito el ELEMENTO (Contexto ZONA)","error");
	}

?>