<?php

namespace SIUToba\rest\lib;

class lector_recursos_archivo
{
    protected $prefijo_recursos;
    protected $directorios_recursos;

    public function __construct($directorio, $prefijo_recursos)
    {
        $this->directorios_recursos = (! is_array($directorio)) ? array($directorio) : $directorio;
        $this->prefijo_recursos = $prefijo_recursos;
    }

    public function get_directorio_recursos()
    {
        return current($this->directorios_recursos);
    }

    /**
    * Permite agregar una fuente de recursos
    * @param $path string  
    */
    public function add_directorio_recursos($path)
    {
        if (! is_array($path) && ! in_array($path, $this->directorios_recursos)) {
            $this->directorios_recursos[] = $path;
        }
    }

    /**
     * @param array $path la sucesion de recursos anidados. El recurso es el ultimo que exista
     *
     * @return array recurso => clase-que-lo-implementa
     */
    public function get_recurso($path, $montaje = '')
    {
        $prefijo_montaje = (!empty($montaje))? $montaje.DIRECTORY_SEPARATOR: '';

        //Busco del mas especifico al mas general
        while (!empty($path)) {
            $recurso = array_pop($path);
            if (!empty($path)) {
                $ruta_padres = $prefijo_montaje.implode(DIRECTORY_SEPARATOR, $path);
            } else {
                $ruta_padres = $montaje;
            }

            if ($file = $this->existe_recurso($ruta_padres, $recurso)) {
                return array('recurso' => $recurso,
                    'archivo' => $file, );
            }
        }

        return false;
    }

    /**
     * Un montaje es un path que agrupa distintos recursos. Un base path por perfil generalmente (/admin, /me).
     * Sobre el montaje solo existen otros recursos. Por ej: /rest/admin/recibos. No existe /rest/admin/07.
     */
    public function es_montaje($padre)
    {
        foreach ($this->directorios_recursos as $directorio) {
            //una carpeta sin recurso_xxx.php la asumo como montaje
            return is_dir($directorio.DIRECTORY_SEPARATOR.$padre) &&
                !file_exists($directorio.DIRECTORY_SEPARATOR.$padre.DIRECTORY_SEPARATOR.$this->prefijo_recursos.$padre.'.php');
        }
    }

    /**
     * Un recurso a/b, puede estar implementado en a/b.php o a/b/b.php.
     *
     * @param $path string a/
     * @param $name string b
     *
     * @return bool
     */
    protected function existe_recurso($path, $name)
    {
        $path = ($path) ? $path.DIRECTORY_SEPARATOR : '';
        $nombre_recurso = $this->prefijo_recursos.$name.'.php';

        foreach ($this->directorios_recursos as $base_dir) {
            $directorio = $base_dir.DIRECTORY_SEPARATOR.$path;
            $como_archivo = $directorio.$nombre_recurso;
            $como_carpeta_archivo = $directorio.$name.DIRECTORY_SEPARATOR.$nombre_recurso;

            if ($file = $this->obtener_archivo($como_archivo)) {
                return $file;
            }
            if ($file = $this->obtener_archivo($como_carpeta_archivo)) {
    //            echo $file . "- $directorio - Path:$path - Name: $name\n";
                return $file;
            }
        }
        return false;
    }

    /**
     * Para personalizar, un archivo fisico puede estar pisado en la personalizacion, devolver ese.
     */
    protected function obtener_archivo($path)
    {
        if (file_exists($path)) {
            return $path;
        }
    }
}
