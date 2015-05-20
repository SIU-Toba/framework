<?php

namespace SIUToba\rest\lib;

/**
 * Interfaz para logear en el mini proyecto REST.
 */
interface logger
{
    /**
     * $this->ref_niveles[2] = "CRITICAL";
     * $this->ref_niveles[3] = "ERROR";
     * $this->ref_niveles[4] = "WARNING";
     * $this->ref_niveles[5] = "NOTICE";
     * $this->ref_niveles[6] = "INFO";
     * $this->ref_niveles[7] = "DEBUG";.
     */
    public function set_nivel($nivel);

    /**
     * Guarda los sucesos actuales en el sist. de archivos.
     */
    public function guardar();

    /**
     * Desactiva el logger durante todo el pedido de página actual.
     */
    public function desactivar();

    ////-----------------------ALIAS PARA LOGGEAR -----------------------------
    /**
     * Dumpea el contenido de una variable al logger.
     */
    public function var_dump($variable);

    /**
     * Registra un suceso útil para rastrear problemas o bugs en la aplicación.
     */
    public function debug($mensaje);

    /**
     * Registra un suceso netamente informativo, para una inspección posterior.
     */
    public function info($mensaje);

    /**
     * Registra un suceso no contemplado que no es critico para la aplicacion.
     */
    public function notice($mensaje);

    /**
     * Registra un suceso no contemplado pero que posiblemente no afecta la correctitud del proceso.
     */
    public function warning($mensaje);

    /**
     * Registra un suceso CRITICO (un error muy grave).
     */
    public function crit($mensaje);

    /**
     * Registra un error en la apl., este nivel es que el se usa en las excepciones.
     */
    public function error($mensaje);
}
