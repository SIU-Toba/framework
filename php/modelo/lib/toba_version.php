<?php

class toba_version
{
	protected $partes;
	protected $extra;
	protected $path_migraciones;
	protected $inestables = array('pre-alpha', 'alpha', 'beta', 'rc');
	
	function __construct($numero)
	{
		$formato = 'El formato debe ser x.y.z(CODIGO-#). Donde CODIGO es opcional: pre-alpha,alpha,beta o rc (Ver http://web2.siu.edu.ar/isw/recursos/EsquemaVersionado.pdf)';
		$numero = trim($numero);
		$this->extra = null;
		$this->partes = explode('.', $numero);
		//Validando el numero
		if (count($this->partes) < 3) {
			throw new toba_error("El número de versión $numero es incorrecto. Se requiere al menos 3 partes. ".$formato);
		}
		if (!is_numeric($this->partes[2])) {
			$digito = intval($this->partes[2]);
			if (! is_numeric($digito)) {
				throw new toba_error("El número de versión $numero es incorrecto. Las partes deben ser numéricas. ".$formato);
			}
			$extra = substr($this->partes[2], strlen($digito));
			$this->partes[2] = $digito;			
			if ($extra != '') {
				$this->extra = explode('-', $extra);
				if (! in_array($this->extra[0], $this->inestables)) {
					throw new toba_error("El número de versión $numero es incorrecto. El codigo de inestable '{$this->extra[0]}' no es válido. ".$formato);
				}
				if (isset($this->extra[1]) && !is_numeric($this->extra[1])) {
					throw new toba_error("El número de versión $numero es incorrecto. El numero de versión inestable debe ser entero ".$formato);
				}
			}
		}
		foreach ($this->partes as $parte) {
			if (!is_numeric($parte) || !is_int(intval($parte))) {
				throw new toba_error("El número de versión $numero es incorrecto. Las partes deben ser numéricas ".$formato);
			}
		}
	}
	
	function path_migraciones()
	{
		return toba_dir()."/php/modelo/migraciones";		
	}

	function __toString()
	{
		$s = implode('.', $this->partes);
		if (isset($this->extra)) {
			$s .= $this->extra[0];
			if (isset($this->extra[1])) {
				$s .= '-'.$this->extra[1];
			}
		}
		return $s;
	}

	function get_string_partes($separador = '_')
	{
		$s = $this->__toString();
		return str_replace('.', $separador, $s);
	}
	
	function get_release($separador = '.')
	{
		return $this->partes[0].$separador.$this->partes[1];
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
		return ($this->comparar($version) >= 0);
	}
	
	function es_menor_igual($version)
	{
		return ($this->comparar($version) <= 0);		
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
		//--- Tienen los mismos numeros, se decide por partes extra
		if (! isset($this->extra)) {
			if (isset($otra_version->extra)) {
				return 1;	//La otra tiene extra, esta es la final por lo que es mayor
			} else {
				return 0;	//Ambas no tienen extra, son iguales
			}
		} else {
			if (! isset($otra_version->extra)) {
				return -1;	//Esta tiene extra, la otra no y es la final
			}
		}
		$indice_actual = array_search($this->extra[0], $this->inestables);
		$indice_otra = array_search($otra_version->extra[0], $this->inestables);
		//-- Son otro relase?
		if ($indice_actual < $indice_otra) {
			return -1;
		}
		if ($indice_actual > $indice_otra) {
			return 1;
		}
		//-- Son el mismo release, se compara por numero de release		
		$numero_actual = isset($this->extra[1]) ? $this->extra[1] : 1;
		$numero_otra = isset($otra_version->extra[1]) ? $otra_version->extra[1] : 1;
		if ($numero_actual < $numero_otra) {
			return -1;			
		}
		if ($numero_actual > $numero_otra) {
			return 1;
		}
		return 0; //Son iguales		
	}

	static function comparar_versiones($v1, $v2)
	{
		return $v1->comparar($v2);
	}
	
	function es_cambio_menor_version(toba_version $otra_version)
	{
		if (!isset($otra_version)) {
			return false;
		}
		//-- Tiene que ser igual numero mayor
		if ($otra_version->partes[0] != $this->partes[0]) {
			return false;
		}
		//-- Tiene que ser igual numero medio
		if ($otra_version->partes[1] != $this->partes[1]) {
			return false;
		}		
		return true;
	}	
	
	/**
	 * @return toba_version
	 */
	function get_siguiente_menor()
	{
		$siguiente = $this->partes[2] + 1;
		return new toba_version($this->partes[0].'.'.$this->partes[1].'.'.$siguiente);
	}
	

	/**
	 * Retorna todas las migraciones disponibles desde la actual hasta la versión parametro
	 *
	 * @param toba_version $hasta
	 * @param string $path_migraciones Path opcional de donde se ubican las migraciones (solo para tests)
	 */
	function get_secuencia_migraciones($hasta, $path_migraciones=null)
	{
		if (!isset($path_migraciones)) {
			$dir = $this->path_migraciones();
		} else {
			$dir = $path_migraciones;
		}
		$exp = "/migracion_(.+)\\.php/";
		$archivos = toba_manejador_archivos::get_archivos_directorio($dir, $exp, false);
		sort($archivos);
		$versiones = array();
		foreach ($archivos as $archivo) {
			$partes = array();
			preg_match($exp, $archivo, $partes);
			$numero = str_replace('_', '.', $partes[1]);
			$version = new toba_version($numero);
			if ($this->es_menor($version) && $hasta->es_mayor_igual($version)) {
				$versiones[] = $version;
			}
		}
		usort($versiones, array('toba_version', 'comparar_versiones')); 
		return $versiones;
	}
	
	function ejecutar_migracion($prefijo, $elemento, $metodo_part = null, $interface = null)
	{
		$nombre_clase = "toba_migracion_".$this->partes[0].'_'.$this->partes[1].'_'.$this->partes[2];
		$archivo = $this->path_migraciones()."/$nombre_clase.php";
		require_once($archivo);
		$migracion = new $nombre_clase($elemento);
		$clase = new ReflectionClass($nombre_clase);
		foreach ($clase->getMethods() as $metodo) {
			$nombre_metodo = $metodo->getName();
			$es_metodo = ($nombre_metodo == $metodo_part ||
						(!isset($metodo_part) && strpos($nombre_metodo, $prefijo."__") === 0));
			if ($es_metodo) {
				$cant = $metodo->invoke($migracion);
				if (isset($cant)) {
					toba_logger::instancia()->info("Migración $nombre_metodo: $cant registros.");
				}
				if (isset($interface)) {
					$interface->progreso_avanzar();	
				}
			}
		}
		$interface->progreso_fin();
	}
	
	static function inicial()
	{
		return new toba_version("0.8.3");
	}
	
}


?>