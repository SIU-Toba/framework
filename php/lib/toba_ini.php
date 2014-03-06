<?php

/**
 * Clase que permite leer/grabar en archivos de configuración .ini
 * @package Varios
 */
class toba_ini
{
	private $path = null;
	private $titulo;
	private $entradas=array();
	
	function __construct( $path = null )
	{
		if ( isset( $path ) ) {
			$this->path = $path;
			if ( file_exists( $this->path ) ) {
				$this->entradas = parse_ini_file( $this->path, true );
			}
		}
	}

	function agregar_titulo( $titulo )
	{
		$this->titulo = $titulo;	
	}

	//-----------------------------------------------------------
	//	Manipulacion de ENTADAS
	//-----------------------------------------------------------

	function get_entradas()
	{
		return $this->entradas;
	}
	
	function existe_entrada($seccion, $nombre=null)
	{
		if (! isset($nombre)) {
			return isset($this->entradas[$seccion]);
		} else {
			return isset($this->entradas[$seccion][$nombre]);
		}
	}

	function agregar_entrada($nombre, $datos)
	{
		$this->entradas[ $nombre ] = $datos;
	}

	function eliminar_entrada( $nombre )
	{
		if ( isset( $this->entradas[ $nombre ] ) ) {
			unset( $this->entradas[ $nombre ] );
		} else {
			throw new toba_error("La entrada '$nombre' no existe");
		}
	}

	function get_datos_entrada( $nombre ) 
	{
		if ( isset( $this->entradas[ $nombre ] ) ) {
			return $this->entradas[ $nombre ];
		} else {
			throw new toba_error("La entrada '$nombre' no existe en '{$this->path}'");
		}
	}

	function get($seccion, $clave=null, $defecto=null, $obligatorio=true)
	{
		if(isset($clave) && isset($this->entradas[$seccion][$clave])) {
			return $this->entradas[$seccion][$clave];
		}
		if (! isset($clave) && isset($this->entradas[$seccion])) {
			return $this->entradas[$seccion];
		}
		if ($obligatorio) {
			throw new toba_error("No se encuentra definido el parámetro '$seccion' $clave en {$this->path}");
		} else {
			return $defecto;
		}
	}
	
	function set_datos_entrada( $nombre, $datos ) 
	{
		if ( isset( $this->entradas[ $nombre ] ) ) {
			$this->entradas[ $nombre ] = $datos;
		} else {
			throw new toba_error("La entrada '$nombre' no existe");
		}
	}

	//-----------------------------------------------------------
	//	Generacion
	//-----------------------------------------------------------

	function guardar( $archivo = null )
	{
		if ( ! isset( $archivo ) ) {
			if ( ! isset( $this->path ) )  {
				throw new toba_error('Es necesario especificar el PATH del INI que se desea generar');	
			} else {
				$archivo = $this->path;	
			}
		}
		file_put_contents($archivo, $this->generar_ini() );
	}

	private function generar_ini()
	{
		$ini = "";
		if ( isset( $this->titulo ) ) {
			$ini .= "; $this->titulo \n";
			$ini .= "\n";
		}
		// Secciones
		foreach ( $this->entradas as $nombre => $datos ) {
			if ( is_array( $datos ) ) {
				$ini .= "\n";
				$ini .= "[$nombre]\n";
				foreach ( $datos as $directiva => $valor ) {
					$ini .= "$directiva = \"$valor\"\n";
				}

			} else {
				$ini .= "$nombre = \"$datos\"\n";
			}
		}
		return $ini;	
	}
}
?>
