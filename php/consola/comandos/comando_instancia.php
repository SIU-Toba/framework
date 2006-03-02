<?
require_once('comando_toba.php');
/**
*	Publica los servicios de la clase INSTANCIA a la consola toba
*/
class comando_instancia extends comando_toba
{
	static function get_info()
	{
		return 'Administracion de INSTANCIAS';
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
	*	Brinda informacion sobre la instancia.
	*/
	function opcion__info()
	{
		$i = $this->get_instancia();
		$param = $this->get_parametros();
		$this->consola->titulo( 'INSTANCIA: ' . $i->get_id() );
		if ( isset( $param['-u'] ) ) {
			// Lista de USUARIOS
			$this->consola->subtitulo('Listado de USUARIOS');
			$this->consola->tabla( $i->get_lista_usuarios(), array( 'Usuario', 'Nombre') );
		} else {										
			// Informacion BASICA
			$this->consola->subtitulo('Informacion BASICA');
			$this->consola->lista_asociativa( $i->get_parametros_db() , array('Parametros Conexion', 'Valores') );
			$this->consola->lista( $i->get_lista_proyectos_vinculados(), 'Proyectos Vinculados' );
			$this->consola->enter();
			$this->consola->subtitulo('Reportes');
			$subopciones = array( '-u' => 'Listado de usuarios' ) ;
			$this->consola->coleccion( $subopciones );			
		}
	}
	
	/**
	*	Exporta la instancia completa de la DB referenciada (METADATOS propios y METADATOS de proyectos contenidos).
	*/
	function opcion__exportar()
	{
		$this->get_instancia()->exportar();
	}

	/**
	*	Exporta los METADATOS propios de la instancia de la DB (exclusivamente la informacion local).
	*/
	function opcion__exportar_local()
	{
		$this->get_instancia()->exportar_local();
	}

	/**
	*	Elimina la instancia y la vuelve a cargar.
	*/
	function opcion__regenerar()
	{
		$this->opcion__eliminar();
		$this->get_instancia()->cargar();
	}

	/**
	*	Carga una instancia en la DB referenciada, partiendo de los METADATOS existentes en el sistema de archivos.
	*/
	function opcion__cargar()
	{
		try {
			$this->get_instancia()->cargar();
		} catch ( excepcion_toba_modelo_preexiste $e ) {
			$this->consola->error( 'Ya existe una instancia en la base de datos' );
			$this->consola->lista( $this->get_instancia()->get_parametros_db(), 'BASE' );
			if ( $this->consola->dialogo_simple('Desea ELIMINAR la instancia y luego CARGARLA?') ) {
				$this->get_instancia()->cargar( true );
			}
		} catch ( excepcion_toba $e ) {
			$this->consola->error( 'Ha ocurrido un error durante la importacion de la instancia.' );
			$this->consola->error( $e->getMessage() );
		}
	}

	/**
	*	Elimina la instancia.
	*/
	function opcion__eliminar()
	{
		$i = $this->get_instancia();
		$this->consola->lista( $i->get_parametros_db(), 'BASE' );
		if ( $this->consola->dialogo_simple('Desea eliminar la INSTANCIA?') ) {
			$i->eliminar_base();
		}
	}

	/**
	*	Crea una instancia NUEVA.
	*/
	function opcion__crear()
	{
		$id_instancia = $this->get_id_instancia_actual();
		$instalacion = $this->get_instalacion();
		$this->consola->titulo("Creando la INSTANCIA: $id_instancia ");
		if ( $instalacion->hay_bases() ) {

			//---- A: Creo la definicion de la instancia
			$proyectos = $this->seleccionar_proyectos();
			// En el esquema actual, el toba siempre tiene que estar.
			if ( ! in_array( 'toba', $proyectos ) ) {
				$proyectos[] = 'toba';
			}
			$this->consola->enter();
			$base = $this->seleccionar_base();
			$instalacion->crear_instancia( $id_instancia, $base, $proyectos );

			//---- B: Cargo la INSTANCIA en la BASE
			$instancia = $this->get_instancia();
			try {
				$instancia->cargar();
			} catch ( excepcion_toba_modelo_preexiste $e ) {
				$this->consola->error( 'ATENCION: Ya existe una instancia en la base de datos seleccionada' );
				$this->consola->lista( $instancia->get_parametros_db(), 'BASE' );
				if ( $this->consola->dialogo_simple('Desea ELIMINAR la instancia y luego CARGARLA (La informacion local previa se perdera!)?') ) {
					$instancia->cargar( true );
				} else {
					return;	
				}
			} catch ( excepcion_toba $e ) {
				$this->consola->error( 'Ha ocurrido un error durante la importacion de la instancia.' );
				$this->consola->error( $e->getMessage() );
			}

			//---- C: Creo un USUARIO y lo asigno a los proyectos
			$datos = $this->definir_usuario( "Crear USUARIO" );
			//print_r( $datos );
			$instancia->agregar_usuario( $datos['usuario'], $datos['nombre'], $datos['clave'] );
			foreach( $instancia->get_proyectos() as $proyecto ) {
				$grupo_acceso = $this->seleccionar_grupo_acceso( $proyecto );
				$proyecto->vincular_usuario( $datos['usuario'], $grupo_acceso );
			}

			//---- D: Exporto la informacion LOCAL
			$instancia->exportar_local();

		} else {
			$this->consola->mensaje("Para crear una INSTANCIA, es necesario definir al menos una BASE. Utilice el comando 'toba instalacion agregar_db'");
		}
	}
		
	/**
	*	Genera un archivo con la lista de registros por cada tabla de la instancia
	function opcion__dump_info_tablas()
	{
		$this->get_instancia()->dump_info_tablas();
	}
	*/	
}
?>