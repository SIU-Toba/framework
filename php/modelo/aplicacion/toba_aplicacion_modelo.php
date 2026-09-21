<?php

/**
 * Interface que debe respetar la extension del modelo
 * @package Centrales
 * @subpackage Modelo
 */
interface toba_aplicacion_modelo
{
    /**
     * Inicialización de la clase en el entorno consumidor
     * @param toba_modelo_instalacion $instalacion Representante de la instalación de toba como un todo
     * @param toba_modelo_instancia $instancia Representante de la instancia actualmente utilizada
     * @param toba_modelo_proyecto $proyecto Representante del proyecto como un proyecto toba (sin la lógica de admin. de la aplicación)
     */
    public function set_entorno($manejador_interface, toba_modelo_instalacion $instalacion, toba_modelo_instancia $instancia, toba_modelo_proyecto $proyecto);

    /**
     * @return toba_modelo_instalacion
     */
    public function get_instalacion();

    /**
     * @return toba_modelo_instancia
     */
    public function get_instancia();

    /**
     * @return toba_modelo_proyecto
     */
    public function get_proyecto();

    /**
     * Retorna la versión actualmente instalada de la aplicación (puede no estar migrada)
     * @return toba_version
     */
    public function get_version_actual();

    /**
     * Retorna la versión a la cual se debe migrar la aplicación (si ya esta migrada debería ser igual a la 'version_actual')
     * @return toba_version
     */
    public function get_version_nueva();

    /**
     * Ejecuta los scripts de migración entre dos versiones específicas del sistema
     * @param toba_version $desde
     * @param toba_version $hasta
     */
    public function migrar(toba_version $desde, toba_version $hasta);

    /**
     * Debe crar todo recurso extra necesario para la ejecución de la aplicación (base de negocios por ejemplo)
     * @param array $datos_servidor Asociativo con los parámetros de conexión a la base
     */
    public function instalar($datos_servidor);

    /**
     * Eliminar todos los recursos creados por 'instalar'
     */
    public function desinstalar();

}
