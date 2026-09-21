<?php

/**
 * Representa el contexto de ejecucion de un proyecto.
 * Este es el lugar indicado para poner los includes de los proyectos
 *
 * Consumir usando toba::contexto_ejecucion()->
 *
 * @package Centrales
 */
class toba_contexto_ejecucion implements toba_interface_contexto_ejecucion
{
    /**
     * Ventana que se ejecuta siempre al ingresar el proyecto a la ejecución del request (pedido de página).
     * Por este motivo es util para agregar configuraciones globales al proyecto
     * @ventana
     */
    public function conf__inicial()
    {
    }

    /**
     * Ventana que se ejecuta siempre a la salida del proyecto adela ejecución del request (pedido de página).
     * @ventana
     */
    public function conf__final()
    {
    }

    /**
     * Ventana que se ejecuta antes de ejecutar un servicio web REST
     * @param \rest\rest $app Clase de rest a configurar
     * @ventana
     */
    public function conf__rest($app)
    {
    }
}
