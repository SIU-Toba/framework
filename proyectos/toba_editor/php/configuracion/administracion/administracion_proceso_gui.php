<?php

class administracion_proceso_gui implements toba_proceso_gui
{
    protected static $display_ancho = 79;
    public const display_coleccion_espacio_nombre = 25;
    public const display_prefijo_linea = ' ';

    protected $_log = '';

    public function get_info_log()
    {
        return $this->_log;
    }

    protected function imprimir($texto)
    {
        if (toba_manejador_archivos::es_windows() && function_exists('iconv')) {
            $this->_log .= iconv('latin1', 'IBM850', $texto);
        } else {
            $this->_log .= $texto;
        }
    }

    public function separador($texto = '', $caracter = '-')
    {
        if ($texto != '') {
            $texto = "--  $texto  ";
        }
        $this->imprimir("\n");
        $this->linea_completa($texto, $caracter);
        $this->imprimir("\n");
    }

    public function titulo($texto)
    {
        $this->imprimir("\n");
        $this->linea_completa(null, '-');
        $this->linea_completa(" $texto  ", ' ');
        $this->linea_completa(null, '-');
        $this->imprimir("\n");

    }

    public function subtitulo($texto)
    {
        $this->imprimir(self::display_prefijo_linea . $texto . "\n");
        $this->imprimir(self::display_prefijo_linea . str_repeat('-', strlen($texto)));
        $this->imprimir("\n\n");

    }

    public function mensaje($texto, $bajar_linea = true)
    {
        $lineas = toba_texto::separar_texto_lineas($texto, self::$display_ancho);
        for ($i = 0; $i < count($lineas); $i++) {
            if ($bajar_linea || $i < count($lineas) - 1) {
                $extra = "\n";
            } else {
                $extra = '';
            }
            $this->imprimir(self::display_prefijo_linea . $lineas[$i] . $extra);
        }
    }

    public function progreso_avanzar()
    {
        $this->imprimir('.');
    }

    public function progreso_fin()
    {
        $this->imprimir("OK\n");

    }

    public function enter()
    {
        $this->imprimir("\n");
    }

    public function error($texto)
    {
        toba_logger::instancia()->error($texto);
        $lineas = toba_texto::separar_texto_lineas($texto, self::$display_ancho);
        foreach ($lineas as $linea) {
            $this->_log .= self::display_prefijo_linea . $linea . "\n" ;
        }

    }

    public function linea_completa($base = '', $caracter_relleno = '-')
    {
        if (self::$display_ancho > 100) {
            $ancho = 100;
        } else {
            $ancho = self::$display_ancho;
        }
        $this->imprimir(str_pad(self::display_prefijo_linea . $base, $ancho, $caracter_relleno));
        $this->imprimir("\n");
    }

    public function lista($lista, $titulo)
    {
        if (count($lista) > 0) {
            $i = 0;
            foreach ($lista as $l) {
                $datos[$i][0] = $l;
                $i++;
            }
            $this->imprimir(Console_Table::fromArray(array($titulo), $datos));
        }
    }
}
