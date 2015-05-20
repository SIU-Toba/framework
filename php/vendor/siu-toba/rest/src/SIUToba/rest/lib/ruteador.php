<?php

namespace SIUToba\rest\lib;

class ruteador
{
    const SUFIJO_COLECCION = '_list';

    /**
     * @var lector_recursos_archivo
     */
    protected $lector;

    protected $instanciador;

    public function __construct($lector, $instanciador)
    {
        $this->lector = $lector;
        $this->instanciador = $instanciador;
    }

    public function buscar_controlador($method, $url)
    {
        $partes_url = explode('/', $url);

        $instanciador = $this->instanciador;

        //colleccion/id/colleccion...
        $colecciones = array();
        $parametros = array();

        if ($es_montaje = $this->lector->es_montaje($partes_url[0])) {
            $montaje = array_shift($partes_url);
        } else {
            $montaje = "";
        }

        foreach ($partes_url as $key => $valor) {
            if (trim($valor) == "") { //mal formato. / de mas
                throw new \Exception("Formato de ruta incorrecto - $url"); //cambiar
            }
            if ($key % 2) {
                $parametros[] = $valor;
            } else {
                $valor = str_replace('_', ' ', $valor); //no permito '_' en las colecciones
                $valor = str_replace('-', '_', $valor); //paso todos los - de la url a '_'
                $colecciones[] = $valor;
            }
        }

        //busco la clase que maneja el recurso
        if (!$recurso = $this->lector->get_recurso($colecciones, $montaje)) {
            $dir = $this->lector->get_directorio_recursos();
            throw new \Exception("No se encuentra el recurso para $url en el directorio $dir. ¿Ruta mal formada?"); //cambiar
        }

        $instanciador->archivo = $recurso['archivo'];

        // Se checkea si matchea con un alias primero
        if (count($colecciones) == count($parametros)) {
            $posibles_params = $parametros;
            $alias = array_pop($posibles_params); // recurso1/param1/rec2/alias_como_param2

            $posible_accion = $this->get_accion_path($method, $recurso['recurso'], $colecciones, $posibles_params);
            $posible_accion .= '__'.$alias;

            if ($instanciador->existe_metodo($posible_accion)) {
                $instanciador->accion = $posible_accion;
                $instanciador->parametros = $posibles_params;

                return $instanciador;
            }
        }

        //se invoca la accion tipica
        $accion = $this->get_accion_path($method, $recurso['recurso'], $colecciones, $parametros);
        $instanciador->accion = $accion;
        $instanciador->parametros = $parametros;

        if ($instanciador->existe_metodo($accion)) { //chequear que exista la accion?
            return $instanciador;
        }
        if (isset($posible_accion)) {
            $error_alias = "o el alias '$posible_accion' ";
        } else {
            $error_alias = '';
        }

        throw new \Exception("No se encuentra el metodo $accion {$error_alias}en la clase {$recurso['archivo']}. ¿Ruta mal formada?");
    }

    protected function get_accion_path($metodo, $clase, $colecciones, $parametros)
    {
        $accion = strtolower($metodo);
        //si hay path faltante lo resuelve en un metodo get_recurso1_recurso2
        $hay_path_por_resolver = array_search(basename($clase, '.php'), $colecciones);

        if (false !== $hay_path_por_resolver && count($colecciones) > ($hay_path_por_resolver + 1)) {
            $path_faltante = array_slice($colecciones, $hay_path_por_resolver + 1);
            $accion .= '_'.implode('_', $path_faltante);
        }

        //Apunta a la coleccion
        if (count($colecciones) == count($parametros) + 1) {
            $accion .= self::SUFIJO_COLECCION;
        }

        return $accion;
    }
}
