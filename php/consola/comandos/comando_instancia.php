<?
require_once('comando_toba.php');
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
		$this->consola->mensaje("INVOCACION: toba instancia 'opcion' [id_instancia]");
		$this->consola->enter();
		$this->consola->mensaje("Si no se indica [id_instancia] se utiliza la variable de entorno 'toba_instancia' ( valor actual: '". $this->get_entorno_id_instancia(). "' ) " );
		$this->consola->enter();
	}

	//-------------------------------------------------------------
	// Opciones
	//-------------------------------------------------------------

	/**
	*	Informacion basica de la instancia
	*/
	function opcion__info()
	{
		$i = $this->get_instancia();
		$this->consola->titulo( 'INSTANCIA: ' . $i->get_id() );
		$this->consola->dump_arbol( $i->get_lista_proyectos(), 'PROYECTOS' );
		$this->consola->dump_arbol( $i->get_parametros_db(), 'BASE' );
	}
	
	/**
	*	Exporta la informacion de la instancia
	*/
	function opcion__exportar()
	{
		$this->get_instancia()->exportar();
	}

	/**
	*	Exporta la informacion COMPLETA de la instancia (incluyendo proyectos) [NO]
	*/
	function opcion__exportar_full()
	{
		$this->get_instancia()->exportar_full();
	}

	/**
	*	Inicializa una instancia [NO]
	*/
	function opcion__importar()
	{
		$this->get_instancia()->importar();
	}

	/**
	*	Elimina todas las tablas correspondientes al toba [NO]
	*/
	function opcion__regenerar()
	{
		$this->opcion__eliminar();
		$this->get_instancia()->importar();
	}

	/**
	*	Crea una instancia nueva.
	*/
	function opcion__crear()
	{
		$instalacion = $this->get_instalacion();
		$instalacion->crear_instancia( $this->get_id_instancia_actual() );
		$instancia = $this->get_instancia();
		$instancia->iniciar_instancia();
	}

	/**
	*	Elimina todas las tablas correspondientes al toba [NO]
	*/
	function opcion__eliminar_tablas()
	{
		$this->get_instancia()->eliminar_tablas();
	}

	/**
	*	Elimina todas las tablas correspondientes al toba [NO]
	*/
	function opcion__eliminar()
	{
		$i = $this->get_instancia();
		$this->consola->dump_arbol( $i->get_parametros_db(), 'BASE' );
		if ( $this->consola->dialogo_simple('Desea eliminar la base de datos?') ) {
			$i->eliminar();
		}
	}
	
	//-------------------------------------------------------------
	// Primitivas internas
	//-------------------------------------------------------------

	/**
	*	Determina la instancia sobre la que se va a trabajar
	*/
	protected function get_id_instancia_actual()
	{
		if ( isset( $this->argumentos[1] ) ) {
			$id = $this->argumentos[1];
		} else {
			$id = $this->get_entorno_id_instancia();
		}
		return $id;
	}
}
?>