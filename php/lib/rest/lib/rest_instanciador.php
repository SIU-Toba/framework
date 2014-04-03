<?php

namespace rest\lib;

/**
 * Sirva para pasa almacenar el controlador, accion y parametros que se decodifica de la ruta
 * y para encapusular eventuales diferencias entre instanciar clases con namespace o globales.
 *
 * Class recurso_rest
 *
 * @package rest\rutas
 */
class rest_instanciador
{

	public $archivo;
	public $accion;
	public $parametros;

	protected $objeto;

	public function get_instancia($autoinclude = true)
	{
		if (!$this->objeto) {
			if ($autoinclude) {
				require_once $this->archivo;
			}
			$clase = $this->get_clase_de_ruta($this->archivo);
			$this->objeto = new  $clase();
		}
		return $this->objeto;
	}

	public function ejecutar_accion()
	{
		if (empty($this->parametros)) {
			$this->parametros = array();
		}
		return call_user_func_array(array($this->get_instancia(), $this->accion), $this->parametros);
	}

	public function existe_metodo($nombre)
	{
		$this->get_instancia(); //incluyo el archivo
		$clase = $this->get_clase_de_ruta($this->archivo);
		return method_exists($clase, $nombre);
	}

	private function get_clase_de_ruta($clase)
	{
		return $this->get_full_class_name($clase); // basename($clase, ".php");
	}

	protected function get_full_class_name($archivo) {
		$src = file_get_contents($archivo, 'r');

		if (preg_match('#\s*namespace\s+(.+?)\s*;.*class\s+(\w+).+;$#sm', $src, $m)) {
			return $m[1].'\\'.$m[2];
		}

		return $class = basename($archivo, '.php');
	}
}