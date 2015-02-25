<?php
namespace SIUToba\rest\chulupi;


use kernel\kernel;
use SIUToba\rest\lib\lector_recursos_archivo;

class chulupi_lector_recursos extends lector_recursos_archivo{


	protected function obtener_archivo($path)
	{
		$clase = $this->get_path_relativo($path);
		$info = kernel::localizador()->buscar_archivo($clase);
		return $info['path'];
	}

	protected function get_path_relativo($path)
	{
		$clase = str_replace('\\', '/', $path); //normalizar
		$clase = substr($clase, strpos($clase, '/siu/') + 5); //desde siu
		return $clase;
	}

}