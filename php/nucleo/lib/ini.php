<?

class ini
{
	private $path = null;
	private $titulo;
	private $directivas=array();
	private $secciones=array();
	
	function __construct( $path = null )
	{
		if ( isset( $path ) ) {
			$this->path = $path;
			if ( file_exists( $this->path ) ) {
				$this->secciones = parse_ini_file( $this->path, true );
			}
		}
	}

	function agregar_titulo( $titulo )
	{
		$this->titulo = $titulo;	
	}

	//-----------------------------------------------------------
	//	Manipulacion de DIRECTIVAS
	//-----------------------------------------------------------

	function agregar_directiva($nombre, $valor)
	{
		$this->directivas[ $nombre ] = $valor;
	}

	//-----------------------------------------------------------
	//	Manipulacion de SECCIONES
	//-----------------------------------------------------------

	function existe_seccion( $nombre )
	{
		return isset( $this->secciones[ $nombre ] );
	}

	function agregar_seccion($nombre, $datos)
	{
		$this->secciones[ $nombre ] = $datos;
	}

	function eliminar_seccion( $nombre )
	{
		if ( isset( $this->secciones[ $nombre ] ) ) {
			unset( $this->secciones[ $nombre ] );
		} else {
			throw new excepcion_toba("El metodo '$nombre' no existe");
		}
	}

	function get_datos_seccion( $nombre ) 
	{
		if ( isset( $this->secciones[ $nombre ] ) ) {
			return $this->secciones[ $nombre ];
		} else {
			throw new excepcion_toba("El metodo '$nombre' no existe");
		}
	}
	
	function set_datos_seccion( $nombre, $datos ) 
	{
		if ( isset( $this->secciones[ $nombre ] ) ) {
			$this->secciones[ $nombre ] = $datos;
		} else {
			throw new excepcion_toba("El metodo '$nombre' no existe");
		}
	}

	//-----------------------------------------------------------
	//	Generacion
	//-----------------------------------------------------------

	function guardar( $archivo = null )
	{
		if ( ! isset( $archivo ) ) {
			if ( ! isset( $this->path ) )  {
				throw new excepcion_toba('Es necesario especificar el PATH de la clase que se desea generar');	
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
		}
		// Primero van las directivas sueltas
		if ( $this->directivas ) {
			$ini .= "\n";
			foreach ( $this->directivas as $directiva => $valor ) {
				$ini .= "$directiva = $valor\n";
			}
			$ini .= "\n";
		}
		// Secciones
		foreach ( $this->secciones as $nombre => $datos ) {
			$ini .= "\n";
			$ini .= "[$nombre]\n";
			$ini .= "\n";
			foreach ( $datos as $directiva => $valor ) {
				$ini .= "$directiva = $valor\n";
			}
		}
		return $ini;	
	}
}
?>