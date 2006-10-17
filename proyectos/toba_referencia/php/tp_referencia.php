<?php
require_once("nucleo/tipo_pagina/toba_tp_normal.php");
require_once("modelo/componentes/info_item.php");

class tp_referencia extends toba_tp_normal
{
    protected $titulo;
    
    function titulo_item()
    {
        if (! isset($this->titulo)) {
            $info['basica'] = toba::solicitud()->get_datos_item();
            $item = new info_item($info);
            $item->cargar_rama();
            
            //Se recorre la rama
            $camino = $item->get_nombre();
            while ($item->get_padre() != null) {
                $item = $item->get_padre();
                if (! $item->es_raiz()) {
                    $camino = '<span style="font-weight:normal;">'.$item->get_nombre() . " > </span>".  $camino;
                }
            }
            $this->titulo = $camino;
        }
        return $this->titulo;
    }
}
?>