<?php

class impr_documento
/*
	@@acceso: PUBLICO
	@@desc: Clase principal de impresion. Contiene las hojas a imprimir en cada PDF.
*/
{
    /*Atributos de instancia*/
    var $nombre;           //Tipo de documento instanciado (factura, nc, nd, expediente, ...)
    var $hoja_ancho;       //Tamano de la hoja en mm {'ancho' => 210,
    var $hoja_alto;        //                         'alto' => 297 )
    var $hoja_orientacion; //                         'orientacion' => ('portrait'|'landscape')        
    var $hoja_nombre;      //                         'nombre' => 'A4'
    var $hojas;        //Arreglo con las hojas que componen el documento,
                       //ordenadas numericamente. 
    var $doc_pdf;      //Objeto de la clase Cezpdf. Mantiene el documento a imprimir
    var $conexion;     //Conexion a la base de datos para obtener la configuracon del formulario.
    var $hoja_aux;     //Variable auxiliar paar generar las hojas.
    
    /*
     	@@acceso: PUBLICO
    	@@desc: Método constructor.
    	@@param: string | Tipo del documento a instanciar (factura, nota, listado, ...). | null
      @@param: recurso | Conexion a la base de datos del proyecto | null
    	@@pendiente: casi todo
    */
    function impr_documento($nombre = null, &$conexion)
    {
    	toba::logger()->ocultar();
       $this->nombre = $nombre;
       $this->conexion =& $conexion;
       $this->hoja_ancho = 210.0;
       $this->hoja_alto = 297.0;
       $this->hoja_orientacion = 'portrait';
       $this->hoja_nombre = 'A4';
       $this->_recuperar_configuracion();
       $this->doc_pdf =& new Cezpdf($this->hoja_nombre, $this->hoja_orientacion);
       
     }

    /*
     	@@acceso: PRIVADO
    	@@desc: Recupera la configuración de un tipo de reporte en particular.
    	@@pendiente: test
    */ 
    function _recuperar_configuracion()
    {
       $this->conexion->SetFetchMode(ADODB_FETCH_ASSOC);
       $rs = $this->conexion->Execute("SELECT * FROM impr_documento WHERE nombre = '" . $this->nombre . "'");

       if (! $rs->EOF)
       {
          $this->hoja_ancho = (is_null($rs->fields['hoja_ancho'])? 210: $rs->fields['hoja_ancho']);
          $this->hoja_alto = (is_null($rs->fields['hoja_alto'])? 297: $rs->fields['hoja_alto']);
          $this->hoja_orientacion = (is_null($rs->fields['hoja_orientacion'])? 'portrait': $rs->fields['hoja_orientacion']);
          $this->hoja_nombre = (is_null($rs->fields['hoja_tipo'])? 'A4': $rs->fields['hoja_tipo']);

       }
       
    }

    /*
     	@@acceso: PUBLICO
    	@@desc: Inserta una hoja previamente creada en una posicion dada del documento actual.
    	@@param: objeto | Objeto que contiene la hoja a insertar. | null
    	@@param: integer | Posición de la hoja en el documento. Si es null se agrega al final. | null
    	@@pendiente: test
    */ 
    function agregar_hoja(&$hoja, $posicion = null)
    {
       if (! is_null($hoja))
       {
          if (is_null($posicion) || ! is_int($posicion))
          {
             if (count($this->hojas) == 0)
             {
                $posicion = 0;
             }
             else
             {
                $maximo = -100000;
                ksort($this->hojas);
                reset($this->hojas);
                while (list($clave, $valor) = each($this->hojas))
                {
                   if ($clave > $maximo)
                   {
                      $maximo = $clave; 
                   }
                }
                $posicion = intval($maximo) + 1;
             }   
          }
          $this->hojas[$posicion] =& $hoja;
       }  
    }

    /*
     	@@acces: PUBLICO
    	@@desc: Genera un objeto de clase hoja según un arreglo de datos.
    	@@param: objeto | Arreglo con los datos que permiten generar la hoja. | null
      @@return: objeto | Objeto hoja. | null
    	@@pendiente: todo
    */     
    function &generar_hoja($nombre = '', &$datos)
    {
       //Se recuperan los tipos de hojas del documento actual.
       unset($this->hoja_aux);       
       reset($datos);
       $hoja_nombre = key($datos);

       $arreglo =& $datos[$hoja_nombre];
	
       $this->conexion->SetFetchMode(ADODB_FETCH_ASSOC);
       $rs = $this->conexion->Execute("SELECT hoja_nombre, clase FROM impr_hoja WHERE hoja_nombre = '" . $nombre . "'");
       $encontro = false;

       while ((! $rs->EOF) && (! $encontro))
       {
          if ($postfijo = strstr($hoja_nombre, $rs->fields['hoja_nombre']))
          {
             eval("\$this->hoja_aux =& new " . $rs->fields['clase'] . "('" . $rs->fields['hoja_nombre'] . "', \$this);");
             if (is_null($this->hoja_aux))
             {
                return null;
             }
             else
             {
                $encontro = true;
                $this->hoja_aux->cargar_datos($arreglo);
             }
          }
          else
          {
             return null;
          }
          $rs->MoveNext();
       }
       return $this->hoja_aux;
    } 
    
    /*
     	@@acceso: PUBLICO
    	@@desc: Genera un objeto de clase hoja según un arreglo de datos y la agrega en una posicion dada del documento actual.
    	@@param: objeto | Arreglo con los datos que permiten generar la hoja. | null
    	@@param: integer | Posición de la hoja en el documento. Si es null se agrega al final. | null
    	@@pendiente: test
    */ 
    function generar_y_agregar_hoja($nombre = '', &$datos, $posicion = null)
    {
       if (! is_null($datos) && ($nombre !== ''))
       {
          if (is_null($posicion) || ! is_int($posicion))
          {
             if (count($this->hojas) == 0)
             {
                $posicion = 0;
             }
             else
             {
                $maximo = -100000;
                ksort($this->hojas);
                reset($this->hojas);
                while (list($clave, $valor) = each($this->hojas))
                {
                   if ($clave > $maximo)
                   {
                      $maximo = $clave; 
                   }
                }
                $posicion = intval($maximo) + 1;
             }   
          }
                    
          /*---GENERACION DE LA HOJA SEGUN EL ARREGLO DE DATOS DE ENTRADA---*/
          unset($hoja);
          $hoja =& $this->generar_hoja($nombre, $datos);
          /*----------------------------------------------------------------*/
          if (! is_null($hoja))
          {
             $this->hojas[$posicion] =& $hoja;
          }
       }
    }
   
    /*
     	@@acceso: PUBLICO
    	@@desc: Genera el documento pdf en base al estado actual del objeto.
    	@@pendiente:  test
    */ 
    function generar_documento()
    {
       $dir_actual = getcwd();
       $dir_fonts = dirname(__FILE__);
       chdir($dir_fonts);
       $this->doc_pdf->ezSetMargins(0.0, 0.0, 0.0, 0.0);    
       $this->doc_pdf->openHere('Fit');
       $comandos = '';
       if (count($this->hojas) > 0)
       {
          foreach($this->hojas as $orden => $hoja)
          {
             if ($comandos != '')
             {
                $comandos .= "\$this->doc_pdf->ezNewPage();";
             } 
             $comandos .= $this->hojas[$orden]->generar_comandos_pdf();
          }
       }
//       echo $comandos;
//       die();
       eval($comandos);
       $this->doc_pdf->setEncryption('', '', array('print'));
       $this->doc_pdf->ezStream();
       @chdir($dir_actual);
    }

    /*
     	@@acceso: PUBLICO
    	@@desc: Genera el documento pdf en base al estado actual del objeto.
    	@@pendiente:  test
    */ 
    function generar_documento_para_archivo()
    {
       $dir_actual = getcwd();
       $dir_fonts = dirname(__FILE__);
	   chdir($dir_fonts);
       
       $this->doc_pdf->ezSetMargins(0.0, 0.0, 0.0, 0.0);
       $this->doc_pdf->openHere('Fit');
       $comandos = '';
       if (count($this->hojas) > 0)
       {
          foreach($this->hojas as $orden => $hoja)
          {
             if ($comandos != '')
             {
                $comandos .= "\$this->doc_pdf->ezNewPage();";
             } 
             $comandos .= $this->hojas[$orden]->generar_comandos_pdf();
          }
       }
       eval($comandos);
       $this->doc_pdf->setEncryption('', '', array('print'));
       $contenido = $this->doc_pdf->ezOutput();
       chdir($dir_actual);
       return $contenido;
    }
    
    /*
     	@@acceso: PUBLICO
    	@@desc: Genera una tabla html con los componentes actuales del documento.
    	@@pendiente:  todo
    */ 
    function recuperar_estructura()
    {
       $tabla = '<table border=1 width=100% class=tabla_doc><tr valign=top>';
       $tabla .= '<td nowrap width=10%><strong>Documento: ' . $this->nombre . '</strong><br>';
       $tabla .=     'Clase: ' . get_class($this) . '<br>';
       $tabla .=     'Ancho: ' . $this->hoja_ancho . '<br>';
       $tabla .=     'Alto: ' . $this->hoja_alto . '<br>';
       $tabla .=     'Orientación: ' . $this->hoja_orientacion . '<br>';
       $tabla .=     'Tipo: ' . $this->hoja_nombre;
       $tabla .= '</td>';       
       $tabla .= '<td>';
       foreach($this->hojas as $orden => $hoja)
       {
          $tabla .= $this->hojas[$orden]->recuperar_estructura();
       }   
       $tabla .= '</td>';
       $tabla .= '</tr></table>';
       return $tabla;
    }
}
?>