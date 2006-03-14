<?php

class impr_etiqueta
/*
	@@acceso: PUBLICO
	@@desc: Clase que implementa una etiqueta dentro de un documento PDF.
*/
{
    /*Atributos de instancia*/
    var $nombre;     //Nombre único asignado al cuerpo según la hoja padre.
    var $padre;      //Puntero al objeto hoja contenedor.
    var $conexion;   //Conexion a la base de datos para obtener la configuracon del cuerpo.
    //Atributos que definen el texto a imprimir.    
    var $texto;          //String a imprimir.
    var $x_origen;       //Coordenada origen horizontal en mm.
    var $y_origen;       //Coordenada origen vertical en mm.
    var $ancho;          //Tamaño horizontal en mm.
    var $alto;           //Tamaño vertical en mm.
    var $letra_tamano;   //Tamaño vertical en puntos.
    var $letra_tipo;     //Tipo de letra ('Helvetica'|'Courier'|'Times'|'Symbol')
    var $negrita;        //Resaltada.
    var $subrrayado;     //Idem.
    var $italica;        //Inclinada.
    var $rotacion;       //En grados.
    var $alineado;       //('izquierda'|'centrado'|'derecha'|'justificado')
    var $modo_parrafo;   //Si debe realizar wrap de las lineas (true|false)
    var $hoja_alto;      //Alto de la hoja en milimetros.
    var $alto_origen;     //Desplazamiento del objeto contenedor desde el comienzo de la pagina.
    
    /*
     	@@acceso: PUBLICO
    	@@desc: Método constructor.
    	@@param: string | Nombre de la etiqueta en particular. | null
      @@param: puntero a objeto | Puntero al contenedor. | null
    	@@pendiente: casi todo
    */
    function impr_etiqueta($nombre = null, &$padre)
    {
       $this->nombre = $nombre;
       $this->padre =& $padre;
       $this->texto = '';
       $this->x_origen = 0.0;
       $this->y_origen = 0.0;
       $this->ancho = 0.0;
       $this->alto = 0.0;
       $this->tamano_letra = 10;
       $this->tipo_letra = 'Helvetica';
       $this->negrita = false;
       $this->subrrayado = false;
       $this->italica = false;
       $this->rotacion = 0.0;
       $this->alineado = 'izquierda';
       $this->modo_parrafo = false;
       $this->conexion =& $padre->conexion;
       $this->hoja_alto = $padre->hoja_alto;
       $this->alto_origen = $padre->alto_origen;
       if (! is_null($padre))
       {
          $this->_recuperar_configuracion();
       }
    }
    
    /*
     	@@acceso: PRIVADO
    	@@desc: Recupera la configuración del nombre de la etiqueta en particular.
    	@@pendiente: todo
    */ 
    function _recuperar_configuracion()
    {
       $this->conexion->SetFetchMode(ADODB_FETCH_ASSOC);
       $rs = $this->conexion->Execute("SELECT * " . 
                                      "FROM impr_etiqueta " . 
                                      "WHERE etiqueta_nombre = '" . $this->nombre . "'");
       if (! $rs->EOF)
       {
          $this->texto = (is_null($rs->fields['texto']) || ($rs->fields['texto'] === '')? '': $rs->fields['texto']);
          $this->x_origen = (is_null($rs->fields['x_origen']) || ($rs->fields['x_origen'] === '')? 0.0: $rs->fields['x_origen']);
          $this->y_origen = (is_null($rs->fields['y_origen']) || ($rs->fields['y_origen'] === '')? 0.0: $rs->fields['y_origen']);
          $this->ancho = (is_null($rs->fields['ancho']) || ($rs->fields['ancho'] === '')? 210.0: $rs->fields['ancho']);
          $this->alto = (is_null($rs->fields['alto']) || ($rs->fields['alto'] === '')? 5.0: $rs->fields['alto']);
          $this->tamano_letra = (is_null($rs->fields['letra_tamanio']) || ($rs->fields['letra_tamanio'] === '')? '10': $rs->fields['letra_tamanio']);
          $this->tipo_letra = (is_null($rs->fields['letra_tipo']) || ($rs->fields['letra_tipo'] === '')? 'Helvetica': $rs->fields['letra_tipo']);
          $this->negrita = (is_null($rs->fields['negrita']) || ($rs->fields['negrita'] === '')? 0: $rs->fields['negrita']);
          $this->subrrayado = (is_null($rs->fields['subrrayado']) || ($rs->fields['subrrayado'] === '')? 0: $rs->fields['subrrayado']);
          $this->italica = (is_null($rs->fields['italica']) || ($rs->fields['italica'] === '')? 0: $rs->fields['italica']);
          $this->rotacion = (is_null($rs->fields['rotacion']) || ($rs->fields['rotacion'] === '')? 0.0: $rs->fields['rotacion']);
          $this->alineado = (is_null($rs->fields['alineado']) || ($rs->fields['alineado'] === '')? 'izquierda': $rs->fields['alineado']);
          $this->modo_parrafo = (is_null($rs->fields['modo_parrafo']) || ($rs->fields['modo_parrafo'] === '')? 0: $rs->fields['modo_parrafo']);
       }
    }
    
    /*
     	@@acceso: PUBLICO
    	@@desc: Define la altura de la etiqueta con respecto al contenedor.
    */    
    function ajustar_posicion($alto = 0.0)
    {
       $this->alto_origen = $alto;
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
       }
       return $comandos;
    }

    /*
     	@@acceso: PUBLICO
    	@@desc: Genera una tabla html con los componentes actuales de la etiqueta.
    	@@pendiente:  todo
    */ 
    function recuperar_estructura()
    {
       $tabla = '<table border=1 width=100% class=tabla_etiqueta><tr valign=top>';
       $tabla .= '<td nowrap width=10%><strong>Etiqueta: ' . $this->nombre . '</strong><br>';
       $tabla .=     'Clase: ' . get_class($this) . '<br>';       
       $tabla .=     'Texto: ' . $this->texto . '<br>';       
       $tabla .=     'X origen: ' . $this->x_origen;
       $tabla .=     '&nbsp;&nbsp;Y origen: ' . $this->y_origen . '<br>';
       $tabla .=     'Ancho: ' . $this->ancho;
       $tabla .=     '&nbsp;&nbsp;Alto: ' . $this->alto . '<br>';
       $tabla .=     'Tipo letra: ' . $this->tipo_letra;
       $tabla .=     '&nbsp;' . $this->tamano_letra . '<br>';
       $tabla .=     'Negrita: ' . ($this->negrita == 1? 'S': 'N');
       $tabla .=     '&nbsp;&nbsp;Subrrayada: ' . ($this->subrrayado == 1? 'S': 'N');
       $tabla .=     '&nbsp;&nbsp;Itálica: ' . ($this->italica == 1? 'S': 'N') . '<br>';       
       $tabla .=     'Rotación: ' . $this->rotacion;
       $tabla .=     '&nbsp;&nbsp;Alineado: ' . $this->alineado . '<br>';
       $tabla .=     'Wrap: ' . ($this->modo_parrafo == 1? 'S': 'N');
       $tabla .= '</td>';       
       $tabla .= '</tr></table>';
       return $tabla;
    }

}

?>