<?php
/**
 * Representa un punto de montaje con un esquema de autoload asociado y provee
 * facilidades para utilizarlo
 * @package Centrales
 * @subpackage Punto Montaje
 */
abstract class toba_punto_montaje_autoload extends toba_punto_montaje
{
	/**
	 * Permite que el archivo de autoload para el punto de montaje sea registrado 
	 * @throws toba_error
	 */
	function registrar_autoload()
	{
		$path_autoload = $this->get_path_autoload();
		$nombre_clase = $this->get_clase_autoload();
		$nombre_metodo = $this->get_metodo_autoload();

		$existe_archivo = file_exists($path_autoload);

		if ($existe_archivo) {
			require_once($path_autoload);
			if (method_exists($nombre_clase, $nombre_metodo)) {
				spl_autoload_register(array($nombre_clase, $nombre_metodo));
				toba::logger()->debug("PUNTO DE MONTAJE: se cargó exitosamente el autoload del punto de montaje {$this->get_etiqueta()}");
			} else {
				throw new toba_error("PUNTO DE MONTAJE PROYECTO: el método $nombre_clase::$nombre_metodo de autoload no existe");
			}
		} else {
			throw new toba_error("PUNTO DE MONTAJE PROYECTO: no se encuentra el archivo de autoload, verifique su existencia. Path: $path_autoload");
			// La existencia del archivo, no la de uno mismo porque es demasiado pedir al usuario (ni siquiera podemos requerir php 5.3 :p)
		}
	}
	
	/**
	 * Quita el archivo de autoload del PM, de la lista disponible
	 */
	function desregistrar_autoload()
	{
		$nombre_clase = $this->get_clase_autoload();
		$nombre_metodo = $this->get_metodo_autoload();
		spl_autoload_unregister(array($nombre_clase, $nombre_metodo));
	}
	
	function get_path()
	{
		return $this->path;
	}

	abstract protected function get_path_autoload();

	abstract protected function get_clase_autoload();

	abstract protected function get_metodo_autoload();
}
?>
