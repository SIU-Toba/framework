<?
require_once('comando.php');
require_once('modelo/proyecto.php');

class comando_proyecto extends comando_toba
{
	static function get_info()
	{
		return 'Administracion de los METADATOS correspondientes a un PROYECTOS';
	}

	/**
	*	Compila los metadatos del proyecto
	*/
	function opcion__compilar()
	{
		/*
		
			Duda: que es mejor?
				- Manejar los procesos por separado o meterlos todos dentro del comando proyecto??

		$proyecto = new proyecto( 	$this->consola->get_dir_raiz(),
									$this->consola->get_instancia(),
									$this->consola->get_proyecto() );
		$proyecto->set_interface_usuario( $this->consola );
		$proyecto->compilar();
		*/
	}

	/**
	*	Exporta los metadatos del proyecto
	*/
	function opcion__exportar()
	{
		/*
		$proyecto = new proyecto( 	$this->consola->get_dir_raiz(),
									$this->consola->get_instancia(),
									$this->consola->get_proyecto() );
		$proyecto->set_interface_usuario( $this->consola );
		$proyecto->exportar();
		*/
	}

}
?>