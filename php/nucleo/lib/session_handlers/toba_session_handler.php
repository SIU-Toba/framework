<?php
class toba_session_handler
{
    protected $default_settings = array();
    protected $settings = array();

    function __construct()
    {}

    /**
     * Lee valores de los seteos desde env
     */
    function read_env_settings()
    {
        $env_vars = array_keys($this->default_settings);
        foreach($env_vars as $clave) {
            $search_key = strtoupper('toba_'.$clave);
            $value = \getenv($search_key);
            if (false !== $value) {            //Esperemos que no haya booleans
                $this->settings[$clave] = $value;
            }
        }
    }

    /**
     * Fija los valores indicados por el handler en php.ini
     */
    function configure_settings()
    {
        $confs = array_merge($this->default_settings, $this->settings);
        foreach($confs as $key => $value) {
            \ini_set($key, $value);
        }
    }

    /**
     * Retorna un arreglo con los parametros configurados
     * @return array
     */
    function get_options()
    {
        $confs = array_merge($this->default_settings, $this->settings);
        return $confs;
    }
}
?>