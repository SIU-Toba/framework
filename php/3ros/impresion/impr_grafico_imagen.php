<?php

class impr_grafico_imagen extends impr_grafico
/*
	@@acceso: PUBLICO
	@@desc: Clase que implemta una imagen en una hoja dentro de un documento PDF.
*/
{
    /*Atributos de instancia*/
    var $archivo;     //Nombre único asignado al cuerpo según la hoja padre.


    /*
     	@@acceso: PRIVADO
    	@@desc: Recupera la configuración del nombre del gráfico en particular.
    */ 
    function _recuperar_configuracion()
    {
       $this->conexion->SetFetchMode(ADODB_FETCH_ASSOC);
       $rs = $this->conexion->Execute("SELECT * " . 
                                      "FROM impr_grafico " . 
                                      "WHERE grafico_nombre = '" . $this->nombre . "'");
       if (! $rs->EOF)
       {
          $this->x_origen = $rs->fields['x_origen'];
          $this->y_origen = $rs->fields['y_origen'];
          $this->ancho = $rs->fields['ancho'];
          $this->alto = $rs->fields['alto'];
          $this->archivo = $rs->fields['archivo_origen'];
       }
    }

    /*
     	@@acceso: PUBLICO
    	@@desc: Parametros para los graficos.
    	@@param: array | Arreglo asociativo con los datos del grafico | null
    */
    function cargar_datos(&$datos)
    {
       $this->archivo = $datos;
    }
        
    /*
     	@@acceso: PUBLICO
    	@@desc: Genera las sentencias necesarias para crear el documento pdf para el gráfico.
    	@@pendiente: todo
    */ 
    function generar_comandos_pdf()
    {
       $comandos = '';
       if (($this->archivo != '') && ! is_null($this->archivo))
       {
          $parametros = "('" . $this->archivo .
                        "', x($this->x_origen)" . 
                        ", y($this->hoja_alto, " . ($this->alto_origen + $this->y_origen) . ")" .
                        ", x($this->ancho)" . 
                        (! is_null($this->alto)? ", x($this->alto)": "") . 
                        ");";
          
          if (stristr($this->archivo, '.jpg') || 
              stristr($this->archivo, '.jpe') ||
              stristr($this->archivo, '.jpeg'))
          {
             $comandos .= "\$this->doc_pdf->addJpegFromFile" . $parametros;
          }
          elseif (stristr($this->archivo, '.png'))
          {
             $comandos .= "\$this->doc_pdf->addPngFromFile" . $parametros;
          }
       }
       return $comandos;
    }
    
    /*
     	@@acceso: PUBLICO
    	@@desc: Genera una tabla html con los componentes actuales del grafico.
    	@@pendiente:  todo
    */ 
    function recuperar_estructura()
    {
       $tabla = '<table border=1 width=100% class=tabla_grafico><tr valign=top>';
       $tabla .= '<td nowrap width=10%><strong>Gráfico-Imagen: ' . $this->nombre . '</strong><br>';
       $tabla .=     'Clase: ' . get_class($this) . '<br>';       
       $tabla .=     'X origen: ' . $this->x_origen . '<br>';
       $tabla .=     'Y origen: ' . $this->y_origen . '<br>';
       $tabla .=     'Ancho: ' . $this->ancho . '<br>';
       $tabla .=     'Alto: ' . $this->alto . '<br>';
       $tabla .=     'Archivo: ' . $this->archivo;
       $tabla .= '</td>';       
       $tabla .= '</tr></table>';
       return $tabla;
    }
      
}

?>