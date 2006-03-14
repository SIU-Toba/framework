<?php

class impr_hoja
/*
	@@acceso: PUBLICO
	@@desc: Clase que implementa una hoja dentro de un documento PDF.
*/
{
    /*Atributos de instancia*/
    var $nombre;  //Nombre único asignado al tipo de hoja según el documento padre.
    var $padre;   //Puntero al objeto documento contenedor.
    var $encabezado; //Encabezado de la hoja.
    var $cuerpo;     //Cuerpo de la hoja.
    var $pie;        //Pie de la hoja.
    var $encabezado_tamanio; //Encabezado de la hoja.
    var $cuerpo_tamanio;     //Cuerpo de la hoja.
    var $pie_tamanio;        //Pie de la hoja.
    var $numero_hoja;//Número de hoja dentro del documento padre.
    var $conexion;   //Conexion a la base de datos para obtener la configuracon de la hoja.
    var $hoja_alto;  //Alto de la hoja en milimetros.
    
    /*
     	@@acceso: PUBLICO
    	@@desc: Método constructor.
    	@@param: string | Nombre de la hoja en particular. | null
        @@param: puntero a objeto | Puntero al documento contenedor. | null
    	@@pendiente: casi todo
    */
    function impr_hoja($nombre = null, &$padre)
    {
       $this->nombre = $nombre;
       $this->padre =& $padre;
       $this->encabezado = null;
       $this->cuerpo = null;
       $this->pie = null;
       $this->encabezado_tamanio = 0.0;
       $this->cuerpo_tamanio = 100.0;
       $this->pie_tamanio = 0.0;
       $this->numero_hoja = null;
       $this->conexion =& $padre->conexion;
       $this->hoja_alto = $padre->hoja_alto;
       $this->_recuperar_configuracion();
    }

    /*
     	@@acceso: PRIVADO
    	@@desc: Recupera la configuración el nombre de la hoja en particular.
    	@@pendiente: todo
    */ 
    function _recuperar_configuracion()
    {
       $this->conexion->SetFetchMode(ADODB_FETCH_ASSOC);
       $rs = $this->conexion->Execute("SELECT * FROM impr_hoja WHERE hoja_nombre = '" . $this->nombre . "'");
       if (! $rs->EOF)
       {
          //Instanciacion del encabezado, si existe.
          if (! is_null($rs->fields['encabezado_nombre']))
          {
             $this->conexion->SetFetchMode(ADODB_FETCH_ASSOC);
             $rs1 = $this->conexion->Execute("SELECT encabezado_nombre, clase " . 
                                             "FROM impr_encabezado " .
                                             "WHERE encabezado_nombre = '" . $rs->fields['encabezado_nombre'] . "' ");
             if (! $rs1->EOF)
             {
                $this->encabezado = null;
                eval("\$this->encabezado =& new " . $rs1->fields['clase'] . "('" . $rs->fields['encabezado_nombre'] . "', \$this);");
             }
          }
          
          //Instanciacion del cuerpo, siempre debe existir.
          if (! is_null($rs->fields['cuerpo_nombre']))
          {
             $this->conexion->SetFetchMode(ADODB_FETCH_ASSOC);
             $rs1 = $this->conexion->Execute("SELECT cuerpo_nombre, clase " . 
                                             "FROM impr_cuerpo " .
                                             "WHERE cuerpo_nombre = '" . $rs->fields['cuerpo_nombre'] . "' ");
             if (! $rs1->EOF)
             {
                $this->cuerpo = null;
                eval("\$this->cuerpo =& new " . $rs1->fields['clase'] . "('" . $rs->fields['cuerpo_nombre'] . "', \$this);");
             }
          }
          
          //Instanciacion del pie, si existe.
          if (! is_null($rs->fields['pie_nombre']))
          {
             $this->conexion->SetFetchMode(ADODB_FETCH_ASSOC);
             $rs1 = $this->conexion->Execute("SELECT pie_nombre, clase " . 
                                             "FROM impr_pie " .
                                             "WHERE pie_nombre = '" . $rs->fields['pie_nombre'] . "' ");
             if (! $rs1->EOF)
             {
                $this->pie = null;
                eval("\$this->pie =& new " . $rs1->fields['clase'] . "('" . $rs->fields['pie_nombre'] . "', \$this);");
             }
          }
         
          $this->encabezado_tamanio = (is_null($rs->fields['encabezado_tamanio'])? 25.0: $rs->fields['encabezado_tamanio']);
          $this->cuerpo_tamanio = (is_null($rs->fields['cuerpo_tamanio'])? 50.0: $rs->fields['cuerpo_tamanio']);
          $this->pie_tamanio = (is_null($rs->fields['pie_tamanio'])? 25.0: $rs->fields['pie_tamanio']);
          
          if (($this->encabezado_tamanio + $this->cuerpo_tamanio + $this->pie_tamanio) == 100.0)
          {
              $avance = 0.0;
              //Tamaño del encabezado.
              if (! is_null($this->encabezado))
              {
                 $this->encabezado->alto_origen = $avance;
                 $avance += ($this->encabezado_tamanio / 100.0) * $this->hoja_alto;
                 $this->encabezado->ajustar_posicion();
              }
              if (! is_null($this->cuerpo))
              {
                 $this->cuerpo->alto_origen = $avance;
                 $avance += ($this->cuerpo_tamanio / 100.0) * $this->hoja_alto;
                 $this->cuerpo->ajustar_posicion();
              }
              if (! is_null($this->pie))
              {
                 $this->pie->alto_origen = $avance;
                 $this->pie->ajustar_posicion();
              }
          }
          else
          {
              if (! is_null($this->encabezado))
              {
                 $this->encabezado->alto_origen = 0.0;
                 $this->encabezado->ajustar_posicion();
              }
              if (! is_null($this->cuerpo))
              {
                 $this->cuerpo->alto_origen = 0.25 * $this->hoja_alto;
                 $this->cuerpo->ajustar_posicion();
              }          
              if (! is_null($this->pie))
              {
                 $this->pie->alto_origen = 0.75 * $this->hoja_alto;
                 $this->pie->ajustar_posicion();
              }          
          }
          
          
       }
    }
    
    /*
     	@@acceso: PUBLICO
    	@@desc: Setea el número de hoja dentro del documento.
    	@@param: string | Número de hoja | null
    */
    function indicar_numero_hoja($numero = null)
    {
       $this->numero_hoja = $numero;
    }

    /*
     	@@acceso: PUBLICO
    	@@desc: Distribuye los datos al encabezado, cuerpo y pie.
    	@@param: array | Arreglo asociativo con los datos de la hoja | null
    */
    function cargar_datos(&$datos)
    {
       if (! is_null($this->encabezado))
       {
          foreach ($datos as $zona => $arreglo)
          {
             if ($postfijo = strstr($zona, $this->encabezado->nombre))
             {
                $this->encabezado->cargar_datos($datos[$zona]);
             }
          }
       }
       if (! is_null($this->cuerpo))
       {
          foreach ($datos as $zona => $arreglo)
          {
             if ($postfijo = strstr($zona, $this->cuerpo->nombre))
             {
                $this->cuerpo->cargar_datos($datos[$zona]);
             }
          }
       }          
       if (! is_null($this->pie))
       {
          foreach ($datos as $zona => $arreglo)
          {
             if ($postfijo = strstr($zona, $this->pie->nombre))
             {
                $this->pie->cargar_datos($datos[$zona]);
             }
          }
       }        
    }
    
    /*
     	@@acceso: PUBLICO
    	@@desc: Obtiene los comandos de los componentes que incluye.
    */
    function generar_comandos_pdf()
    {
       $comandos = '';
       if (! is_null($this->encabezado))
       {
          $comandos .= $this->encabezado->generar_comandos_pdf();
       }
       if (! is_null($this->cuerpo))
       {
          $comandos .= $this->cuerpo->generar_comandos_pdf();
       }          
       if (! is_null($this->pie))
       {
          $comandos .= $this->pie->generar_comandos_pdf();
       }        
       return $comandos;       
    }

    /*
     	@@acceso: PUBLICO
    	@@desc: Genera una tabla html con los componentes actuales de la hoja.
    	@@pendiente:  todo
    */ 
    function recuperar_estructura()
    {
       $tabla = '<table border=1 width=100% class=tabla_hoja><tr valign=top>';
       $tabla .= '<td nowrap width=10%><strong>Hoja: ' . $this->nombre . '</strong><br>';
       $tabla .=     'Clase: ' . get_class($this) . '<br>';       
       $tabla .=     'Encabezado %: ' . $this->encabezado_tamanio . '<br>';
       $tabla .=     'Cuerpo %: ' . $this->cuerpo_tamanio . '<br>';
       $tabla .=     'Pie %: ' . $this->pie_tamanio . '<br>';
       $tabla .=     'Nro. hoja: ' . $this->numero_hoja . '<br>';
       $tabla .= '</td>';       
       $tabla .= '<td><table border=1 width=100% class=tabla_hoja>';
       if (! is_null($this->encabezado))
       {
          $tabla .= '<tr><td>';
          $tabla .= $this->encabezado->recuperar_estructura();
          $tabla .= '</td></tr>';
       }
       if (! is_null($this->cuerpo))
       {
          $tabla .= '<tr><td>';       
          $tabla .= $this->cuerpo->recuperar_estructura();
          $tabla .= '</td></tr>';          
       }
       if (! is_null($this->pie))
       {
          $tabla .= '<tr><td>';       
          $tabla .= $this->pie->recuperar_estructura();
          $tabla .= '</td></tr>';          
       }       
       $tabla .= '</table></td>';
       $tabla .= '</tr></table>';
       return $tabla;
    }
    
}
?>