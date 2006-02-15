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
		$this->consola->mensaje("[id_instancia] Asume por defecto el valor de la variable de entorno 'toba_instancia': ". $this->get_entorno_id_instancia() );
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
		//$this->consola->dump_arbol( $i->get_registros_tabla(), 'BASE' );
	}
	
	/**
	*	Exporta la informacion de la instancia
	*/
	function opcion__exportar()
	{
		$this->get_instancia()->exportar();
	}

	/**
	*	Exporta la informacion COMPLETA de la instancia (incluyendo proyectos)
	*/
	function opcion__exportar_full()
	{
		$this->get_instancia()->exportar_full();
	}

	/**
	*	Inicializa una instancia
	*/
	function opcion__importar()
	{
		try {
			$this->get_instancia()->importar();
		} catch ( excepcion_toba_modelo_preexiste $e ) {
			$this->consola->error( 'Ya existe una instancia en la base de datos' );
			$this->consola->dump_arbol( $this->get_instancia()->get_parametros_db(), 'BASE' );
			if ( $this->consola->dialogo_simple('Desea ELIMINAR la instancia y luego IMPORTARLA?') ) {
				$this->get_instancia()->importar( true );
			}
		} catch ( excepcion_toba $e ) {
			$this->consola->error( 'Ha ocurrido un error durante la importacion de la instancia.' );
			$this->consola->error( $e->getMessage() );
		}
	}

	/**
	*	Regenera el TOBA de una instancia
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
	*	Elimina la instancia
	*/
	function opcion__eliminar()
	{
		$i = $this->get_instancia();
		$this->consola->dump_arbol( $i->get_parametros_db(), 'BASE' );
		if ( $this->consola->dialogo_simple('Desea eliminar la INSTANCIA?') ) {
			$i->eliminar_base();
		}
	}

	/**
	*	Genera un archivo con la lista de registros por cada tabla de la instancia
	*/	
	function opcion__dump_info_tablas()
	{
		$this->get_instancia()->dump_info_tablas();
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