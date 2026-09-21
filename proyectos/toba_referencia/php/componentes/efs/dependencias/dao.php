<?php

php_referencia::instancia()->agregar(__FILE__);

/*
*	En este dao las búsquedas son estáticas, generalmente terminarían en una SQL
*
*/
class dao
{
    public static function get_paises()
    {
        return array(
                array('id' => 'ar', 'valor' => 'Argentina'),
                array('id' => 'br', 'valor' => 'Brasil')
            );
    }

    public static function get_provincias($pais)
    {
        if ($pais == 'ar') {
            return array(
                    array('id' => 'ba', 'valor' => 'Buenos Aires'),
                    array('id' => 'sj', 'valor' => 'San Juan')
                );
        }
        return array();
    }

    /*
    *  Para obtener la localidad sólo necesitaría la provincia, pero para mostrar las dependencias de multiples maestros
    *  se usa el país también
    */
    public static function get_localidades($pais, $provincia)
    {
        if ($pais == 'ar') {
            if ($provincia == 'ba') {
                return array( array('id' => 'bb', 'valor' => 'Bahía Blanca'));
            } elseif ($provincia == 'sj') {
                return array( array('id' => 'sj', 'valor' => 'San Juan'));
            }
        }
        return array();
    }


    public static function get_descripcion($localidad)
    {
        if ($localidad == 'bb') {
            return 'Esta es la descripción de Bahía Blanca';
        } elseif ($localidad == 'sj') {
            return 'Esta es la descripción de San Juan';
        }
    }
}
