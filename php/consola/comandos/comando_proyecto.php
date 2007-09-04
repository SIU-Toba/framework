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
	* Crea un proyecto NUEVO.
	* @gtk_icono nucleo/agregar.gif 
	* @gtk_no_mostrar 1
	*/
	function opcion__crear()
	{
		$id_instancia = $this->get_id_instancia_actual();
		$id_proyecto = $this->get_id_proyecto_actual();
		$instancia = $this->get_instancia($id_instancia);
		
		// --  Creo el proyecto
		$this->consola->mensaje( "Creando el proyecto '$id_proyecto' en la instancia '$id_instancia'...", false );
		$usuarios = $this->seleccionar_usuarios( $instancia );
		toba_modelo_proyecto::crear( $instancia, $id_proyecto, $usuarios );
		$this->consola->progreso_fin();
		
		// -- Asigno un nuevo item de login
		$proyecto = $this->get_proyecto();		
		$proyecto->actualizar_login();
		
		// -- Exporto el proyecto creado
		$proyecto->exportar();
		$instancia->exportar_local();
		if (! $proyecto->esta_publicado()) {
			$this->consola->separador();
			$agregar = $this->consola->dialogo_simple("El proyecto ha sido creado. ¿Desea agregar el alias de apache al archivo toba.conf?", true);
			if ($agregar) {
				$proyecto->publicar();
				$this->consola->mensaje('OK. Debe reiniciar el servidor web para que los cambios tengan efecto');
			}
		}
	}	
	

	/**
	* Carga el PROYECTO en la INSTANCIA (Carga metadatos y crea un vinculo entre ambos elementos).
	* Opcionalmente crea el alias del proyecto
	* @gtk_icono importar.png
	* @gtk_no_mostrar 1
	*/
	function opcion__cargar()
	{
		$path = null;
		$id_proyecto = $this->get_id_proyecto_actual(false);
		if (!isset($id_proyecto)) {
			list($id_proyecto, $path) = $this->seleccionar_proyectos(false, false);
			if ($id_proyecto == $path) {
				$path=null;
			}
		}
		$p = $this->get_proyecto($id_proyecto);
		$i = $p->get_instancia();
		if ( ! $i->existen_metadatos_proyecto( $p->get_id() ) ) {

			//-- 1 -- Cargar proyecto
			$this->consola->enter();
			$this->consola->subtitulo("Carga del Proyecto ".$p->get_id());
			$i->vincular_proyecto( $p->get_id(), $path );
			$p->cargar_autonomo();
			$this->consola->mensaje("Vinculando usuarios", false);
			$usuarios = $this->seleccionar_usuarios( $p->get_instancia() );
			$grupo_acceso = $this->seleccionar_grupo_acceso( $p );
			foreach ( $usuarios as $usuario ) {
				$p->vincular_usuario( $usuario, $grupo_acceso );
				toba_logger::instancia()->debug("Vinculando USUARIO: $usuario, GRUPO ACCESO: $grupo_acceso");
				$this->consola->progreso_avanzar();
			}
			$this->consola->progreso_fin();
			
			//-- 2 -- Exportar proyecto
			$this->consola->enter();
			// Exporto la instancia con la nueva configuracion (por fuera del request)
			$i->exportar_local();
		} else {
			$this->consola->mensaje("El proyecto '" . $p->get_id() . "' ya EXISTE en la instancia '".$i->get_id()."'");
		}

		if (! $p->esta_publicado()) {
			//--- Generación del alias
			$this->consola->separador();
			$agregar = $this->consola->dialogo_simple("¿Desea agregar el alias de apache al archivo toba.conf?", true);
			if ($agregar) {
				$p->publicar();
				$this->consola->mensaje('OK. Debe reiniciar el servidor web para que los cambios tengan efecto');
			}		
		}
	}
	

	/**
	* Brinda informacion sobre los METADATOS del proyecto.
	* @gtk_icono info_chico.gif
	* @gtk_no_mostrar 1
	*/
	function opcion__info()
	{
		$p = $this->get_proyecto();
		$param = $this->get_parametros();
		$this->consola->titulo( "Informacion sobre el PROYECTO '" . $p->get_id() . "' en la INSTANCIA '" .  $p->get_instancia()->get_id() . "'");
		if ( isset( $param['-c'] ) ) {
			// COMPONENTES
			$this->consola->subtitulo('Listado de COMPONENTES');
			$this->consola->tabla( $p->get_resumen_componentes_utilizados() , array( 'Tipo', 'Cantidad') );
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
	 * Ejecuta el proceso de instalación propio del proyecto
	 * @gtk_icono instalacion.png
	 */
	function opcion__instalar()
	{
		$proyecto = $this->get_proyecto();
		$proyecto->instalar();		
	}	
	
	/**
	* Exporta los METADATOS del proyecto.
	* @gtk_icono exportar.png 
	*/
	function opcion__exportar()
	{
		$p = $this->get_proyecto();
		$p->exportar();
		$p->get_instancia()->exportar_local();
	}

	/**
	* Elimina los METADATOS del proyecto y los vuelve a cargar.
	* @gtk_icono importar.png 
	*/
	function opcion__regenerar()
	{
		$this->get_proyecto()->regenerar();
	}

	/**
	* Elimina el PROYECTO de la INSTANCIA (Elimina los metadatos y el vinculo entre ambos elementos).
	* @gtk_icono borrar.png
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
	 * Exporta los METADATOS y luego actualiza el proyecto (usando svn)
	 * @gtk_icono refrescar.png
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
	* Compila los METADATOS del proyecto.
	* @gtk_icono compilar.png 
	*/
	function opcion__compilar()
	{
		$this->get_proyecto()->compilar();
	}
	

	/**
	* Incluye al proyecto dentro del archivo de configuración de apache (toba.conf)
	* @consola_parametros Opcional: [-u 'url'] Lo publica en una url específica (por ej. /mi_proyecto )
	*/
	function opcion__publicar()
	{
		$param = $this->get_parametros();
		$url = '';
		if (isset($param['-u'])) {
			$url = $param['-u'];
		}

		if (! $this->get_proyecto()->esta_publicado()) {
			$this->get_proyecto()->publicar($url);
			$this->consola->mensaje('OK. Debe reiniciar el servidor web para que los cambios tengan efecto');
		} else {
			throw new toba_error("El proyecto ya se encuentra publicado. Debe despublicarlo primero");
		}
	}	
	
	/**
	* Quita al proyecto del archivo de configuración de apache (toba.conf)
	*/
	function opcion__despublicar()
	{
		if ($this->get_proyecto()->esta_publicado()) {
			$this->get_proyecto()->despublicar();
			$this->consola->mensaje('OK. Debe reiniciar el servidor web para que los cambios tengan efecto');
		} else {
			throw new toba_error("El proyecto no se encuentra actualmente publicado.");
		}
	}		
	
	
	/**
	 * Actualiza o crea la operación de login asociada al proyecto
	 * @gtk_icono usuarios/usuario.gif
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
	
	/**
	 * Migra un proyecto entre dos versiones toba.
	 * @consola_parametros Opcionales: [-d 'desde']  [-h 'hasta'] [-R 0|1]
	 * @gtk_icono convertir.png 
	 */
	function opcion__migrar()
	{
		$proyecto = $this->get_proyecto();
		//--- Parametros
		$param = $this->get_parametros();
		$desde = isset($param['-d']) ? new toba_version($param['-d']) : $proyecto->get_version_actual();
		$hasta = isset($param['-h']) ? new toba_version($param['-h']) : toba_modelo_instalacion::get_version_actual();

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
}
?>