<?php

require_once('formateo_referencia.php');
php_referencia::instancia()->agregar(__FILE__);

class extension_ci extends toba_ci
{
    protected $orden;

    public function ini()
    {
        $this->set_propiedades_sesion(array('orden'));
    }

    public function get_datos()
    {
        $datos = array();
        $inicio = 1;
        $fin = 31;
        for ($i = $inicio ; $i <= $fin; $i++) {
            $datos[] = array('fecha' => "2006-03-$i", 'importe' => 1000 - ($i * rand(1, $i)), 'id' => $i % 2);
        }
        if (isset($this->orden)) {
            $ordenamiento = array();
            foreach ($datos as $fila) {
                $ordenamiento[] = $fila[$this->orden['columna']];
            }
            $sentido = ($this->orden['sentido'] == 'asc') ? SORT_ASC : SORT_DESC;
            array_multisort($ordenamiento, $sentido, $datos);
        }
        return $datos;
    }

    public function conf__cuadro_auto($cuadro)
    {
        $cuadro->set_formateo_columna('importe', 'pesos_sin_coma', 'formateo_referencia');
        return $this->get_datos();
    }


    public function conf__cuadro($cuadro)
    {
        $datos = $this->get_datos();
        $cuadro->set_total_registros(count($datos));
        $tamanio_pagina = $cuadro->get_tamanio_pagina();
        $offset = ($cuadro->get_pagina_actual() - 1) * $tamanio_pagina;
        $cuadro->set_datos(array_slice($datos, $offset, $tamanio_pagina));
    }

    public function evt__cuadro__ordenar($orden)
    {
        $this->orden = $orden;
    }
}
