<?php

namespace rest\lib;


class lector_recursos_archivo
{

	protected $prefijo_recursos;
	protected $directorio_recursos;

	function __construct($directorio, $prefijo_recursos)
	{
		$this->directorio_recursos = $directorio;
		$this->prefijo_recursos = $prefijo_recursos;
	}

	function get_directorio_recursos()
	{
		return $this->directorio_recursos;
	}

	/**
	 * @param array $path la sucesion de recursos anidados. El recurso es el ultimo que exista
	 * @return array recurso => clase-que-lo-implementa
	 */
	function get_recurso($path)
	{
		//Busco del mas especifico al mas general
		while (!empty($path)) {
			$recurso = array_pop($path);
			$ruta_padres = implode(DIRECTORY_SEPARATOR, $path);
			if ($file = $this->existe_recurso($ruta_padres, $recurso)) {
				return array('recurso' => $recurso,
							'archivo' => $file);
			}
		}
		return false;
	}

	/**
	 * Un recurso a/b, puede estar implementado en a/b.php o a/b/b.php
	 * @param $path string a/
	 * @param $name string b
	 * @return bool
	 */
	private function existe_recurso($path, $name)
	{
		$path = ($path) ? $path . DIRECTORY_SEPARATOR : '';
		$nombre_recurso = $this->prefijo_recursos . $name . '.php';
		$directorio = $this->directorio_recursos . DIRECTORY_SEPARATOR . $path;
		$como_archivo = $directorio . $nombre_recurso;
		$como_carpeta_archivo = $directorio . $name . DIRECTORY_SEPARATOR . $nombre_recurso;

		if (file_exists($como_archivo)) {
			return $como_archivo;
		}
		if (file_exists($como_carpeta_archivo)) {
			return $como_carpeta_archivo;
		}
		return false;
	}
}