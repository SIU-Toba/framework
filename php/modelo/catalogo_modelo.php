<?
require_once('modelo/lib/excepciones.php');
require_once('modelo/proyecto.php');
require_once('modelo/instancia.php');
require_once('modelo/instalacion.php');
require_once('modelo/nucleo.php');
require_once('modelo/conversor.php');

class catalogo_modelo
{
	/**
	*	Crea una instalacion
	*/
	static function get_instalacion( $manejador_interface )
	{
		$instalacion = new instalacion();
		$instalacion->set_manejador_interface( $manejador_interface );
		return $instalacion;
	}

	/**
	*	Crea una instancia
	*/
	static function get_instancia( $id_instancia, $manejador_interface )
	{
		$instancia = new instancia(	$id_instancia );
		$instancia->set_manejador_interface( $manejador_interface );
		return $instancia;
	}
	
	/**
	*	Crea un proyecto
	*/
	static function get_proyecto( $id_instancia, $id_proyecto, $manejador_interface )
	{
		$instancia = self::get_instancia( $id_instancia, $manejador_interface );
		$proyecto = new proyecto( $instancia, $id_proyecto );
		$proyecto->set_manejador_interface( $manejador_interface );
		return $proyecto;
	}

	/**
	*	Crea un manejador de nucleo
	*/
	static function get_nucleo( $manejador_interface )
	{
		$nucleo = new nucleo();
		$nucleo->set_manejador_interface( $manejador_interface );
		return $nucleo;
	}

	/**
	*	Crea un conversor
	*/
	static function get_conversor( $id_instancia, $manejador_interface )
	{
		$instancia = self::get_instancia( $id_instancia, $manejador_interface );
		$conversor = new conversor( $instancia );
		$conversor->set_manejador_interface( $manejador_interface );
		return $conversor;
	}
}
?>