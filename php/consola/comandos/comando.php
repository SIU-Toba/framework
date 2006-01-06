<?
/*
*	Ancestro de todos los comandos de consola
*/
class comando_toba
{
	protected $argumentos;
	protected $consola;			// Referencia a la consola

	function __construct( $consola )
	{
		$this->consola = $consola;	
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
				$this->consola->mensaje("La opcion '".$this->argumentos[0]."' no existe");
				$this->mostrar_ayuda();
			}
		}
	}

	static function get_info()
	{
		return 'No definida';
	}
	
	function mostrar_ayuda()
	{
		$this->consola->titulo( $this->get_info() );
		$this->consola->subtitulo( 'Lista de opciones' );
		$this->consola->coleccion( $this->inspeccionar_opciones() );
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