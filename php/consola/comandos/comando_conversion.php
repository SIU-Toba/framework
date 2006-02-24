<?
require_once('comando_toba.php');

class comando_conversion extends comando_toba
{
	static function get_info()
	{
		return 'Administracion de cambios entre versiones';
	}
	
	function mostrar_observaciones()
	{
		$this->consola->mensaje("INVOCACION: toba conversion OPCION [-p proyecto] [-i instancia] [-v version]");
		$this->consola->enter();
		$this->get_info_parametro_proyecto();
		$this->get_info_parametro_instancia();
		$this->get_info_parametro_version();
		$this->consola->enter();
	}
		
	/**
	*	Muestra el contenido de una conversion puntual.  
	*/
	function opcion__info()
	{
		$version = $this->get_id_version_actual();
		$this->get_conversor()->ver_contenido_conversion( $version );
	}
	
	/**
	*	Simula la migración de metadatos de un proyecto hacia una versión.
	*/
	function opcion__probar()
	{
		$version = $this->get_id_version_actual();
		$proyecto = $this->get_id_proyecto_actual();
		$this->get_conversor()->procesar( $version, $proyecto, true );
	}

	/**
	*	Ejecuta la migración de metadatos de un proyecto hacia una versión. 
	*/
	function opcion__ejecutar()
	{
		$version = $this->get_id_version_actual();
		$proyecto = $this->get_id_proyecto_actual();
		$this->get_conversor()->procesar( $version, $proyecto );
	}
	
	/**
	*	Muestra la lista de conversiones que pueden aplicarse en un proyecto puntual. No utiliza el parametro [-v]
	*/
	function opcion__listar()
	{
		$conversor = $this->get_conversor();
		$proyecto = $this->get_id_proyecto_actual();
		$conversiones = $conversor->get_conversiones_posibles( $proyecto );
		$this->consola->lista( $conversiones, "Conversiones posibles PROYECTO '$proyecto'" );
	}

	//-----------------------------------------------------------
	// Acceso a los PARAMETROS
	//-----------------------------------------------------------
	
	/**
	*	Determina la VERSION de la conversion
	*/
	protected function get_id_version_actual()
	{
		$param = $this->get_parametros();
		if ( isset($param['-v']) &&  (trim($param['-v']) != '') ) {
			$version = $param['-v'];
			return $version;
		} else {
			throw new excepcion_toba("Es necesario indicar una version.");
		}
	}

	/**
	*	Describe el parametro VERSION
	*/
	protected function get_info_parametro_version()
	{
		$this->consola->mensaje("[ -v id_version ] Version del TOBA a la que se quiere migrar.");
	}
}
?>