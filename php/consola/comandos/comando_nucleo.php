<?
require_once('comando.php');
require_once('modelo/proyecto.php');

class comando_nucleo extends comando_toba
{
	static function get_info()
	{
		return 'Administracion de la informacion perteneciente al nucleo del sistema';
	}

	/**
	*	Genera la metadata necesaria para los exportadores.
	*/
	function opcion__parsear_ddl()
	{
		require_once('modelo/parser_ddl.php');
		$parser = new parser_ddl( $this->consola, $this->consola->get_dir_raiz() );
		$parser->procesar();
	}

	/**
	*	Exporta las tablas maestras del sistema.
	*/
	function opcion__exportar_datos()
	{
		require_once('modelo/exportador_tablas_nucleo.php');
		$parser = new exportador_tablas_nucleo( $this->consola, $this->consola->get_dir_raiz() );
		$parser->procesar();

	}
}
?>