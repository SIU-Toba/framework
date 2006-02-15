<?
require_once('comando_toba.php');
/**
*	Publica los servicios de la clase PROYECTO a la consola toba
*/
class comando_proyecto extends comando_toba
{
	static function get_info()
	{
		return 'Administracion de los METADATOS de PROYECTOS';
	}

	function mostrar_observaciones()
	{
		$this->consola->mensaje("INVOCACION: toba proyecto 'opcion' [id_proyecto] [id_instancia]");
		$this->consola->enter();
		$this->consola->mensaje("[id_proyecto] Asume el valor de la variable de entorno 'toba_proyecto': ". $this->get_entorno_id_proyecto() );
		$this->consola->mensaje("[id_instancia] Asume el valor de la variable de entorno 'toba_instancia': ". $this->get_entorno_id_instancia() );
		$this->consola->enter();
	}

	//-------------------------------------------------------------
	// Opciones
	//-------------------------------------------------------------

	/**
	*	Brinda informacion sobre los metadatos.
	*/
	function opcion__info()
	{
		$datos = $this->get_proyecto()->info();
		$this->consola->tabla( $datos, array('tipo','componentes') ,'COMPONENTES' );
	}

	/**
	*	Exporta los metadatos.
	*/
	function opcion__exportar()
	{
		$this->get_proyecto()->exportar();
	}

	/**
	*	Importa los metadatos.
	*/
	function opcion__importar()
	{
		$this->get_proyecto()->importar_autonomo();
	}

	/**
	*	Elimina los metadatos.
	*/
	function opcion__eliminar()
	{
		$this->get_proyecto()->eliminar();
	}

	/**
	*	Compila los metadatos.
	*/
	function opcion__compilar()
	{
		$this->get_proyecto()->compilar();
	}

	//-------------------------------------------------------------
	// Primitivas internas
	//-------------------------------------------------------------

	/**
	*	Determina la instancia sobre la que se va a trabajar
	*/
	protected function get_id_instancia_actual()
	{
		if ( isset( $this->argumentos[2] ) ) {
			$id = $this->argumentos[2];
		} else {
			$id = $this->get_entorno_id_instancia( true );
		}
		return $id;
	}

	/**
	*	Determina el PROYECTO sobre el que se va a trabajar
	*/
	protected function get_id_proyecto_actual()
	{
		if ( isset( $this->argumentos[1] ) ) {
			$id = $this->argumentos[1];
		} else {
			$id = $this->get_entorno_id_proyecto( true );
		}
		return $id;
	}
}
?>