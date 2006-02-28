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
		$this->consola->mensaje("INVOCACION: toba instancia OPCION [-i id_instancia]");
		$this->consola->enter();
		$this->get_info_parametro_instancia();
		$this->consola->enter();
	}

	//-------------------------------------------------------------
	// Opciones
	//-------------------------------------------------------------

	/**
	*	Brinda informacion sobre la instancia
	*/
	function opcion__info()
	{
		$i = $this->get_instancia();
		$param = $this->get_parametros();
		if ( isset( $param['-u'] ) ) {
			// Lista de USUARIOS
			$this->consola->titulo( 'Lista de Usuarios - (INSTANCIA: ' . $i->get_id() .')' );
			$this->consola->tabla( $i->get_lista_usuarios(), array( 'Usuario', 'Nombre') );
		} else {										
			// Informacion BASICA
			$this->consola->titulo( 'Informacion basica - (INSTANCIA: ' . $i->get_id() .')' );
			$this->consola->lista_asociativa( $i->get_parametros_db() , array('Parametros BASE', 'Valores') );
			$this->consola->lista( $i->get_lista_proyectos(), 'PROYECTOS' );
		}
	}
	
	/**
	*	Carga una INSTANCIA en una base de datos, partiendo del contenido del sistema de archivos
	*/
	function opcion__cargar()
	{
		try {
			$this->get_instancia()->cargar();
		} catch ( excepcion_toba_modelo_preexiste $e ) {
			$this->consola->error( 'Ya existe una instancia en la base de datos' );
			$this->consola->dump_arbol( $this->get_instancia()->get_parametros_db(), 'BASE' );
			if ( $this->consola->dialogo_simple('Desea ELIMINAR la instancia y luego CARGARLA?') ) {
				$this->get_instancia()->cargar( true );
			}
		} catch ( excepcion_toba $e ) {
			$this->consola->error( 'Ha ocurrido un error durante la importacion de la instancia.' );
			$this->consola->error( $e->getMessage() );
		}
	}

	/**
	*	Elimina una INSTANCIA y la vuelve a generar.
	*/
	function opcion__regenerar()
	{
		$this->opcion__eliminar();
		$this->get_instancia()->cargar();
	}

	/**
	*	Exporta una instancia completa ( Metadatos propios y metadatos de proyectos )
	*/
	function opcion__exportar()
	{
		$this->get_instancia()->exportar();
	}

	/**
	*	Exporta los METADATOS propios de la instancia ( Exclusivamente info local )
	*/
	function opcion__exportar_local()
	{
		$this->get_instancia()->exportar_local();
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
	*	Crea una instancia nueva.
	*/
	function opcion__crear()
	{
		$id_instancia = $this->get_id_instancia_actual();
		$opciones_base = array_keys( instalacion::get_lista_bases() );
		$texto = 'Seleccione una BASE para la instancia';
		$base = $this->consola->dialogo_lista_opciones( $opciones_base, $texto, false, array('ID','BASE') );
		var_dump( $base );
		/*
		$instalacion = $this->get_instalacion();
		$instalacion->crear_instancia(  );
		$instancia = $this->get_instancia();
		$instancia->iniciar_instancia();
		*/
	}

	/**
	*	Genera un archivo con la lista de registros por cada tabla de la instancia
	*/	
	function opcion__dump_info_tablas()
	{
		$this->get_instancia()->dump_info_tablas();
	}
}
?>