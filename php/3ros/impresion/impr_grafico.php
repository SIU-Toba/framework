<?php

class impr_grafico
/*
	@@acceso: PUBLICO
	@@desc: Clase que implemta un elemento grafico (imagenes, lineas, cuadros, ...)
           una hoja dentro de un documento PDF.
*/
{
    /*Atributos de instancia*/
    var $nombre;     //Nombre único asignado al cuerpo según la hoja padre.
    var $padre;      //Puntero al objeto hoja contenedor.
    var $conexion;   //Conexion a la base de datos para obtener la configuracon del cuerpo.
    //Atributos que definen el texto a imprimir.    
    var $x_origen;       //Coordenada origen horizontal en mm.
    var $y_origen;       //Coordenada origen vertical en mm.
    var $ancho;          //Tamaño horizontal en mm.
    var $alto;           //Tamaño vertical en mm.
    var $hoja_alto;      //Alto de la hoja en milimetros.
    var $alto_origen;     //Desplazamiento del objeto contenedor desde el comienzo de la pagina.

    /*
     	@@acceso: PUBLICO
    	@@desc: Método constructor.
    	@@param: string | Nombre de la etiqueta en particular. | null
      @@param: puntero a objeto | Puntero al contenedor. | null
    	@@pendiente: casi todo
    */
    function impr_grafico($nombre = null, &$padre)
    {
       $this->nombre = $nombre;
       $this->padre =& $padre;
       $this->x_origen = 0.0;
       $this->y_origen = 0.0;
       $this->ancho = 0.0;
       $this->alto = 0.0;
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
       }
    }
    
    /*
     	@@acceso: PUBLICO
    	@@desc: Define la altura del grafico con respecto al contenedor.
    */    
    function ajustar_posicion($alto = 0.0)
    {
       $this->alto_origen = $alto;
    }

    /*
     	@@acceso: PUBLICO
    	@@desc: Parametros para los graficos.
    	@@param: array | Arreglo asociativo con los datos del grafico | null
    */
    function cargar_datos(&$datos)
    {
    }
        
    /*
     	@@acceso: PUBLICO
    	@@desc: Genera las sentencias necesarias para crear el documento pdf para el gráfico.
    	@@pendiente: todo
    */ 
    function generar_comandos_pdf()
    {
       $comandos = '';    
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
       $tabla .= '<td nowrap width=10%><strong>Gráfico: ' . $this->nombre . '</strong><br>';
       $tabla .=     'Clase: ' . get_class($this) . '<br>';       
       $tabla .=     'X origen: ' . $this->x_origen . '<br>';
       $tabla .=     'Y origen: ' . $this->y_origen . '<br>';
       $tabla .=     'Ancho: ' . $this->ancho . '<br>';
       $tabla .=     'Alto: ' . $this->alto;
       $tabla .= '</td>';       
       $tabla .= '</tr></table>';
       return $tabla;
    }
      
}

?>