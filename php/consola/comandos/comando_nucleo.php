<?
require_once('comando.php');
require_once('modelo/nucleo.php');

class comando_nucleo extends comando_toba
{
	static function get_info()
	{
		return 'Administracion de la informacion perteneciente al nucleo del sistema';
	}

	private function get_elemento()
	{
		$nucleo = new nucleo(	$this->consola->get_dir_raiz() );
		$nucleo->set_manejador_interface( $this->consola );
		return $nucleo;
	}

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