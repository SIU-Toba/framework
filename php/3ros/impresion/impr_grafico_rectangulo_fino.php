<?php

class impr_grafico_rectangulo_fino extends impr_grafico_rectangulo
/*
	@@acceso: PUBLICO
	@@desc: Clase que implemta un rectangulo fino en una hoja dentro de un documento PDF.
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
       $this->espesor = 0.5;
    }      
}

?>