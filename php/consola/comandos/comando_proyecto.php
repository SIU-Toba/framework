<?
require_once('comando_toba.php');
/**
*	Publica los servicios de la clase PROYECTO a la consola toba
*
*	@todo	La asociacion de usuarios al proyecto nuevo tiene que ofrecer una seleccion
*	
*/
class comando_proyecto extends comando_toba
{
	static function get_info()
	{
		return 'Administracion de PROYECTOS';
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
	*	Brinda informacion sobre los METADATOS del proyecto.
	*/
	function opcion__info()
	{
		$p = $this->get_proyecto();
		$param = $this->get_parametros();
		$this->consola->titulo( "Informacion sobre el PROYECTO '" . $p->get_id() . "' en la INSTANCIA '" .  $p->get_instancia()->get_id() . "'");
		if ( isset( $param['-c'] ) ) {
			// COMPONENTES
			$this->consola->subtitulo('Listado de COMPONENTES');
			$this->consola->tabla( $p->get_lista_componentes() , array( 'Tipo', 'Cantidad') );
		} elseif ( isset( $param['-g'] ) ) {
			// GRUPOS de ACCESO
			$this->consola->subtitulo('Listado de GRUPOS de ACCESO');
			$this->consola->tabla( $p->get_lista_grupos_acceso() , array( 'ID', 'Nombre') );
		} else {										
			$this->consola->subtitulo('Reportes');
			$subopciones = array( 	'-c' => 'Listado de COMPONENTES',
									'-g' => 'Listado de GRUPOS de ACCESO' ) ;
			$this->consola->coleccion( $subopciones );			
		}
	}

	/**
	*	Exporta los METADATOS del proyecto.
	*/
	function opcion__exportar()
	{
		$p = $this->get_proyecto();
		$p->exportar();
		$p->get_instancia()->exportar_local();
	}

	/**
	*	Regenera los METADATOS del proyecto.
	*/
	function opcion__regenerar()
	{
		$p = $this->get_proyecto();
		$this->consola->enter();
		$this->consola->subtitulo("Eliminar proyecto");
		$p->eliminar();
		$this->consola->enter();
		$this->consola->subtitulo("Cargar proyecto");
		$p->cargar_autonomo();
		$p->get_instancia()->cargar_informacion_instancia_proyecto( $p->get_id() );
	}

	/**
	*	Carga el PROYECTO en la INSTANCIA (Carga metadatos y crea un vinculo entre ambos elementos).
	*/
	function opcion__cargar()
	{
		$p = $this->get_proyecto();
		$i = $p->get_instancia();
		if ( ! $i->existen_metadatos_proyecto( $p->get_id() ) ) {

			//-- 1 -- Cargar proyecto
			$this->consola->enter();
			$this->consola->subtitulo("Cargar METADATOS");
			$i->vincular_proyecto( $p->get_id() );
			$p->cargar_autonomo( false );
			$this->consola->enter();
			$this->consola->subtitulo("Vincular USUARIOS");
			$usuarios = $this->seleccionar_usuarios( $p->get_instancia() );
			$grupo_acceso = $this->seleccionar_grupo_acceso( $p );
			foreach ( $usuarios as $usuario ) {
				$p->vincular_usuario( $usuario, $grupo_acceso );
				$this->consola->mensaje("USUARIO: $usuario, GRUPO ACCESO: $grupo_acceso");
			}

			//-- 2 -- Exportar proyecto
			$this->consola->enter();
			$this->consola->subtitulo("Exportar datos");
			// Exporto la instancia con la nueva configuracion (por fuera del request)
			self::exportar_instancia_actual();
		} else {
			$this->consola->mensaje("El proyecto '" . $p->get_id() . "' ya EXISTE en la instancia '".$i->get_id()."'");	
		}
	}

	/**
	*	Elimina el PROYECTO de la INSTANCIA (Elimina los metadatos y el vinculo entre ambos elementos).
	*/
	function opcion__eliminar()
	{
		$p = $this->get_proyecto();
		if ( $this->consola->dialogo_simple("Desea ELIMINAR los metadatos y DESVINCULAR el proyecto '"
				.$this->get_id_proyecto_actual()."' de la instancia '"
				.$this->get_id_instancia_actual()."'") ) {
			$p->eliminar();
		}
		$p->get_instancia()->desvincular_proyecto( $p->get_id() );
	}
	
	/**
	*	Crea un proyecto NUEVO.
	*/
	function opcion__crear()
	{
		$id_proyecto = $this->get_id_proyecto_actual();
		$instancia = $this->get_instancia();
		$id_instancia = $instancia->get_id();

		// -- 1 -- Creo el proyecto
		$this->consola->mensaje( "Creando el proyecto '$id_proyecto' en la instancia '$id_instancia'" );
		$usuarios = $this->seleccionar_usuarios( $instancia );
		proyecto::crear( $instancia, $id_proyecto, $usuarios );

		// -- 2 -- Exporto el proyecto creado
		$this->consola->mensaje( "Exportando metadatos iniciales" );
		$proyecto = $this->get_proyecto();
		$proyecto->exportar( false );
		// Exporto la instancia con la nueva configuracion (por fuera del request)
		self::exportar_instancia_actual();
		$this->consola->separador();
		$this->consola->mensaje( "El proyecto ha sido creado. Si se desea crear un acceso WEB al mismo" .
									" agrege al archivo de configuracion de apache ('httpd.conf') las directivas" .
									" existentes en el archivo '".toba_dir()."/proyectos/$id_proyecto/toba.conf'" );
		$this->consola->separador();
	}

	/**
	*	Compila los METADATOS del proyecto.
	*/
	function opcion__compilar()
	{
		$this->get_proyecto()->compilar();
	}
}
?>