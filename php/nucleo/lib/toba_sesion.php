<?php

/**
 * Representa la sesión del usuario en la aplicacion. En su ciclo de vida presenta tres momentos:
 *  - Inicio, donde se producen validaciones (generalmente despues de un login)
 *  - Nudo o refresco, donde se valida que no haya excedido el tiempo de no-interaccion, etc
 *  - Finalizacion. Se borra toda la informacion de la sesion en memoria
 *
 * Cabe aclarar que el sentido de la sesion es orientada al ciclo de vida del usuario en este proyecto y no
 * sobre el  $_SESSION, siendo éste un contenedor de información manejado en bajo nivel por lo que llamamos memoria
 * @see toba_memoria
 *
 * @package Centrales
 */
class toba_sesion implements toba_interface_sesion
{
    /**
     * Atrapa el inicio de la sesión del usuario en la instancia (unica vez en toda la sesión)
     * @ventana
     */
    public function conf__inicial($datos = null)
    {

    }

    /**
     * Atrapa el fin de la sesión del usuario en la instancia (el usuario presiono salir)
     * @ventana
     */
    public function conf__final()
    {

    }

    /**
     * Atrapa la activación de la sesión en cada pedido de página (similar a toba::contexto_ejecucion()->conf__inicial pero se ejecuta sólo con el usuario logueado)
     */
    public function conf__activacion()
    {

    }
}
