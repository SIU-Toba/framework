<?php

class impr_etiqueta_barcode extends impr_etiqueta
/*
	@@acceso: PUBLICO
	@@desc: Clase que implementa una etiqueta dentro de un documento PDF.
*/
{
    /*
     	@@acceso: PUBLICO
    	@@desc: Método constructor.
    	@@param: string | Nombre de la etiqueta en particular. | null
      @@param: puntero a objeto | Puntero al contenedor. | null
    	@@pendiente: casi todo
    */
    function impr_etiqueta_barcode($nombre = null, &$padre)
    {
		parent::impr_etiqueta($nombre, $padre);
    }
    
	
    /*
     	@@acceso: PUBLICO
    	@@desc: Genera las sentencias necesarias para crear el documento pdf para la etiqueta.
    	@@pendiente: todo
    */ 
    function generar_comandos_pdf()
    {
      $comandos = '';    
       if (! is_null($this->texto) && ($this->texto !== ''))
       {
			// --- BARCODE ---
			$barcode = new Image_Barcode_int25();
			$archivo = tempnam ("/tmp", "FOO");
			$barcode->draw($this->texto, 'jpg', $archivo);
			 							
			$imageInfo = getimagesize($archivo);
//			$ancho = $imageInfo[0]/2.1;
//			$alto = $imageInfo[1]/1.8;
			$ancho = x($this->ancho);
			$alto = x($this->alto);
			$comandos .= "\$this->doc_pdf->addJpegFromFile('".$archivo."',
			 				x($this->x_origen),
				 		y($this->hoja_alto, " . ($this->alto_origen + $this->y_origen) ."), ".
				 		$ancho.", ".$alto.
				 		");";
			$this->y_origen = $this->y_origen + 3;
			//------------------
		  	   
          if (is_null($this->tipo_letra) || ($this->tipo_letra == ''))
          {
             $comandos .= "\$this->doc_pdf->selectFont('./fonts/Helvetica.afm');";
          }
          else
          {
             $comandos .= "\$this->doc_pdf->selectFont('./fonts/" . $this->tipo_letra . ".afm');";
          }
          $texto_local = $this->texto;
          $texto_local = ($this->negrita == 1? '<b>' . $texto_local . '</b>': $texto_local);
          $texto_local = ($this->subrrayado == 1? '<c:uline>' . $texto_local . '</c:uline>': $texto_local);
          $texto_local = ($this->italica == 1? '<i>' . $texto_local . '</i>': $texto_local);
          
          $alineado = $this->alineado;
          if ($this->alineado == 'izquierda')
          {
             $alineado = 'left';
          }
          elseif ($this->alineado == 'derecha')
          {
             $alineado = 'right';
          }
          elseif ($this->alineado == 'centrado')
          {
             $alineado = 'center';
          }
          elseif ($this->alineado == 'justificado')
          {
             $alineado = 'full';
          }
          
          if ($this->modo_parrafo == 1)
          {
             $comandos .= "\$this->doc_pdf->ezSetY(y($this->hoja_alto, " . ($this->alto_origen + $this->y_origen) . 
                                                                            " - x_inv(\$this->doc_pdf->getFontHeight($this->tamano_letra))" . 
//                                                                            " + x_inv(\$this->doc_pdf->getFontDecender($this->tamano_letra))" . 
                                                                            "));";
             $comandos .= "\$this->doc_pdf->ezText('$texto_local', " .
                                                  "$this->tamano_letra, " . 
                                                  "array('aleft' => x($this->x_origen), " .
                                                        "'aright' => " . x($this->ancho + $this->x_origen) . ", " .
                                                        "'justification' => '$alineado'));";
          }
          else
          {
             $comandos .= "\$this->doc_pdf->addTextWrap(x($this->x_origen), " .
                                                       "y($this->hoja_alto, " . ($this->alto_origen + $this->y_origen) . 
//                                                                            " + x_inv(\$this->doc_pdf->getFontHeight($this->tamano_letra))" . 
//                                                                            " - x_inv(\$this->doc_pdf->getFontDecender($this->tamano_letra))" . 
                                                                            "), " . 
                                                       "x($this->ancho), " . 
                                                       "$this->tamano_letra, " . 
                                                       "'$texto_local', " .
                                                       "'$alineado', " . 
                                                       "$this->rotacion);";
          }

		  $comandos .= "\$this->doc_pdf->ezSetY(y($this->hoja_alto, " . ($this->alto_origen + $this->y_origen) . 
                                                                        " - x_inv(\$this->doc_pdf->getFontHeight($this->tamano_letra))" . 
//                                                                            " + x_inv(\$this->doc_pdf->getFontDecender($this->tamano_letra))" . 
                                                                        "));";

       }
       return $comandos;
    }

}

?>