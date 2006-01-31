<?
require_once('comando.php');
require_once('modelo/proyecto.php');

class comando_proyecto extends comando_toba
{
	static function get_info()
	{
		return 'Administracion de los METADATOS correspondientes a PROYECTOS';
	}

	private function get_id_proyecto_actual()
	{
		if ( isset( $this->argumentos[1] ) ) {
			$id = $this->argumentos[1];
		} else {
			$id = $this->consola->get_proyecto();
		}
		return $id;
	}

	private function get_elemento()
	{
		$proyecto = new proyecto(	$this->consola->get_dir_raiz(),
									$this->consola->get_instancia(),
									$this->get_id_proyecto_actual() );
		$proyecto->set_manejador_interface( $this->consola );
		return $proyecto;
	}

	/**
	*	Exporta los metadatos del proyecto
	*/
	function opcion__exportar()
	{
		$this->get_elemento()->exportar();
	}

	/**
	*	Compila los metadatos del proyecto
	*/
	function opcion__compilar()
	{
		$this->get_elemento()->compilar();
	}
}
?>