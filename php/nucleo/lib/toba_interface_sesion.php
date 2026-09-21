<?php

/**
 * Interface que debe implementar una extensión o redefinición de toba::sesion()
 * @package Centrales
 */
interface toba_interface_sesion
{
    /**
     * Atrapa el inicio de la sesión del usuario en la instancia (unica vez en toda la sesión)
     * @ventana
     */
    public function conf__inicial($datos_iniciales = null);

    /**
     * Atrapa el fin de la sesión del usuario en la instancia (el usuario presiono salir)
     * @ventana
     */
    public function conf__final();

    /**
     * Atrapa la activación de la sesión en cada pedido de página (similar a toba::contexto_ejecucion()->conf__inicial pero se ejecuta sólo con el usuario logueado)
     */
    public function conf__activacion();

}
