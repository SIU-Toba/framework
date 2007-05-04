<?php
/**
 * Esta clase representa a una clase estatica cuyos metodos proveen informacion fija.
 * @ignore
 */
class toba_clase_datos
{
	private $nombre;
	private $path = null;
	private $metodos=array();
	
	function __construct( $nombre, $path = null )
	{
		$this->nombre = $nombre;
		if ( isset( $path ) ) {
			$this->path = $path;
			if ( file_exists( $this->path ) ) {
				$this->cargar_clase();
			}
		}
	}

	function cargar_clase()
	{
		require_once( $this->path );
		$metodos = array();
		$clase = new ReflectionClass( $this->nombre );
		foreach ( $clase->getMethods() as $metodo ){
			$nombre = $metodo->getName();
			$datos = $metodo->invoke( null );
			$this->metodos[ $nombre ] = $datos;
		}
	}

	//-----------------------------------------------------------
	//	Manipulacion de la informacion de la clase
	//-----------------------------------------------------------

	function existe_metodo( $nombre )
	{
		return isset( $this->metodos[ $nombre ] );
	}

	function agregar_metodo_datos($nombre, $datos)
	{
		$this->metodos[ $nombre ] = $datos;
	}

	function eliminar_metodo_datos( $nombre )
	{
		if ( isset( $this->metodos[ $nombre ] ) ) {
			unset( $this->metodos[ $nombre ] );
		} else {
			throw new toba_error("El metodo '$nombre' no existe");
		}
	}

	function get_datos_metodo( $nombre ) 
	{
		if ( isset( $this->metodos[ $nombre ] ) ) {
			return $this->metodos[ $nombre ];
		} else {
			throw new toba_error("El metodo '$nombre' no existe");
		}
	}
	
	function set_datos_metodo( $nombre, $datos ) 
	{
		if ( isset( $this->metodos[ $nombre ] ) ) {
			$this->metodos[ $nombre ] = $datos;
		} else {
			throw new toba_error("El metodo '$nombre' no existe");
		}
	}

	//-----------------------------------------------------------
	//	Generacion
	//-----------------------------------------------------------

	function guardar( $archivo = null )
	{
		if ( ! isset( $archivo ) ) {
			if ( ! isset( $this->path ) )  {
				throw new toba_error('Es necesario especificar el PATH de la clase que se desea generar');	
			} else {
				$archivo = $this->path;	
			}
		}
		file_put_contents($archivo, $this->generar_php() );
	}

	private function generar_php()
	{
		$php = "<?php\n";
		$php .= $this->get_contenido();
		$php .= "\n?>";
		return $php;	
	}

	function get_contenido()
	{

		$php = "\nclass $this->nombre\n{\n";
		foreach ( $this->metodos as $metodo => $datos ) {
			$php .= "\tstatic function $metodo()\n\t{\n";
			$php .= "\t\treturn " . var_export( $datos, true) . ";\n";
			$php .= "\t}\n";
			$php .= "\n";
		}
		$php .= "}\n";
		return $php;
	}
}
?>