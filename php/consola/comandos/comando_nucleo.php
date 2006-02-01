<?
require_once('comando_toba.php');
require_once('modelo/nucleo.php');

/**
*	Publica los servicios de la clase NUCLEO a la consola toba
*/
class comando_nucleo extends comando_toba
{
	static function get_info()
	{
		return 'Administracion de la informacion perteneciente al nucleo del sistema';
	}

	/**
	*	Devuelve una referencia al NUCLEO
	*/
	private function get_elemento()
	{
		$nucleo = new nucleo(	$this->get_dir_raiz() );
		$nucleo->set_manejador_interface( $this->manejador_interface );
		return $nucleo;
	}

	//-------------------------------------------------------------
	// Opciones
	//-------------------------------------------------------------

	/**
	*	Genera la metadata necesaria para los exportadores.
	*/
	function opcion__parsear_ddl()
	{
		$this->get_elemento()->parsear_ddl();
	}

	/**
	*	Exporta las tablas maestras del sistema.
	*/
	function opcion__exportar_datos()
	{
		$this->get_elemento()->exportar();
	}
}
?>