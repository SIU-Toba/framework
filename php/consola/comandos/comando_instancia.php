<?
require_once('comando.php');
require_once('modelo/instancia.php');

/**
*	Publica los servicios de la clase instancia a la consola
*/
class comando_instancia extends comando_toba
{
	static function get_info()
	{
		return 'Administracion de INSTANCIAS.';
	}

	private function get_id_instancia_actual()
	{
		if ( isset( $this->argumentos[1] ) ) {
			$id = $this->argumentos[1];
		} else {
			$id = $this->consola->get_instancia();
		}
		return $id;
	}
		
	private function get_elemento()
	{
		$instancia = new instancia(	$this->consola->get_dir_raiz(),
									$this->get_id_instancia_actual() );
		$instancia->set_manejador_interface( $this->consola );
		return $instancia;
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