<?
require_once('modelo/lib/excepciones.php');
require_once('modelo/proyecto.php');
require_once('modelo/instancia.php');
require_once('modelo/instalacion.php');
require_once('modelo/nucleo.php');
require_once('modelo/conversor.php');

class catalogo_modelo
{
	private $instalacion;				// Instalacion
	private $instancia;					// Array de instancias existentes en la instalacion
	static private $singleton;

	private function __construct(){}
	
	/**
	*	Devuelve una referencia a la INSTALACION
	*/
	function get_instalacion( $manejador_interface )
	{
		if ( ! isset( $this->instalacion ) ) {
			$this->instalacion = new instalacion();
			$this->instalacion->set_manejador_interface( $manejador_interface );
		}
		return $this->instalacion;
	}

	/**
	*	Devuelve una referencia a un INSTANCIA.
	*/
	function get_instancia( $id_instancia, $manejador_interface )
	{
		if ( ! isset ( $this->instancia[ $id_instancia ] ) ) {
			$instalacion = $this->get_instalacion( $manejador_interface );
			$this->instancia[ $id_instancia ] = new instancia( $instalacion, $id_instancia );
			$this->instancia[ $id_instancia ]->set_manejador_interface( $manejador_interface );
		}
		return $this->instancia[ $id_instancia ];
	}
	
	/**
	*	Devuelve una referencia a un PROYECTO
	*/
	function get_proyecto( $id_instancia, $id_proyecto, $manejador_interface )
	{
		$instancia = $this->get_instancia( $id_instancia, $manejador_interface );
		$proyecto = new proyecto( $instancia, $id_proyecto );
		$proyecto->set_manejador_interface( $manejador_interface );
		return $proyecto;
	}

	/**
	*	Devuelve una referencia al NUCLEO
	*/
	function get_nucleo( $manejador_interface )
	{
		$nucleo = new nucleo();
		$nucleo->set_manejador_interface( $manejador_interface );
		return $nucleo;
	}

	/**
	*	Devuelve una referencia al CONVERSOR
	*/
	function get_conversor( $id_instancia, $manejador_interface )
	{
		$instancia = self::get_instancia( $id_instancia, $manejador_interface );
		$conversor = new conversor( $instancia );
		$conversor->set_manejador_interface( $manejador_interface );
		return $conversor;
	}

	/**
	*	Singleton
	*/
	static function instanciacion()
	{
		if (!isset(self::$singleton)) {
			self::$singleton = new catalogo_modelo();	
		}
		return self::$singleton;	
	}	
}
?>
