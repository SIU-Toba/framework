<?php

/**
 * Interface que debe implementar una extensión o redefinición de toba::contexto_ejecucion
 * @package Centrales
 */
interface toba_interface_contexto_ejecucion
{
    /**
     * Ventana que se ejecuta siempre al ingresar el proyecto a la ejecución del request (pedido de página).
     * Por este motivo es util para agregar configuraciones globales al proyecto
     * @ventana
     */
    public function conf__inicial();

    /**
     * Ventana que se ejecuta siempre a la salida del proyecto adela ejecución del request (pedido de página).
     * @ventana
     */
    public function conf__final();

}
