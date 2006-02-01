<?
require_once('comando_toba.php');
require_once('modelo/proyecto.php');

/**
*	Publica los servicios de la clase PROYECTO a la consola toba
**/
class comando_proyecto extends comando_toba
{
	static function get_info()
	{
		return 'Administracion de los METADATOS correspondientes a PROYECTOS';
	}

	function mostrar_observaciones()
	{
		$this->manejador_interface->mensaje("INVOCACION: toba proyecto 'opcion' [id_proyecto] [id_instancia]");
		$this->manejador_interface->enter();
		$this->manejador_interface->mensaje("Si no se indica [id_proyecto] se utiliza la variable de entorno 'toba_proyecto' ( valor actual: '". $this->get_entorno_id_proyecto( false ). "' ) " );
		$this->manejador_interface->mensaje("Si no se indica [id_instancia] se utiliza la variable de entorno 'toba_instancia' ( valor actual: '". $this->get_entorno_id_instancia( false ). "' ) " );
		$this->manejador_interface->enter();
	}

	/**
	*	Determina la instancia sobre la que se va a trabajar
	*/
	private function get_id_instancia_actual()
	{
		if ( isset( $this->argumentos[2] ) ) {
			$id = $this->argumentos[2];
		} else {
			$id = $this->get_entorno_id_instancia();
		}
		return $id;
	}

	/**
	*	Determina el PROYECTO sobre el que se va a trabajar
	*/
	private function get_id_proyecto_actual()
	{
		if ( isset( $this->argumentos[1] ) ) {
			$id = $this->argumentos[1];
		} else {
			$id = $this->get_entorno_id_proyecto();
		}
		return $id;
	}

	/**
	*	Devuelve una referencia al PROYECTO
	*/
	private function get_elemento()
	{
		$proyecto = new proyecto(	$this->get_dir_raiz(),
									$this->get_id_instancia_actual(),
									$this->get_id_proyecto_actual() );
		$proyecto->set_manejador_interface( $this->manejador_interface );
		return $proyecto;
	}

	//-------------------------------------------------------------
	// Opciones
	//-------------------------------------------------------------

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