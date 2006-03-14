<?php

class impr_grafico_rectangulo extends impr_grafico
/*
	@@acceso: PUBLICO
	@@desc: Clase que implemta un rectangulo en una hoja dentro de un documento PDF.
*/
{
    /*Atributos de instancia*/
    var $espesor;     //Espesor de la linea


    /*
     	@@acceso: PRIVADO
    	@@desc: Recupera la configuración del nombre del gráfico en particular.
    */ 
    function _recuperar_configuracion()
    {
       parent::_recuperar_configuracion();
       $this->espesor = 2;
    }
    
    /*
     	@@acceso: PUBLICO
    	@@desc: Parametros para los graficos.
    	@@param: array | Arreglo asociativo con los datos del grafico | null
    */
    function cargar_datos(&$datos)
    {
		 $this->espesor = $datos;
    }
        
    /*
     	@@acceso: PUBLICO
    	@@desc: Genera las sentencias necesarias para crear el documento pdf para el gráfico.
    */ 
    function generar_comandos_pdf()
    {
       $comandos = '';
       $comandos .= "\$this->doc_pdf->setLineStyle(" . $this->espesor . ");";
       $comandos .= "\$this->doc_pdf->rectangle(x($this->x_origen)" . 
                     ", y($this->hoja_alto, " . ($this->alto_origen + $this->y_origen + $this->alto) . ")" .
                     ", x($this->ancho)" . 
                     ", x($this->alto)" . 
                     ");";
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
       $tabla .= '<td nowrap width=10%><strong>Gráfico-Rectangulo: ' . $this->nombre . '</strong><br>';
       $tabla .=     'Clase: ' . get_class($this) . '<br>';       
       $tabla .=     'X origen: ' . $this->x_origen . '<br>';
       $tabla .=     'Y origen: ' . $this->y_origen . '<br>';
       $tabla .=     'Ancho: ' . $this->ancho . '<br>';
       $tabla .=     'Alto: ' . $this->alto . '<br>';
       $tabla .= '</td>';       
       $tabla .= '</tr></table>';
       return $tabla;
    }
      
}

?>