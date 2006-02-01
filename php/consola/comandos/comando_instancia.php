<?
require_once('comando_toba.php');
require_once('modelo/instancia.php');

/**
*	Publica los servicios de la clase INSTANCIA a la consola toba
*/
class comando_instancia extends comando_toba
{
	static function get_info()
	{
		return 'Administracion de INSTANCIAS.';
	}

	function mostrar_observaciones()
	{
		$this->manejador_interface->mensaje("INVOCACION: toba instancia 'opcion' [id_instancia]");
		$this->manejador_interface->enter();
		$this->manejador_interface->mensaje("Si no se indica [id_instancia] se utiliza la variable de entorno 'toba_instancia' ( valor actual: '". $this->get_entorno_id_instancia(). "' ) " );
		$this->manejador_interface->enter();
	}

	/**
	*	Determina la instancia sobre la que se va a trabajar
	*/
	private function get_id_instancia_actual()
	{
		if ( isset( $this->argumentos[1] ) ) {
			$id = $this->argumentos[1];
		} else {
			$id = $this->get_entorno_id_instancia();
		}
		return $id;
	}
		
	/**
	*	Devuelve una referencia a la INSTANCIA
	*/
	private function get_elemento()
	{
		$instancia = new instancia(	$this->get_dir_raiz(),
									$this->get_id_instancia_actual() );
		$instancia->set_manejador_interface( $this->manejador_interface );
		return $instancia;
	}

	//-------------------------------------------------------------
	// Opciones
	//-------------------------------------------------------------
	
	/**
	*	Informacion basica de la instancia
	*/
	function opcion__info()
	{
		$this->manejador_interface( $this->get_elemento()->info() );
	}
	
	/**
	*	Exporta la informacion de la instancia
	*/
	function opcion__exportar()
	{
		$this->get_elemento()->exportar();
	}

	/**
	*	Inicializa una instancia (INC.)
	*/
	function opcion__iniciar()
	{
	}
	
	/**
	*	Agrega un proyecto en la instancia (INC.)
	*/
	function opcion__agregar_proyecto()
	{
	}
	
	/**
	*	Elimina un proyecto de la instancia (INC.)
	*/
	function opcion__eliminar_proyecto()
	{
	}
}
?>