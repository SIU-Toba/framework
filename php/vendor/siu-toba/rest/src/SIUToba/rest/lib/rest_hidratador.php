<?php

namespace SIUToba\rest\lib;

class rest_hidratador
{
    /**
     * Formatea un recordset de acuerdo a una especificacion según la clase /lib/modelable.
     */
    public static function hidratar($spec, $fuente)
    {
        $return = array();
        foreach ($fuente as $fila) {
            $return[] = self::aplicar_spec_fila($spec, $fila);
        }

        return self::aplicar_group_by($spec, $return);
    }

    /**
     * Formatea una fila de acuerdo a una especificacion según la clase /lib/modelable.
     */
    public static function hidratar_fila($spec, $fuente)
    {
        $h = self::hidratar($spec, array($fuente));

        return $h[0];
    }

    /*
     * Revierte un objeto hidratado - Deberìa ser el formato que maneja el usuario, por lo tanto
     * puede usarse para mapear el input del usuario al formato del modelo.
     * @param $data array la fila para deshidratar
     * @param $spec_hidratar
     * @param array $nueva_fila Se utiliza para poder llamar la funcion recursivamente.
     *                          Posiblemente no se necesite utilizarla en la llamada inicial
     * @return array la fila deshidratado
     */
    public static function deshidratar_fila($data, $spec_hidratar, &$nueva_fila = array())
    {
        foreach ($spec_hidratar as $key => $campo) {
            if (!is_array($campo)) { //si no proveen todos los campos no los incluyo.
                if (isset($data[(string) $campo])) {
                    $nueva_fila[(string) $campo] = $data[(string) $campo];
                } // 2 => 'campo'
                continue;
            }
            if (!array_key_exists($key, $data)) {
                continue;
            }

            if (isset($campo['_mapeo'])) { // "nombre" => array('_mapeo' => "otro nombre",
                $nueva_fila[$campo['_mapeo']] = $data[$key];
                continue;
            }
            if (isset($campo['_compuesto'])) {
                //pongo en la misma fila, las columnas del compuesto
                self::deshidratar_fila($data, $spec_hidratar['_compuesto'], $nueva_fila);
                continue;
            }
            //pasa derecho
            $nueva_fila[$key] = $data[$key];
        }

        return $nueva_fila;
    }

    protected static function aplicar_spec_fila($spec, $fila)
    {
        $nueva_fila = array();
        foreach ($spec as $key => $campo) {
            if (!is_array($campo) && !is_numeric($key)) {
                throw new rest_error_interno("Hidratador: no se acepta el formato para la columna $key. Debe ser un arreglo o solo el nombre de la columna");
            }

            if (is_array($campo) && isset($campo['_mapeo'])) { // "nombre" => array('_mapeo' => "otro nombre",
                $nueva_fila[$key] = $fila[$campo['_mapeo']];
                continue;
            }
            if (is_array($campo) && isset($campo['_compuesto'])) {
                $nuevo_objeto = self::aplicar_spec_fila($campo['_compuesto'], $fila);
                $nueva_fila[$key] = $nuevo_objeto;
                continue;
            }
            //pasa como viene
            if (is_array($campo)) {
                $nueva_fila[$key] = $fila[$key]; // 'key' => array()..
            } else {
                $nueva_fila[$campo] = $fila[$campo]; // 2 => 'campo'
            }
        }

        return $nueva_fila;
    }

    protected static function aplicar_group_by($spec, $rs)
    {
        $grupos = array();
        $id_fila = null;
        if (isset($spec)) { //veo si tiene grupos
            foreach ($spec as $columna_agrupar => $fila_spec) {
                if (is_array($fila_spec) && isset($fila_spec['_agrupado_por'])) {
                    $grupos[$columna_agrupar] = $fila_spec['_agrupado_por'];
                }
            }
        }
        if (empty($grupos)) {
            return $rs;
        }

        $resultado = array();
        foreach ($rs as $fila) {
            foreach ($grupos as $columna_agrupar => $agrupar_por) {
                $id_fila = $fila[$agrupar_por];
                $valor_en_grupo = $fila[$columna_agrupar];

                if (isset($resultado[$id_fila])) { //ya existe, solo mergeo los grupos
                    $resultado[$id_fila][$columna_agrupar][] = $valor_en_grupo;
                } else {
                    $fila[$columna_agrupar] = array($fila[$columna_agrupar]); //pongo el grupo en un arreglo
                    $resultado[$id_fila] = $fila;
                }
            }
        }

        return array_values($resultado);
    }
}
