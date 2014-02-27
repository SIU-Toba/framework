<?php


namespace rest\lib;


class lector_recursos_archivo {

	protected $directorio_recursos;

	function __construct($directorio)
	{
		$this->directorio_recursos = $directorio;
	}

	/**
	 * @param array $path la sucesion de recursos anidados. El recurso es el ultimo que exista
	 * @return string la clase del recurso
	 */
	function get_recurso($path)
	{

		//Busco del mas especifico al mas general
		while (!empty($path)) {
			$recurso = array_pop($path);

			$ruta_padres = implode(DIRECTORY_SEPARATOR, $path);
			if($file = $this->existe_recurso($ruta_padres, $recurso)){
				return $file;
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
		$directorio = $this->directorio_recursos . DIRECTORY_SEPARATOR . $path;
		$como_archivo = $directorio . $name . '.php';
		$como_carpeta_archivo = $directorio . $name .DIRECTORY_SEPARATOR. $name. '.php';

		if(file_exists($como_archivo)) return $como_archivo;
		if(file_exists($como_carpeta_archivo)) return $como_carpeta_archivo;
		return false;
	}


}