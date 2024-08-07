<?php

/**
 * Interface que deben respetar las clases de definicion de los componentes
 *
 * @package Componentes
 */
interface toba_componente_definicion
{
    //Indica que tablas conforman a un componente
    public static function get_estructura();
    //Devuelve la VISTA del componente utilizada en el ambiente
    public static function get_vista_extendida($proyecto, $componente=null);

    //Se almacena el objeto db, para el quoteo
    public static function set_db($db);
}
