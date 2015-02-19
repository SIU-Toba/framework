<?php

namespace rest\docs;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use rest\lib\modelo_recursos;
use rest\lib\rest_error;
use rest\lib\rest_instanciador;
use rest\lib\ruteador;
use rest\rest;

class controlador_docs
{

    protected $api_root;
    protected $api_url;

    protected $list;


    function __construct($api_root, $api_url)
    {
        $this->api_root = $api_root;
        $this->api_url = $api_url;
    }

    /**
     * Retorna la documentacion en formato swagger para el path. Si el path
     * es nulo retorna la delcaracion de recursos, sino retorna la api para el path
     */
    public function get_documentacion($path)
    {
        if (empty($path)) {
            $lista = $this->getResourceList();
            return rest::response()->get($lista);
        } else {
            throw new rest_error("En esta version toda la documentación esta en la raiz");
        }
    }

    protected function getResourceList()
    {
        $list = array();
        $this->list = &$list;
        $list['swagger'] = "2.0";
        $list['info'] = array();
        $list['basePath'] = $this->api_url;
        $list['produces'] = array("application/json");
        $list['apiVersion'] = '1.0';
        $list['paths'] = array();


        $this->list['paths'] = array();
        $this->list['definitions'] = array();

        $lista_apis = $this->get_lista_apis();
        foreach ($lista_apis as $path)
        {

            $this->add_apis($path);
            $this->add_modelos($path);
        }

        $this->reordenar_lista_apis($list['paths']);
        return $list;
    }

    protected function get_lista_apis()
    {
        $list = array();
        $path = realpath($this->api_root);
        $archivos_api = $this->obtener_clases_directorio($path);

        foreach ($archivos_api as $nombre => $objeto) {
            if ('php' !== pathinfo($nombre, PATHINFO_EXTENSION)) {
                continue;
            }
            $prefijo = rest::app()->config('prefijo_controladores');

            if (! $this->empieza_con($prefijo, pathinfo($nombre, PATHINFO_BASENAME)) ){
                continue;
            }
            $nombre = str_replace('\\', '/', $nombre); // windows! ...

            $path = $this->get_url_de_clase($nombre);
            $path = ltrim($path,'/') ;

            $list[] = $path;
        }
        return $list;
    }


    protected function add_apis($path)
    {

        /** @var $reflexion anotaciones_docs */
        $reflexion = $this->get_annotaciones_de_path($path);
        $metodos = $reflexion->get_metodos();

        foreach ($metodos as $metodo) {
            $parametros = $metodo['parametros'];
            $nombre_metodo = $metodo['nombre'];

            $alias = '';
            $partes_nombre_alias = explode('__', $nombre_metodo);
            if(count($partes_nombre_alias) > 1){
                $alias = $partes_nombre_alias[1];
                $nombre_metodo = $partes_nombre_alias[0];
            }

            $partes_nombre = explode('_', $nombre_metodo);
            $prefijo_metodo = array_shift($partes_nombre);
            if ($es_coleccion = $this->termina_con(ruteador::SUFIJO_COLECCION, $nombre_metodo)) {
                array_pop($partes_nombre); //SUFIJO_COLECCION
            }

            /////------------PARAMETERS ---------------------------------
            $params_path = array();
            $partes_path = explode('/', $path);

            foreach($partes_nombre as $parte){
                $partes_path[] = $parte;
            }

            $nro_parametro = 0;
            $api_path = '';// $path;

            foreach ($partes_path as $parte) {
                $api_path .= "/" . $parte;
                if (isset($parametros[$nro_parametro])) {
                    $param_name = $parametros[$nro_parametro++];
                    $api_path .= "/{" . $param_name . "}";
                    $params_path[] = $this->get_parametro_path($param_name, $parte);
                }
            }
            if($alias){
                $api_path .= '/' . $alias;
            }
            ////--------------------------------------------------------
            $params_query = $reflexion->get_parametros_metodo($metodo, 'query');
            $params_body = $reflexion->get_parametros_metodo($metodo, 'body');

            $operation = array();
            $operation['tags'] = array( $path );
            $operation['method'] = strtolower($prefijo_metodo);
            $operation['summary'] = $reflexion->get_summary_metodo($metodo);
            $operation['description'] = $reflexion->get_notes_metodo($metodo);

            $operation['operationId'] = $nombre_metodo;
            $operation['parameters'] = array_merge($params_path, $params_body, $params_query);

            $operation['responses'] = $reflexion->get_respuestas_metodo($metodo);

            $this->list['paths'][$api_path][$operation['method']] = $operation;

        }

    }

