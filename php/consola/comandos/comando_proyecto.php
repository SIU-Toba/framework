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
		$this->consola->mensaje("INVOCACION: toba proyecto OPCION [-p id_proyecto] [-i id_instancia]");
		$this->consola->enter();
		$this->get_info_parametro_proyecto();
		$this->get_info_parametro_instancia();
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
		$p = $this->get_proyecto();
		$p->exportar();
		$p->get_instancia()->exportar_local();
	}

	/**
	*	Importa los metadatos.
	*/
	function opcion__cargar()
	{
		$this->get_proyecto()->cargar_autonomo();
	}

	/**
	*	Elimina los metadatos.
	*/
	function opcion__eliminar()
	{
		if ( $this->consola->dialogo_simple("Desea eliminar el proyecto '"
				.$this->get_id_proyecto_actual()."' de la instancia '"
				.$this->get_id_instancia_actual()."'") ) {
			$this->get_proyecto()->eliminar();
		}
	}

	/**
	*	Compila los metadatos.
	*/
	function opcion__compilar()
	{
		$this->get_proyecto()->compilar();
	}

	/**
	*	Crea un proyecto nuevo. Falta Terminar
	*/
	function opcion__crear()
	{
		$id_proyecto = $this->get_id_proyecto_actual();
		$instancia = $this->get_instancia();
		$id_instancia = $instancia->get_id();
		$this->consola->mensaje( "Creando el proyecto '$id_proyecto' en la instancia '$id_instancia'" );
		proyecto::crear( $instancia, $id_proyecto );
		$proyecto = $this->get_proyecto();
		// Vinculo USUARIOS
		/*
		$this->consola->subtitulo( "Asociar USUARIOS" );
		$opcion[0] = "Asociar el usuario 'toba'";
		$opcion[1] = "Asociar TODOS los usuarios de la instancia";
		$opcion[2] = "Mostrar una lista de usuario y SELECCIONAR";
		$ok = $this->consola->dialogo_lista_opciones( $opcion, 'Asociar USUARIOS al proyecto. Seleccione una FORMA de CARGA', false );
		switch ( $ok ) {
			case 0:
				echo "TOBA";
				break;	
			case 1:
				echo "TODOS";
				break;	
			case 2:
				echo "ELEGIR";
				break;	
		}
		*/		
		$proyecto->vincular_usuario( 'toba' );
		$this->consola->mensaje( "Exportando metadatos iniciales" );
		$proyecto->exportar( false );
		// Exporto metadatos de la INSTANCIA. Como el archivo de PROYECTOS asociados ya se cargo,
		//	tengo que ejecutar la exportacion por fuera de este contexto de ejecucion
		//system("toba instancia ");
		$this->consola->mensaje( "El proyecto ha sido creado. Si se desea publicar el proyecto en el apache" .
									" para accederlo por http, agrege al 'httpd.conf' las directivas" .
									" explicitadas en el archiivo 'toba.conf' en el directorio raiz del proyecto." );
	}
}
?>