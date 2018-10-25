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
	
	/**
	 * Constructor
	 * @param string $path
	 */
	function __construct( $path = null )
	{
		if ( isset( $path ) ) {
			$this->path = $path;
			if ( file_exists( $this->path ) ) {
				$this->entradas = parse_ini_file( $this->path, true );
			}
		}
	}
	
	/**
	 * Agrega un titulo al archivo en forma de comentario
	 * @param string $titulo
	 */
	function agregar_titulo( $titulo )
	{
		$this->titulo = $titulo;	
	}

	//-----------------------------------------------------------
	//	Manipulacion de ENTADAS
	//-----------------------------------------------------------
	/**
	 * Devuelve las entradas del archivo
	 * @return array
	 */
	function get_entradas()
	{
		return $this->entradas;
	}

	/**
	 * Elimina el contenido del archivo en memoria
	 */
	function vaciar()
	{
		$this->entradas = array();
	}

	/**
	 * Devuelve si existe una entrada determinada
	 * @param string $seccion
	 * @param string $nombre
	 * @return mixed
	 */
	function existe_entrada($seccion, $nombre=null)
	{
		if (! isset($nombre)) {
			return isset($this->entradas[$seccion]);
		} else {
			return isset($this->entradas[$seccion][$nombre]);
		}
	}

	/**
	 * Agrega una entrada con datos
	 * @param string $nombre
	 * @param mixed $datos
	 */
	function agregar_entrada($nombre, $datos)
	{
		$this->entradas[ $nombre ] = $datos;
	}

	/**
	 * Elimina la entrada indicada
	 * @param string $nombre
	 * @throws toba_error
	 */
	function eliminar_entrada( $nombre )
	{
		if ( isset( $this->entradas[ $nombre ] ) ) {
			unset( $this->entradas[ $nombre ] );
		} else {
			throw new toba_error("La entrada '$nombre' no existe");
		}
	}

	/**
	 * Devuelve los datos de la entrada indicada
	 * @param string $nombre
	 * @return mixed
	 * @throws toba_error
	 */
	function get_datos_entrada( $nombre ) 
	{
		if ( isset( $this->entradas[ $nombre ] ) ) {
			return $this->entradas[ $nombre ];
		} else {
			throw new toba_error("La entrada '$nombre' no existe en '{$this->path}'");
		}
	}

	/**
	 * Devuelve los datos pedidos lanzando una excepcion si no son encontrados
	 * @param string $seccion
	 * @param string $clave
	 * @param mixed $defecto
	 * @param boolean $obligatorio
	 * @return mixed
	 * @throws toba_error
	 */
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
	
	/**
	 * Modifica los datos para una entrada
	 * @param string $nombre
	 * @param mixed $datos
	 * @throws toba_error
	 */
	function set_datos_entrada( $nombre, $datos ) 
	{
		if ( isset( $this->entradas[ $nombre ] ) ) {
			$this->entradas[ $nombre ] = $datos;
		} else {
			throw new toba_error("La entrada '$nombre' no existe");
		}
	}

	/**
	 * Fija todas las entradas del archivo
	 * @param array $datos
	 */
	function set_entradas($datos)
	{
		$this->entradas = $datos;
	}
	
	//-----------------------------------------------------------
	//	Generacion
	//-----------------------------------------------------------
	/**
	 * Dispara el guardado con un nombre de archivo especifico
	 * @param string $archivo
	 * @throws toba_error
	 */
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

	/**
	 * Genera la estructura del ini a guardar
	 * @return string
	 */
	private function generar_ini()
	{
		$ini = "";
		if ( isset( $this->titulo ) ) {
			$ini .= "; $this->titulo \n";
			$ini .= "\n";
		}
		// Config Gral
		foreach ( $this->entradas as $nombre => $datos ) {
			if (! is_array( $datos ) ) {
				$ini .= "$nombre = \"$datos\"\n";
			}
		}

		// Secciones
		foreach ( $this->entradas as $nombre => $datos ) {
			if ( is_array( $datos ) ) {
				$ini .= "\n";
				$ini .= "[$nombre]\n";
				foreach ( $datos as $directiva => $valor ) {
					$ini .= "$directiva = \"$valor\"\n";
				}
			}
		}
		return $ini;	
	}
}
?>
