<?
require_once('modelo/lib/excepciones.php');
require_once('modelo/proyecto.php');
require_once('modelo/instancia.php');
require_once('modelo/instalacion.php');
require_once('modelo/nucleo.php');
require_once('modelo/conversor.php');

class catalogo_modelo
{
	private $instancia;
	static private $singleton;

	private function __construct(){}
	
	static function instanciacion()
	{
		if (!isset(self::$singleton)) {
			self::$singleton = new catalogo_modelo();	
		}
		return self::$singleton;	
	}	

	/**
	*	Devuelve una referencia a un INSTANCIA. La forma actual de definir instancia hace que solo
	*	se pueda instanciar una por ejecucion ( se basa en la declaracion de una constante )
	*/
	function get_instancia( $id_instancia, $manejador_interface )
	{
		if ( ! isset ( $this->instancia ) ) {
			$this->instancia = new instancia( $id_instancia );
			$this->instancia->set_manejador_interface( $manejador_interface );
		} else {
			if ( $this->instancia->get_id() !== $id_instancia ) {
				throw new excepcion_toba("No es posible utilizar dos instancias distintas en un contexto de ejecucion.");	
			}	
		}
		return $this->instancia;
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
	*	Devuelve una referencia a la INSTALACION
	*/
	function get_instalacion( $manejador_interface )
	{
		$instalacion = new instalacion();
		$instalacion->set_manejador_interface( $manejador_interface );
		return $instalacion;
	}
}
?>