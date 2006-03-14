<?php

class impr_encabezado
/*
	@@acceso: PUBLICO
	@@desc: Clase que implemta el encabezado de una hoja dentro de un documento PDF.
*/
{
    /*Atributos de instancia*/
    var $nombre;     //Nombre único asignado al encabezado según la hoja padre.
    var $padre;      //Puntero al objeto hoja contenedor.
    var $conexion;   //Conexion a la base de datos para obtener la configuracon del encabezado.
    var $componentes;//Arreglo con los componentes del encabezado.
    var $hoja_alto;  //Alto de la hoja en milimetros.
    var $alto_origen;//Altura origen del encabezado con respecto a la hoja.
    /*Variables auxiliares*/
    var $etiqueta_aux;
    var $grafico_aux;

    /*
     	@@acceso: PUBLICO
    	@@desc: Método constructor.
    	@@param: string | Nombre del encabezado en particular. | null
      @@param: puntero a objeto | Puntero a la hoja contenedora. | null
    	@@pendiente: casi todo
    */
    function impr_encabezado($nombre = null, &$padre)
    {
       $this->nombre = $nombre;
       $this->padre = &$padre;
       $this->componentes = array();
       $this->conexion =& $padre->conexion;
       $this->hoja_alto = $padre->hoja_alto;
       $this->_recuperar_configuracion();
    }

    /*
     	@@acceso: PRIVADO
    	@@desc: Recupera los componentes del encabezado en particular.
    	@@pendiente: todo
    */ 
    function _recuperar_componentes()
    {
       //Recuperamos las etiquetas sueltas
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
             $this->componentes[count($this->componentes)] =& $this->etiqueta_aux;
          }
          $rs->MoveNext();
       }
       
       //Recuperamos los graficos
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

    /*
     	@@acceso: PRIVADO
    	@@desc: Recupera la configuración del nombre del encabezado en particular.
    	@@pendiente: todo
    */ 
    function _recuperar_configuracion()
    {
       $this->_recuperar_componentes();
    }
    
    /*
     	@@acceso: PUBLICO
    	@@desc: Define la altura del encabezado en la hoja para los componentes.
    */
    function ajustar_posicion()
    {
       foreach($this->componentes as $orden => $componente)
       {
          $this->componentes[$orden]->ajustar_posicion($this->alto_origen);
       }
    }

    /*
     	@@acceso: PUBLICO
    	@@desc: Distribuye los datos dentro del encabezado.
    	@@param: array | Arreglo asociativo con los datos del encabezado | null
    */
    function cargar_datos(&$datos)
    {
       foreach ($this->componentes as $indice => $componente)
       {
          if (is_a($this->componentes[$indice], 'impr_etiqueta') ||
              is_a($this->componentes[$indice], 'impr_grafico'))
          {
             foreach ($datos as $nombre => $arreglo)
             {
                if ($postfijo = strstr($nombre, $this->componentes[$indice]->nombre))
                {
                   if (is_a($this->componentes[$indice], 'impr_etiqueta'))
                   {
                      $this->componentes[$indice]->texto = $datos[$nombre]; 
                   }
                   elseif (is_a($this->componentes[$indice], 'impr_grafico'))
                   {
                      $this->componentes[$indice]->cargar_datos($datos[$nombre]);
                   }                   
                }
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
       ksort($this->componentes);
       reset($this->componentes);
       $comandos = '';
       foreach($this->componentes as $orden => $componente)
       {
          $comandos .= $this->componentes[$orden]->generar_comandos_pdf();
       }
       return $comandos;
    }
 
    /*
     	@@acceso: PUBLICO
    	@@desc: Genera una tabla html con los componentes actuales del encabezado.
    	@@pendiente:  todo
    */ 
    function recuperar_estructura()
    {
       $tabla = '<table border=1 width=100% class=tabla_encabezado><tr valign=top>';
       $tabla .= '<td nowrap width=10%><strong>Encabezado: ' . $this->nombre . '</strong>' . '<br>';
       $tabla .=     'Clase: ' . get_class($this);
       $tabla .= '</td>';       
       $tabla .= '<td>';
       foreach($this->componentes as $orden => $componente)
       {
          $tabla .= $this->componentes[$orden]->recuperar_estructura();
       }   
       $tabla .= '</td>';
       $tabla .= '</tr></table>';
       return $tabla;
    }
 
}

?>