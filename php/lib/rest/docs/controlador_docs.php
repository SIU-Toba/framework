<?php

namespace rest\docs;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use rest\lib\ruteador;
use rest\rest;

class controlador_docs
{

	protected $api_root;
	protected $api_url;


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
			$resource = $this->getResource($path);
			return rest::response()->get($resource);
		}
	}

	protected function getResourceList()
	{
		$list = array();
		$list['apiVersion'] = '1.0';
		$list['swaggerVersion'] = "1.2";
		//$list['basePath'] = $this->api_url.'/api-docs';
		$list['apis'] = $this->get_lista_apis();
		$this->reordenar_lista_apis($list['apis']);
		return $list;
	}

	protected function getResource($path)
	{
		$path = str_replace( '_', '/', $path);
		$annotaciones = $this->get_annotaciones_de_path($path);
		$resource = array();
		$resource['apiVersion'] = '1.0'; //@todo
		$resource['swaggerVersion'] = "1.2";
		$resource['produces'] = array("application/json");
		$resource['basePath'] = $this->api_url;
		$resource['resourcePath'] = $path;
		$resource['apis'] = $this->get_apis($annotaciones, $path);
		$resource['models'] = $annotaciones->get_modelos_clase();
		return $resource;
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

//			$documentacion = new anotaciones_docs($nombre);

			$path = $this->get_url_de_clase($nombre);
			$path = ltrim($path,'/') ;
			$descripcion =  '/'.str_replace('/', '/{id}/', $path );
			$api_path =  '/'.str_replace('/', '_', $path);


			$api = array();
			$api['path'] = $api_path;
			$api['description'] = $descripcion; // $documentacion->get_descripcion_clase();
			$list[] = $api;
		}
		return $list;
	}


	protected function get_apis(anotaciones_docs $reflexion, $path)
	{
		$recursos = array();
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
			unset($partes_path[0]);
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
			$operation['method'] = strtoupper($prefijo_metodo);
			//$operation['produces'] = array("application/json");
			$operation['nickname'] = $nombre_metodo;
			$operation['parameters'] = array_merge($params_path, $params_body, $params_query);
			$operation['summary'] = $reflexion->get_summary_metodo($metodo);
			$operation['notes'] = $reflexion->get_notes_metodo($metodo);
			$operation['responseMessages'] = $reflexion->get_errores_metodo($metodo);
			$respuesta_metodo = $reflexion->get_respuestas_metodo($metodo);
			if (isset($respuesta_metodo['items'])) {
				$operation['items'] = $respuesta_metodo['items'];
			}
			if (isset($respuesta_metodo['type'])) {
				$operation['type'] = $respuesta_metodo['type'];
			}

			$recursos[$api_path]['operations'][] = $operation;
			$recursos[$api_path]['path'] = $api_path;
		}

		return $this->reordenar_recursos_api($recursos);
	}

	/**
	 * Reordena los distintos recursos, agrupandolos por path para
	 * cumplir con la especificacion swagger
	 * @param $recursos
	 * @return array
	 */
	protected function reordenar_recursos_api($recursos)
	{
		$apis = array();
		$orden = array();
		foreach ($recursos as $recurso) {

			$api = array();
			$api['path'] = $recurso['path'];
			$api['operations'] = $recurso['operations'];

			$orden_ops = array();
			foreach ($api['operations'] as $operaciones) {
				//3GET,3PUT,6DELETE,6UPDATE
				$orden_ops[] = strlen($operaciones['method']) . $operaciones['method'];
			}
			array_multisort($orden_ops, SORT_ASC, $api['operations']);

			$apis[] = $api;
			$orden[] = strlen($recurso['path']);
		}
		array_multisort($orden, SORT_ASC, $apis);
		return $apis;
	}


	protected function reordenar_lista_apis(&$apis)
	{
		$orden_apis = array();
		foreach ($apis as $api) {
			$orden_apis[] = $api['path'];
		}
		array_multisort($orden_apis, SORT_ASC, $apis);
	}


	protected function get_parametro_path($param_name, $parte)
	{
		$api_parameter = array();
		$api_parameter['paramType'] = "path";
		$api_parameter['name'] = $param_name;
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