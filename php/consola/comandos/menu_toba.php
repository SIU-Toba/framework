<?php

require_once('consola/menu.php');
/**
 * Clase que implementa un menu basico en consola
 *
 * @package consola
 */

class menu_toba extends menu
{
    public function get_titulo()
    {
        return "SIU-TOBA ( Ambiente de desarrollo WEB )";
    }

    public function mostrar_observaciones()
    {
        $this->consola->mensaje("Versión: " . file_get_contents(toba_dir().'/VERSION'));
        $this->consola->mensaje("Directorio de la INSTALACION: " . toba_dir());
    }

    public function get_comandos()
    {
        return array(
            'instalacion',
            'base',
            'instancia',
            'proyecto',
            'personalizacion',
            'test',
            'servicios_web'
        );
    }
}
