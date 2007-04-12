<?php
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
	 * Exporta los METADATOS y luego actualiza el proyecto (usando svn)
	 */
	function opcion__actualizar()
	{
		$this->consola->titulo("1.- Exportando METADATOS");		
		$this->opcion__exportar();

		$this->consola->titulo("2.- Actualizando el proyecto utilizando SVN");
		$p = $this->get_proyecto();		
		$p->actualizar();		
	}
	
	/**
	*	Elimina los METADATOS del proyecto y los vuelve a cargar.
	*/
	function opcion__regenerar()
	{
		$this->get_proyecto()->regenerar();
	}

	/**
	*	Carga el PROYECTO en la INSTANCIA (Carga metadatos y crea un vinculo entre ambos elementos).
	* 	Opcionalmente crea el alias del proyecto
	*/
	function opcion__cargar()
	{
		$p = $this->get_proyecto();
		$i = $p->get_instancia();
		if ( ! $i->existen_metadatos_proyecto( $p->get_id() ) ) {

			//-- 1 -- Cargar proyecto
			$this->consola->enter();
			$this->consola->subtitulo("Carga del Proyecto ".$p->get_id());
			$i->vincular_proyecto( $p->get_id() );
			$p->cargar_autonomo();
			$this->consola->mensaje("Vinculando usuarios", false);
			$usuarios = $this->seleccionar_usuarios( $p->get_instancia() );
			$grupo_acceso = $this->seleccionar_grupo_acceso( $p );
			foreach ( $usuarios as $usuario ) {
				$p->vincular_usuario( $usuario, $grupo_acceso );
				toba_logger::instancia()->debug("Vinculando USUARIO: $usuario, GRUPO ACCESO: $grupo_acceso");
				$this->consola->mensaje_directo('.');
			}
			$this->consola->mensaje("OK");
			
			//-- 2 -- Exportar proyecto
			$this->consola->enter();
			// Exporto la instancia con la nueva configuracion (por fuera del request)
			$i->exportar_local();
		} else {
			$this->consola->mensaje("El proyecto '" . $p->get_id() . "' ya EXISTE en la instancia '".$i->get_id()."'");
		}

		//--- Generación del alias
		$this->consola->separador();
		$agregar = $this->consola->dialogo_simple("¿Desea agregar el alias de apache al archivo toba.conf?", true);
		if ($agregar) {
			instalacion::agregar_alias_apache($p->get_alias(), $p->get_dir(), $p->get_instancia()->get_id());
			$this->consola->separador();
			$this->consola->mensaje("OK. Para poder acceder via Web, recuerde chequear que el archivo '".instalacion::get_archivo_alias_apache().
									"' se encuentre incluído en la configuración de apache (con un include explícito en httpd.conf o un link simbolico en la carpeta sites-enabled)");
			$this->consola->separador();									
		}		
	}

	/**
	*	Elimina el PROYECTO de la INSTANCIA (Elimina los metadatos y el vinculo entre ambos elementos).
	*/
	function opcion__eliminar()
	{
		$id_proyecto = $this->get_id_proyecto_actual();
		if ( $id_proyecto == 'toba' ) {
			throw new toba_error("No es posible eliminar el proyecto 'toba'");
		}	
		$p = $this->get_proyecto();
		if ( $this->consola->dialogo_simple("Desea ELIMINAR los metadatos y DESVINCULAR el proyecto '"
				.$id_proyecto."' de la instancia '"
				.$this->get_id_instancia_actual()."'") ) {
			$p->eliminar_autonomo();
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

		// --  Creo el proyecto
		$this->consola->mensaje( "Creando el proyecto '$id_proyecto' en la instancia '$id_instancia'...", false );
		$usuarios = $this->seleccionar_usuarios( $instancia );
		proyecto::crear( $instancia, $id_proyecto, $usuarios );
		$this->consola->mensaje( "OK" );
		
		// -- Asigno un nuevo item de login
		$proyecto = $this->get_proyecto();		
		$proyecto->actualizar_login();
		
		// -- Exporto el proyecto creado
		$proyecto->exportar();
		$instancia->exportar_local();
		$this->consola->separador();
		$agregar = $this->consola->dialogo_simple("El proyecto ha sido creado. ¿Desea agregar el alias de apache al archivo toba.conf?", true);
		if ($agregar) {
			instalacion::agregar_alias_apache($proyecto->get_alias(), $proyecto->get_dir(), $proyecto->get_instancia()->get_id());
			$this->consola->separador();
			$this->consola->mensaje("OK. Para poder acceder via Web, recuerde chequear que el archivo '".instalacion::get_archivo_alias_apache().
									"' se encuentre incluído en la configuración de apache (con un include explícito en httpd.conf o un link simbolico en la carpeta sites-enabled)");
			$this->consola->separador();									
		}
		
	}

	/**
	*	Compila los METADATOS del proyecto.
	*/
	function opcion__compilar()
	{
		$this->get_proyecto()->compilar();
	}
	
	/**
	 * Migra un proyecto entre dos versiones toba. [-d 'desde']  [-h 'hasta'] [-m 'metodo']
	 * -d se asume la versión de toba que posee actualmente el proyecto
	 * -h se asume la versión de toba que posee actualmente la instalacion
	 * -m ejecuta solo un metodo de la migración (incluir nombre completo)
	 */
	function opcion__migrar()
	{
		$proyecto = $this->get_proyecto();
		//--- Parametros
		$param = $this->get_parametros();
		$desde = isset($param['-d']) ? new version_toba($param['-d']) : $proyecto->get_version_actual();
		$hasta = isset($param['-h']) ? new version_toba($param['-h']) : instalacion::get_version_actual();

		$desde_texto = $desde->__toString();
		$hasta_texto = $hasta->__toString();
		$this->consola->titulo("Migración el proyecto '{$proyecto->get_id()}'"." desde la versión $desde_texto hacia la $hasta_texto.");

		if (! isset($param['-m'])) {
			$versiones = $desde->get_secuencia_migraciones($hasta);
			if (empty($versiones)) {
				$this->consola->mensaje("No es necesario ejecutar una migración entre estas versiones para el proyecto '{$proyecto->get_id()}'");
				return ;
			}
			$proyecto->migrar_rango_versiones($desde, $hasta, false);
		} else {
			//Se pidio un método puntual
			$proyecto->ejecutar_migracion_particular($hasta, trim($param['-m']));
		}
	}	
	
	/**
	 * Ejecuta el proceso de instalación propio del proyecto
	 */
	function opcion__instalar()
	{
		$proyecto = $this->get_proyecto();
		$proyecto->instalar();		
	}
	
	/**
	 * Actualiza o crea el item de login asociado al proyecto
	 */
	function opcion__actualizar_login()
	{
		$proyecto = $this->get_proyecto();
	
		//--- Existe un item de login??
		$pisar = false;
		if ($proyecto->get_item_login()) {
			$clonar = $this->consola->dialogo_simple("El proyecto ya posee un item de login propio, ¿desea continuar?", true);
			if (!$clonar) {
				return;
			}
			$pisar = $this->consola->dialogo_simple("¿Desea borrar del proyecto el item de login anterior?", false);
		}
		$proyecto->actualizar_login($pisar);
	}
}
?>