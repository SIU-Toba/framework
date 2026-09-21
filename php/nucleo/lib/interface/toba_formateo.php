<?php


/**
 * Funciones varias relacionadas con transformación de distintos formatos en diferentes medios
 * @package SalidaGrafica
 */
class toba_formateo
{
    protected $tipo_salida;

    public function __construct($tipo_salida)
    {
        $this->tipo_salida = $tipo_salida;
    }

    protected function get_separador()
    {
        if ($this->tipo_salida == 'html') {
            return '&nbsp;';
        } else {
            return ' ';
        }
    }

    public function formato_escapar($valor)
    {
        $salida = stripslashes($valor);
        if ($this->tipo_salida != 'excel') {
            return $salida;
        } else {
            return array($salida, null);
        }
    }

    public function formato_NULO($valor)
    {
        if ($this->tipo_salida != 'excel') {
            return $valor;
        } else {
            return array($valor, null);
        }
    }

    public function formato_decimal($valor)
    {
        if ($this->tipo_salida != 'excel') {
            if (strpos($valor, '.') === false) {
                return number_format($valor, 0, ',', '.');
            } else {
                return number_format($valor, 2, ',', '.');
            }
        } else {
            return array($valor, array('numberFormat' =>
                        array('formatCode' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00)
                    ));
        }
    }

    public function formato_decimal_estricto($valor)
    {
        if ($this->tipo_salida != 'excel') {
            return number_format($valor, 2, ',', '.');
        } else {
            $estilo = array($valor, array('numberFormat' =>
                        array('formatCode' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00)
                    ));


            return $estilo;
        }
    }

    public function formato_porcentaje($valor)
    {
        //Es trucho forzar desde aca, los datos tienen que esta bien
        //if($valor<0)$valor=0;
        if ($this->tipo_salida != 'excel') {
            return number_format($valor, 2, ',', '.') . $this->get_separador()."%";
        } else {
            return array($valor / 100, array('numberFormat' =>
                        array('formatCode' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00)
                    ));
        }
    }

    public function formato_moneda($valor)
    {
        //Es trucho forzar desde aca, los datos tienen que esta bien
        //if($valor<0)$valor=0;
        if ($this->tipo_salida != 'excel') {
            return '$'.$this->get_separador(). number_format($valor, 2, ',', '.');
        } else {
            return array($valor, array('numberFormat' =>
                         array('formatCode' => toba_vista_excel::FORMAT_CURRENCY_CUSTOM)
                ));
        }
    }

    public function formato_tiempo($valor)
    {
        if ($this->tipo_salida != 'excel') {
            return "<b>" . number_format($valor, 2, ',', '.') . '</b>'.
                $this->get_separador().'seg.';

        } else {
            return array($valor, null);
        }
    }

    public function formato_tiempo_ms($valor)
    {
        if ($this->tipo_salida != 'excel') {
            return "<b>" . number_format($valor * 1000, 2, ',', '.') . '</b>'.
                $this->get_separador().'ms';

        } else {
            return array($valor, null);
        }
    }

    public function formato_millares($valor)
    {
        if ($this->tipo_salida != 'excel') {
            if (is_numeric($valor)) {
                return number_format($valor, 0, ',', '.');
            } else {
                return $valor;
            }
        } else {
            return array($valor, array('numberFormat' =>
                            array('formatCode' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER)
                    ));
        }

    }

    public function formato_numero($valor)
    {
        return $this->formato_decimal($valor);
    }

    public function formato_forzar_cadena($valor)
    {
        if ($this->tipo_salida != 'excel') {
            return $valor;
        } else {
            return array(" ".$valor, array());
        }
    }

    public function formato_mayusculas($valor)
    {
        $salida = strtoupper($valor);
        if ($this->tipo_salida != 'excel') {
            return $salida;
        } else {
            return array($salida, null);
        }
    }

    public function formato_indivisible($valor)
    {
        if ($this->tipo_salida == 'html') {
            return "<span style='white-space:nowrap'>$valor</span>";
        } elseif ($this->tipo_salida == 'pdf') {
            return $valor;
        } else {
            return array($valor, null);
        }
    }

    public function formato_may_ind($valor)
    {
        $salida = str_replace(" ", $this->get_separador(), strtoupper(trim($valor)));
        if ($this->tipo_salida != 'excel') {
            return $salida;
        } else {
            return array($salida, null);
        }

    }

    public function formato_salto_linea_html($valor)
    {
        if ($this->tipo_salida == 'html') {
            return  str_replace("\n", "<br />", $valor);
        } elseif ($this->tipo_salida == 'pdf') {
            return $valor;
        } else {
            return array($valor, null);
        }
    }

    public function formato_fecha($fecha)
    {
        if (isset($fecha) && ($fecha != '')) {
            $desc = cambiar_fecha($fecha, '-', '/');
        } else {
            $desc = '';
        };
        if ($this->tipo_salida != 'excel') {
            return $desc;
        } else {
            return array($desc, array('numberFormat' =>
                    array('formatCode' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY)
                ));
        }
    }

    public function formato_fecha_hora($fecha)
    {
        if (isset($fecha) && ($fecha != '')) {
            $desc = cambiar_fecha($fecha, '-', '/', true);
        } else {
            $desc = '';
        };
        if ($this->tipo_salida != 'excel') {
            return $desc;
        } else {
            return array($desc, array('numberFormat' =>
                array('formatCode' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DATETIME)
            ));
        }
    }

    public function formato_hora($hora)
    {
        if (isset($hora) && ($hora != '')) {
            $desc = $hora;
        } else {
            $desc = '';
        };
        if ($this->tipo_salida != 'excel') {
            return $desc;
        } else {
            $desc = cambiar_hora_formato_12($desc);
            return array($desc, array('numberFormat' =>
                    array('formatCode' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME1 )));
        }
    }

    public function formato_checkbox($valor)
    {
        if ($valor === true || $valor === '1' || $valor === 1 || $valor === 'S' || $valor === 's') {
            $html = "SI";
        } else {
            $html = "NO";
        }
        if ($this->tipo_salida != 'excel') {
            return $html;
        } else {
            return array($html, null);
        }
    }

    public function formato_html_br($valor)
    {
        $html = str_replace("\n", "<br>", $valor);
        if ($this->tipo_salida != 'excel') {
            return $html;
        } else {
            return array($valor, null);
        }
    }

    public function formato_imagen_toba($valor)
    {
        return toba_recurso::imagen_toba($valor, true);
    }

    public function formato_imagen_proyecto($valor)
    {
        return toba_recurso::imagen_proyecto($valor, true);
    }

    public function formato_superficie($valor)
    {
        //Es trucho forzar desde aca, los datos tienen que esta bien
        //if($valor<0)$valor=0;
        $salida = number_format(doubleval($valor), 2, ',', '.') . $this->get_separador() . "Km²";
        if ($this->tipo_salida != 'excel') {
            return $salida;
        } else {
            return array($valor, array('numberFormat' => array('formatCode' => '0.00 k\m')));
        }
    }

    public function formato_cuit($valor)
    {
        if (isset($valor) && $valor != '') {
            $length = strlen($valor);
            $salida = substr($valor, 0, 2) . '-' . substr($valor, 2, $length - 3) . '-' . substr($valor, $length - 1, $length);
        } else {
            $salida = '';
        }
        if ($this->tipo_salida != 'excel') {
            return $salida;
        } else {
            return array($salida, null);
        }
    }

    public function formato_pre($valor)
    {
        if ($this->tipo_salida != 'excel') {
            return '<pre>'.$valor.'</pre>';
        } else {
            return array($valor, null);
        }
    }
}
