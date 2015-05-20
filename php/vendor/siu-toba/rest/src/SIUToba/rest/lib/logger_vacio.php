<?php

namespace SIUToba\rest\lib;

/**
 * Un logger que no hace nada, solo ocupa las llamadas para que siempre exista un logger
 * Class logger_vacio.
 */
class logger_vacio implements logger
{
    /**
     * $this->ref_niveles[2] = "CRITICAL";
     * $this->ref_niveles[3] = "ERROR";
     * $this->ref_niveles[4] = "WARNING";
     * $this->ref_niveles[5] = "NOTICE";
     * $this->ref_niveles[6] = "INFO";
     * $this->ref_niveles[7] = "DEBUG";.
     */
    public function set_nivel($nivel)
    {
        // TODO: Implement set_nivel() method.
    }

    /**
     * Guarda los sucesos actuales en el sist. de archivos.
     */
    public function guardar()
    {
        // TODO: Implement guardar() method.
    }

    /**
     * Desactiva el logger durante todo el pedido de página actual.
     */
    public function desactivar()
    {
        // TODO: Implement desactivar() method.
    }

    /**
     * Dumpea el contenido de una variable al logger.
     */
    public function var_dump($variable)
    {
        // TODO: Implement var_dump() method.
    }

    /**
     * Registra un suceso útil para rastrear problemas o bugs en la aplicación.
     */
    public function debug($mensaje)
    {
        // TODO: Implement debug() method.
    }

    /**
     * Registra un suceso netamente informativo, para una inspección posterior.
     */
    public function info($mensaje)
    {
        // TODO: Implement info() method.
    }

    /**
     * Registra un suceso no contemplado que no es critico para la aplicacion.
     */
    public function notice($mensaje)
    {
        // TODO: Implement notice() method.
    }

    /**
     * Registra un suceso no contemplado pero que posiblemente no afecta la correctitud del proceso.
     */
    public function warning($mensaje)
    {
        // TODO: Implement warning() method.
    }

    /**
     * Registra un suceso CRITICO (un error muy grave).
     */
    public function crit($mensaje)
    {
        // TODO: Implement crit() method.
    }

    /**
     * Registra un error en la apl., este nivel es que el se usa en las excepciones.
     */
    public function error($mensaje)
    {
        // TODO: Implement error() method.
    }
}
