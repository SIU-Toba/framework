<?php
require_once('nucleo/lib/manejador_archivos.php');

class version_toba
{
	protected $partes;
	
	function __construct($numero)
	{
		$this->partes = explode('.', $numero);
		//Validando el numero
		if (count($this->partes) !== 3) {
			throw new excepcion_toba("El n�mero de versi�n $numero es incorrecto. Las versiones constan de tres numeros A.B.C");
		}
		foreach ($this->partes as $parte) {
			if (!is_numeric($parte) || !is_int(intval($parte))) {
				throw new excepcion_toba("El n�mero de versi�n $numero es incorrecto. El formato es A.B.C siendo estas componentes n�meros enteros");
			}
		}
	}

	function __toString()
	{
		return implode('.', $this->partes);
	}
	
	function es_igual($version)
	{
		return $this->comparar($version) == 0;
	}
	
	function es_menor($version)
	{
		return $this->comparar($version) < 0;
	}
	
	function es_mayor($version)
	{
		return $this->comparar($version) > 0;
	}
	
	function es_mayor_igual($version)
	{
		$res = $this->comparar($version);
		return ($res == 0 || $res > 0);		
	}
	
	function es_menor_igual($version)
	{
		$res = $this->comparar($version);
		return ($res == 0 || $res < 0);		
	}	
	
	/**
	 * Compara dos versiones y retorna si la actual es mayor (1), igual (0) o menor (-1)
	 */
	function comparar($otra_version)
	{
		foreach ($otra_version->partes as $pos => $parte) {
			if ($this->partes[$pos] < $parte) {
				return -1;	//Es menor
			} else if ($this->partes[$pos] > $parte) {
				return 1;	//Es mayor
			}
		}
		return 0; //Son iguales		
	}

	static function comparar_versiones($v1, $v2)
	{
		return $v1->comparar($v2);
	}

	/**
	 * Retorna todas las migraciones disponibles desde la actual hasta la versi�n parametro
	 *
	 * @param version_toba $hasta
	 * @param string $path_migraciones Path opcional de donde se ubican las migraciones (solo para tests)
	 */
	function get_camino_migraciones($hasta, $path_migraciones=null)
	{
		if (!isset($path_migraciones)) {
			$dir = toba_dir()."/php/modelo/migraciones";
		} else {
			$dir = $path_migraciones;
		}
		$exp = "/migracion_([0-9]+)_([0-9]+)_([0-9]+)\\.php/";
		$archivos = manejador_archivos::get_archivos_directorio($dir, $exp, false);
		sort($archivos);
		$versiones = array();
		foreach ($archivos as $archivo) {
			$partes = array();
			preg_match($exp, $archivo, $partes);
			array_shift($partes);
			$version = new version_toba(implode(".", $partes));
			if ($this->es_menor($version) && $hasta->es_mayor_igual($version)) {
				$versiones[] = $version;
			}
		}
		usort($versiones, array('version_toba', 'comparar_versiones')); 
		return $versiones;
	}
	
}


?>