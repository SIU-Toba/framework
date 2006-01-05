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

	static function get_info()
	{
		return 'No definida';
	}
	
	function mostrar_ayuda()
	{
		echo "AYUDA";
		/*
			Este metodo tiene que inspeccionar la clase,
			tomar tomaro todos los metodos que empiezen con opcion__,
			leer su doc, y generar una lista de opciones con parametros
		*/	
	}

	function procesar()
	{
		if ( count( $this->argumentos ) == 0 ) {
			$this->mostrar_ayuda();
		} else {
			$opcion = 'opcion__' . $this->argumentos[0];
			if( method_exists( $this, $opcion ) ) {
				$this->$opcion();	
			}
		}
		/*
			$proceso = new $id_proceso( $this->dir_raiz, $this->instancia, $this->proyecto );
			$proceso->set_interface_usuario( $this );
			try{
				$proceso->procesar( $argumentos );		
			} catch (excepcion_toba $e) {
				echo "Error ejecutando el proceso.\n" . $e->getMessage();	
			}
		*/
	}
}
?>