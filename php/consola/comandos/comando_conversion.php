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
		$this->consola->mensaje("INVOCACION: toba conversion opcion [-p proyecto] [-i instancia] [-v version]");
		$this->consola->enter();
		$this->consola->mensaje("Si no se indica '-i' se utiliza la variable de entorno 'toba_instancia': ". $this->get_entorno_id_instancia() );
		$this->consola->mensaje("Si no se indica '-p' se utiliza la variable de entorno 'toba_proyecto': ". $this->get_entorno_id_proyecto() );
		$this->consola->enter();
	}
		
	/**
	*	Muestra la lista de conversiones que pueden aplicarse en un proyecto puntual.
	*/
	function opcion__listar()
	{
		$conversor = $this->get_conversor();
		$proyecto = $this->get_id_proyecto_actual();
		$conversiones = $conversor->get_conversiones_posibles( $proyecto );
		$this->consola->lista( $conversiones, "Conversiones posibles PROYECTO '$proyecto'" );
	}

	/**
	*	Muestra el contenido de una conversion puntual. (Utiliza -v)
	*/
	function opcion__info()
	{
		$version = $this->get_id_version_actual();
		$this->get_conversor()->ver_contenido_conversion( $version );
	}
	
	/**
	*	Simula la migración de metadatos de un proyecto hacia una versión
	*/
	function opcion__probar()
	{
		$version = $this->get_id_version_actual();
		$proyecto = $this->get_id_proyecto_actual();
		$this->get_conversor()->procesar( $version, $proyecto, true );
	}

	/**
	*	Ejecuta la migración de metadatos de un proyecto hacia una versión
	*/
	function opcion__ejecutar()
	{
		$version = $this->get_id_version_actual();
		$proyecto = $this->get_id_proyecto_actual();
		$this->get_conversor()->procesar( $version, $proyecto );
	}
	
	//-------------------------------------------------------------
	// Primitivas internas
	//-------------------------------------------------------------

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
			throw new excepcion_toba("Es necesario indicar una version");
		}
	}

	/**
	*	Determina la INSTANCIA sobre la que se va a trabajar
	*/
	protected function get_id_instancia_actual()
	{
		$param = $this->get_parametros();
		if ( isset($param['-i']) &&  (trim($param['-i']) != '') ) {
			$id = $param['-i'];
		} else {
			try {
				$id = $this->get_entorno_id_instancia( true );
			} catch ( excepcion_toba $e ) {
				throw new excepcion_toba("Es necesario definir una instancia de trabajo. Utilice el modificador '-i'." . $e->getMessage() );	
			}
		}
		return $id;
	}

	/**
	*	Determina el PROYECTO sobre el que se va a trabajar
	*/
	protected function get_id_proyecto_actual()
	{
		$param = $this->get_parametros();
		if ( isset($param['-p']) &&  (trim($param['-p']) != '') ) {
			$id = $param['-p'];
		} else {
			try {
				$id = $this->get_entorno_id_proyecto( true );
			} catch ( excepcion_toba $e ) {
				throw new excepcion_toba("Es necesario definir un proyecto. Utilice el modificador '-p'." . $e->getMessage() );	
			}
		}
		return $id;
	}
}
?>