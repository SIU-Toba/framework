<?
class comando
{
	protected $manejador_interface;
	protected $argumentos;

	function __construct( gui $manejador_interface )
	{
		$this->manejador_interface = $manejador_interface;
	}

	static function get_info(){}

	function mostrar_observaciones(){}

	function get_nombre()
	{
		$nombre = get_class( $this );
		$temp = explode('_', $nombre);
		return $temp[1];
	}
	
	function set_argumentos( $argumentos )
	{
		$this->argumentos = $argumentos;		
	}

	function procesar()
	{
		if ( count( $this->argumentos ) == 0 ) {
			$this->mostrar_ayuda();
		} else {
			$opcion = 'opcion__' . $this->argumentos[0];
			if( method_exists( $this, $opcion ) ) {
				$this->$opcion();	
			} else {
				$this->manejador_interface->mensaje("La opcion '".$this->argumentos[0]."' no existe");
				$this->mostrar_ayuda();
			}
		}
	}

	function mostrar_ayuda()
	{
		$this->manejador_interface->titulo( $this->get_info() );
		$this->mostrar_observaciones();
		$this->manejador_interface->subtitulo( 'Lista de opciones' );
		$this->manejador_interface->coleccion( $this->inspeccionar_opciones() );
	}

	function inspeccionar_opciones()
	{
		$opciones = array();
		$clase = new ReflectionClass(get_class($this));
		foreach ($clase->getMethods() as $metodo){
			if (substr($metodo->getName(), 0, 8) == 'opcion__'){
				$temp = explode('__', $metodo->getName());
				$nombre = $temp[1];
				$info = parsear_doc_comment( $metodo->getDocComment() );
				$opciones[ $nombre ] = $info;
			}
		}
		return $opciones;
	}
}
?>