    protected function reordenar_lista_apis(&$apis_paths)
    {
        $orden_apis = array();
        foreach ($apis_paths as $api_path => $api) {
            $orden_apis[] = $api_path;
            $this->reordenar_operaciones($apis_paths[$api_path]);
        }
        array_multisort($orden_apis, SORT_ASC, $apis_paths);
    }

    /**
     * Reordena los distintas operaciones GET PUT DELETE UPDATE
     * @param $paths
     * @return array
     */
    protected function reordenar_operaciones(&$paths)
    {

        $orden_ops = array();
        foreach ($paths as $metodo => $detalle) {
            //3GET,3PUT,6DELETE,6UPDATE
            $orden_ops[] = strlen($metodo) . $metodo;
        }
        array_multisort($orden_ops, SORT_ASC, $paths);
    }

    protected function get_parametro_path($param_name, $parte)
    {
        $api_parameter = array();
        $api_parameter['name'] = $param_name;
        $api_parameter['in'] = "path";
        $api_parameter['description'] = "ID del recurso $parte";
        $api_parameter['type'] = "string";
        $api_parameter['required'] = true;
        return $api_parameter;
    }


    /**
     * Retorna la url del recurso REST en base a la ruta del archivo.
     * Se aceptan los formatos algo/recurso/recurso.php o /algo/recurso.php
     * Para ambos, la ruta es /algo/recurso
     * @param $ruta_absoluta
     * @return string
     */
    protected function get_url_de_clase($ruta_absoluta)
    {
        $partes = preg_split('/rest/', $ruta_absoluta);
        $path_relativo = $partes[1];
        $prefijo = rest::app()->config('prefijo_controladores');
        $clase_recurso = basename($path_relativo, '.php'); //recurso_padre
        $recurso = substr($clase_recurso, strlen($prefijo)); //padre
        if ($this->termina_con($recurso, dirname($path_relativo))) {
            // /rest/padre/hijo/recurso_hijo.php  => /padre/hijo
            $url = substr($path_relativo, 0, -strlen($clase_recurso . '.php') - 1);
        } else {
            // /rest/padre/recurso_hijo.php => /padre/hijo
            $url = substr($path_relativo, 0, -strlen($clase_recurso . '.php'));
            $url .= $recurso;
        }
        return $url;
    }

    /**
     * @param $path
     * @return anotaciones_docs
     */
    protected function get_annotaciones_de_path($path)
    {
        $lector = rest::app()->lector_recursos; //new lector_recursos_archivo($this->api_root);
        $archivo = $lector->get_recurso(explode('/', $path));
        return new anotaciones_docs($archivo['archivo']);
    }

    /**
     * @param $path
     * @return anotaciones_docs
     */
    protected function add_modelos($path)
    {
        $lector = rest::app()->lector_recursos; //new lector_recursos_archivo($this->api_root);
        $archivo = $lector->get_recurso(explode('/', $path));

        $i = new rest_instanciador();
        $i->archivo = $archivo['archivo'];
        $objeto = $i->get_instancia();

        if(method_exists($objeto, '_get_modelos')){
            $modelo = new modelo_recursos( );
            $specs = $modelo->to_swagger($objeto->_get_modelos());
            $this->list['definitions'] = array_merge($this->list['definitions'], $specs);
        }else {
            rest::app()->logger->debug('El objeto no tiene el metodo _get_modelos. Clase: '. get_class($objeto));
            return array();
        }

    }

    /**
     * @param $path
     * @return RecursiveIteratorIterator
     */
    protected function obtener_clases_directorio($path)
    {
        $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
        return $objects;
    }

    protected function termina_con($needle, $haystack)
    {
        return substr($haystack, -strlen($needle)) === $needle;
    }

    protected function empieza_con($prefijo, $string)
    {
        return substr($string, 0, strlen($prefijo)) === $prefijo;
    }
}