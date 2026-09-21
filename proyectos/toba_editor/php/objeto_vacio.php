<?php

class objeto_vacio
{
    private $nombre;
    private $llamada = array();
    private $proxima;

    public function __construct($nombre)
    {
        $this->nombre = $nombre;
        $this->proxima = 0;
    }

    public function __call($metodo, $argumentos)
    {
        $escapador = toba::escaper();
        echo $escapador->escapeHtml($this->nombre) . ' -> ' . $escapador->escapeHtml($metodo) . '()<br>';				//hiper WTF???
        //Llamar a un metodo desde aca cuelga al apache
        //$this->agregar_llamada($metodo, $argumentos);
    }

    public function __set($propiedad, $valor)
    {

    }

    public function __get($propiedad)
    {

    }

    public function agregar_llamada($metodo, $argumentos)
    {
        $this->llamada[$this->proxima] = $metodo;
        $this->llamada[$this->proxima] = $argumentos;
        $this->proxima++;
    }

    public function get_llamadas()
    {
        return $this->llamadas;
    }

    public function dump_llamadas()
    {
        ei_arbol($this->llamada, 'Llamadas a ' . $this->nombre);
    }
}
