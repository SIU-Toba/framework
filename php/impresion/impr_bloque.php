<?php

class impr_bloque
/*
	@@acceso: PUBLICO
	@@desc: Clase que implemta un bloque repetitivo de etiquetas 
            dentro de una hoja dentro de un documento PDF.
*/
{
    /*Atributos de instancia*/
    var $nombre;      //Nombre único asignado al cuerpo según la hoja padre.
    var $padre;       //Puntero al objeto hoja contenedor.
    var $conexion;    //Conexion a la base de datos para obtener la configuracon del cuerpo.
    //Atributos que definen el bloque a repetir.    
    var $x_origen;    //Coordenada origen horizontal en mm.
    var $y_origen;    //Coordenada origen vertical en mm.
    var $ancho;       //Tamaño horizontal en mm.
    var $alto;        //Tamaño vertical en mm.
    var $fila_alto;   //Alto de la fila en cada repeticion.
    var $hoja_alto;   //Alto de la hoja en milimetros.
    var $alto_origen; //Desplazamiento del objeto contenedor desde el comienzo de la página.
    var $componentes; //Arreglo con las etiquetas que forman un renglon del bloque.
    var $datos; //Arreglo con las etiquetas que forman un renglon del bloque.      
    /*Variables auxiliares*/
    var $etiqueta_aux;
    var $grafico_aux;
        
    /*
     	@@acceso: PUBLICO
    	@@desc: Método constructor.
    	@@param: string | Nombre del bloque en particular. | null
      @@param: puntero a objeto | Puntero al contenedor. | null
    	@@pendiente: casi todo
    */
    function impr_bloque($nombre = null, &$padre)
    {
       $this->nombre = $nombre;
       $this->padre =& $padre;
       $this->x_origen = 0.0;
       $this->y_origen = 0.0;
       $this->ancho = 0.0;
       $this->alto = 0.0;
       $this->fila_alto = 0.0;
       $this->conexion =& $padre->conexion;
       $this->hoja_alto = $padre->hoja_alto;
       $this->alto_origen = $padre->alto_origen;
       $this->datos = null;
       if (! is_null($padre))
       {
          $this->_recuperar_configuracion();
       }
    }

    /*
     	@@acceso: PRIVADO
    	@@desc: Recupera la configuración del nombre del bloque en particular.
    	@@pendiente: todo
    */ 
    function _recuperar_configuracion()
    {
       $this->conexion->SetFetchMode(ADODB_FETCH_ASSOC);
       $rs = $this->conexion->Execute("SELECT * " . 
                                      "FROM impr_bloque " . 
                                      "WHERE bloque_nombre = '" . $this->nombre . "'");
       if (! $rs->EOF)
       {
          $this->x_origen = (is_null($rs->fields['x_origen']) || ($rs->fields['x_origen'] === '')? 0.0: $rs->fields['x_origen']);
          $this->y_origen = (is_null($rs->fields['y_origen']) || ($rs->fields['y_origen'] === '')? 0.0: $rs->fields['y_origen']);
          $this->ancho = (is_null($rs->fields['ancho']) || ($rs->fields['ancho'] === '')? 210.0: $rs->fields['ancho']);
          $this->alto = (is_null($rs->fields['alto']) || ($rs->fields['alto'] === '')? 5.0: $rs->fields['alto']);
          $this->fila_alto = (is_null($rs->fields['fila_alto']) || ($rs->fields['fila_alto'] === '')? 5.0: $rs->fields['fila_alto']);
          
          //Se recuperan las etiquetas
          $this->conexion->SetFetchMode(ADODB_FETCH_ASSOC);
          $rs = $this->conexion->Execute("SELECT etiqueta_nombre, clase " . 
                                         "FROM impr_etiqueta " .
                                         "WHERE padre_nombre = '" . $this->nombre . "' " .
                                         "ORDER BY etiqueta_nombre");
          while (! $rs->EOF)
          {
             unset($this->etiqueta_aux);
             eval("\$this->etiqueta_aux =& new " . $rs->fields['clase'] . "('" . $rs->fields['etiqueta_nombre'] . "', \$this);");
             if (! is_null($this->etiqueta_aux))
             {
                $this->componentes[count($this->componentes)] = $this->etiqueta_aux;
             }
             $rs->MoveNext();
          }

          //Se recuperan los graficos
          $this->conexion->SetFetchMode(ADODB_FETCH_ASSOC);
          $rs = $this->conexion->Execute("SELECT grafico_nombre, clase " . 
                                         "FROM impr_grafico " .
                                         "WHERE padre_nombre = '" . $this->nombre . "' " .
                                         "ORDER BY grafico_nombre");
          while (! $rs->EOF)
          {
             unset($this->grafico_aux);
             eval("\$this->grafico_aux =& new " . $rs->fields['clase'] . "('" . $rs->fields['grafico_nombre'] . "', \$this);");
             if (! is_null($this->grafico_aux))
             {
                $this->componentes[count($this->componentes)] = $this->grafico_aux;
             }
             $rs->MoveNext();
          }
       }
    }

    /*
     	@@acceso: PUBLICO
    	@@desc: Define la altura del bloque en el cuerpo para los componentes.
    */
    function ajustar_posicion($alto = 0.0)
    {
       $this->alto_origen = $alto;
       if (count($this->componentes) > 0)
       {
          foreach($this->componentes as $orden => $componente)
          {
             $this->componentes[$orden]->ajustar_posicion($this->alto_origen);
          }       
       }
    }


    /*
     	@@acceso: PUBLICO
    	@@desc: Distribuye los datos dentro del bloque.
    	@@param: array | Arreglo asociativo con los datos del bloque | null
    */
    function cargar_datos(&$datos)
    {
       $this->datos =& $datos;
    }
       
    /*
     	@@acceso: PUBLICO
    	@@desc: Genera las sentencias necesarias para crear 
                el documento pdf para el bloque.
    	@@pendiente: todo
    */ 
    function generar_comandos_pdf()
    {
       if (! is_null($this->datos))
       {
          ksort($this->datos);
          reset($this->datos);
          ksort($this->componentes);
          reset($this->componentes);
          
          //buscamos el maximo de filas desde el arreglo de datos
          //y generamos un arreglo por etiqueta con los textos por fila.
          $nombres = array();
          $etiqueta_max = null;
          foreach($this->componentes as $orden => $componente)
          {
             if (is_a($this->componentes[$orden], 'impr_etiqueta'))
             {
                $nombres[$this->componentes[$orden]->nombre] = array();
                foreach ($this->datos as $nombre => $texto)
                {
                   if ($postfijo = strstr($nombre, $this->componentes[$orden]->nombre))
                   {
                      array_push($nombres[$this->componentes[$orden]->nombre], $texto);
                   }
                }
                if (count($nombres[$this->componentes[$orden]->nombre]) >
                    (is_null($etiqueta_max)? 0: count($nombres[$etiqueta_max])))
                {  
                   $etiqueta_max = $this->componentes[$orden]->nombre;
                   //Si es necesario se asume un alto para cada fila a imprimir
                   if ($this->fila_alto == 0.0)
                   {
                      $this->fila_alto = $this->componentes[$orden]->alto;
                   }
                }
             }
          }
          $maximo = (is_null($etiqueta_max)? 0: count($nombres[$etiqueta_max]));
          
          $comandos = '';
          if ($maximo > 0)
          {
             foreach($this->componentes as $orden => $componente)
             {
                if (is_a($this->componentes[$orden], 'impr_etiqueta') &&
                    isset($nombres[$this->componentes[$orden]->nombre]))
                {
                   $y_origen_original = $this->componentes[$orden]->y_origen;
                   foreach ($nombres[$this->componentes[$orden]->nombre] as $texto)
                   {
                      $this->componentes[$orden]->texto = $texto;
                      $comandos .= $this->componentes[$orden]->generar_comandos_pdf();
                      $this->componentes[$orden]->y_origen += $this->fila_alto;
                   }
                   $this->componentes[$orden]->y_origen = $y_origen_original;
                }
                elseif (is_a($this->componentes[$orden], 'impr_grafico'))
                {
                   $y_origen_original = $this->componentes[$orden]->y_origen;
                   for ($i = 0 ; $i < $maximo; $i++)
                   {
                      $comandos .= $this->componentes[$orden]->generar_comandos_pdf();
                      $this->componentes[$orden]->y_origen += $this->fila_alto;
                   }
                   $this->componentes[$orden]->y_origen = $y_origen_original;
                } 
             }
          }
          return $comandos;    
       }
       else
       {
          return '';
       }   
    }

    /*
     	@@acceso: PUBLICO
    	@@desc: Genera una tabla html con los componentes actuales del bloque.
    	@@pendiente:  todo
    */ 
    function recuperar_estructura()
    {
       $tabla = '<table border=1 width=100% class=tabla_bloque><tr valign=top>';
       $tabla .= '<td nowrap width=10%><strong>Bloque: ' . $this->nombre . '</strong><br>';
       $tabla .=     'Clase: ' . get_class($this) . '<br>';
       $tabla .=     'X origen: ' . $this->x_origen . '<br>';
       $tabla .=     'Y origen: ' . $this->y_origen . '<br>';
       $tabla .=     'Ancho: ' . $this->ancho . '<br>';
       $tabla .=     'Alto: ' . $this->alto . '<br>';
       $tabla .=     'Fila alto: ' . $this->fila_alto;       
       $tabla .= '</td>';       
       $tabla .= '<td>';
       if (count($this->componentes) > 0)
       {       
          foreach($this->componentes as $orden => $componente)
          {
             $tabla .= $this->componentes[$orden]->recuperar_estructura();
          }
       }   
       $tabla .= '</td>';
       $tabla .= '</tr></table>';
       return $tabla;
    }
  
}

?>