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

	function get_info_extra()
	{
		try {
			$proyecto = $this->get_proyecto();
			$salida = "Path: ".$proyecto->get_dir();
			$version = $proyecto->get_version_proyecto();
			if (isset($version)) {
				$salida .= "\nVersión: ".$version->__toString();
			}
			$url = $proyecto->get_url();
			if (isset($url)) {
				$salida .= "\nURL: ".$url;
			}			
			return $salida;
		} catch (toba_error $e) {
			//El proyecto puede no existir
		}
	}	

	function inspeccionar_opciones($clase = null)
	{
		$opciones = array();
		$basicas = parent::inspeccionar_opciones($clase);
		$id_proyecto = $this->get_id_proyecto_actual(false);
		$id_instancia = $this->get_id_instancia_actual(false);
		if (isset($id_proyecto) && isset($id_instancia)) {
			try {
				$proyecto = $this->get_proyecto();
				$clase = $proyecto->get_aplicacion_comando();
				if (isset($clase)) {
					$opciones = parent::inspeccionar_opciones($clase);
				}
			} catch (toba_error $e) {
				
			}
		}
		return $basicas + $opciones;	
	}	
	
	protected function ejecutar_opcion($opcion, $argumentos)
	{
		$id_proyecto = $this->get_id_proyecto_actual(false);
		$id_instancia = $this->get_id_instancia_actual(false);
		$clase = null;
		if (isset($id_proyecto) && isset($id_instancia)) {
			try {
				$proyecto = $this->get_proyecto();
				$clase = $proyecto->get_aplicacion_comando();
			} catch (toba_error_db $d) {
				$this->consola->mensaje('Existe un problema con la base de datos, por favor verifique los logs');
				return;
			} catch (toba_error $e) {
				
			}
		}
		if(isset($clase) && method_exists( $clase, $opcion ) ) {
			if (! isset($argumentos)) {
				$argumentos = array();
			}
			$argumentos = array_merge($argumentos, $this->get_parametros());
			$clase->$opcion($argumentos);
		} elseif (method_exists( $this, $opcion)) {
			$this->$opcion($argumentos);
		} else {
			$this->consola->mensaje("La opcion '".$this->argumentos[0]."' no existe");
			$this->mostrar_ayuda();
		}
	}
	
	//-------------------------------------------------------------
	// Opciones
	//-------------------------------------------------------------


	/**
	 * Extiende las clases de las componentes de toba con las clases de los proyectos
	 * @consola_no_mostrar
	 */
	function opcion__extender_clases_toba()
	{
		$proyecto = $this->get_proyecto();
		if (util_modelo_proyecto::extender_clases($proyecto, $this->consola, 'toba')) {
			$this->consola->mensaje('Extensión exitosa. No olvide ejecutar el comando toba proyecto revincular');
		}
	}

	/**
	 * Hace personalizable un proyecto, se usa desde la opción crear y personalizable
	 * @param boolean $publicar decide si se presenta el diálogo de publicación o no
	 */
	protected function hacer_personalizable($publicar = true)
	{
		$proyecto = $this->get_proyecto();
		$pms = $proyecto->get_pms();

		util_modelo_proyecto::extender_clases($proyecto, $this->consola, 'proyecto');
		util_modelo_proyecto::crear_arbol_personalizacion($proyecto->get_dir());
		try {
			$pms->crear_pm_personalizacion();
		} catch (toba_error_db $e) {
			$this->consola->mensaje('No se pudo crear el punto de montaje para la personalización, puede que existiera o se haya producido un error, verifique los logs' , true);
		}

		if ($publicar) {
			$this->consola->separador();
			$agregar = $this->consola->dialogo_simple("¿Desea agregar el alias de apache al archivo toba.conf para los recursos personalizados?", true);
			if ($agregar) {
				$proyecto->publicar_pers();
				$this->consola->mensaje('OK. Debe reiniciar el servidor web para que los cambios tengan efecto');
			}
		}
	}

	function opcion__actualizar_proyecto()
	{
		$proyecto = $this->get_proyecto();
		
		$instalacion = $proyecto->get_instalacion();
		$instancia = $proyecto->get_instancia();
		$params = $instalacion->get_parametros_base($instancia->get_ini_base());
		$params['schema'] = 'desarrollo';
		$instalacion->actualizar_db($instancia->get_ini_base(), $params);
	}

	/**
	 * Hace que un proyecto pueda ser personalizado.
	 */
	function opcion__personalizable()
	{
		$proyecto = $this->get_proyecto();

		if (!$proyecto->tiene_clases_extendidas('toba')) {
			$mensaje  = "Debe extender las clases de toba primero con el comando ";
			$mensaje .= "toba proyecto extender_clases_toba";
			$this->consola->mensaje($mensaje);
			return;
		}
		$this->hacer_personalizable();
		$proyecto->generar_autoload($this->consola);

		$mensaje  = "El proyecto ya es personalizable. Ahora debe revincular las clases con el comando toba proyecto revincular";
		$this->consola->mensaje($mensaje);
	}

	/**
	 *	Revincula las clases que representan componentes. Reescribe código, utilizar con CUIDADO.
	 */
	function opcion__revincular()
	{
		$params = $this->get_parametros();
		if (!isset($params['-d']) || !isset($params['-a'])) {
			$mensaje  = 'Debe especificar de y hasta donde se quiere revincular ';
			$mensaje .= 'con los parametros -d (toba|proyecto) y -a (proyecto|personalizacion)';
			$this->consola->mensaje($mensaje);
			return;
		}
		$de = $params['-d'];
		$a  = $params['-a'];
		$proyecto = $this->get_proyecto();
		util_modelo_proyecto::revincular_componentes($proyecto, $de, $a);
		$mensaje  = "Las clases del proyecto fueron revinculadas exitosamente";
		$this->consola->mensaje($mensaje);
	}

	/**
	 * Regenera el autoload del proyecto. Ejecutar cuando se crea una nueva clase.
	 * @consola_parametros Opcional: [-s] Si se utiliza esta opción solo se genera el autoload de la personalizacion
	 */
	function opcion__autoload()
	{
		$params = $this->get_parametros();
		$proyecto = $this->get_proyecto();
		$generar_solo_pers = false;
                $params = $this->get_parametros();
		if (isset($params['-s'])) {
                    $generar_solo_pers = true;
		}
		$extractor = $proyecto->generar_autoload($this->consola, false, true, $generar_solo_pers);
		$clases_repetidas = $extractor->get_clases_repetidas();
		$pms_no_encontrados = $extractor->get_pms_no_encontrados();
		
		if (isset($params['-v'])) {
			if (count($pms_no_encontrados) > 0) {
				$this->consola->separador('Puntos de montaje no encontrados');
			}

			if (count($clases_repetidas) > 0) {
				foreach ($clases_repetidas as $montaje => $clase) {
					$this->consola->separador("Clases repetidas de '[$montaje]'");

					foreach ($clase as $key => $paths) {
						$this->consola->mensaje("\n[$key]");
						foreach ($paths as $path) {
							$this->consola->mensaje($path, true);
						}
					}
				}
			}
		} else {
			$hubo_error = false;
			if (count($pms_no_encontrados) > 0) {
				$hubo_error = true;
				$this->consola->mensaje("Hubo puntos de montaje no encontrados.");
			}

			if (count($clases_repetidas) > 0) {
				$hubo_error = true;
				$this->consola->mensaje("Existen clases repetidas.");
			}

			if ($hubo_error) {
				$this->consola->mensaje('Ejecute el comando con la opción -v para más información.');
			} else {
				$this->consola->mensaje("Se generaron los archivos correctamente");
			}
		}
	}

	/**
	* Crea un proyecto NUEVO.
	* @consola_parametros Opcional: [-x] Si se utiliza esta opción el proyecto creado será personalizable
	* @gtk_icono nucleo/agregar.gif 
	* @gtk_no_mostrar 1
	*/
	function opcion__crear()
	{
		$id_instancia = $this->get_id_instancia_actual();
		$id_proyecto = $this->get_id_proyecto_actual();
		$instancia = $this->get_instancia($id_instancia);
		$params = $this->get_parametros();
		
		// --  Creo el proyecto
		$this->consola->mensaje( "Creando el proyecto '$id_proyecto' en la instancia '$id_instancia'...", false );
		$usuarios = $this->seleccionar_usuarios( $instancia );
		toba_modelo_proyecto::crear( $instancia, $id_proyecto, $usuarios );
		$this->consola->progreso_fin();
		
		// Extendemos las clases de toba a clases del proyecto
		$proyecto = $this->get_proyecto($id_proyecto);
		$pms = $proyecto->get_pms();
		util_modelo_proyecto::extender_clases($proyecto, $this->consola, 'toba');
		$pms->crear_pm_proyecto();
		
		// -- Modifica el proyecto para que sea apto para personalizaciones
		if (isset($params['-x'])) {
			$this->hacer_personalizable(false);
		}
		$proyecto->generar_autoload($this->consola);

		// -- Asigno un nuevo item de login
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
	* @consola_parametros Opcional: [-d 'directorio'] Especifica el path en donde se encuentra el proyecto (por ej. ../mi_proyecto ) 
	* @gtk_icono importar.png
	* @gtk_no_mostrar 1
	*/
	function opcion__cargar($datos = null)
	{
		if (! isset($datos)) {
			$path = null;
			$id_proyecto = $this->get_id_proyecto_actual(false);
			if (!isset($id_proyecto)) {
				list($id_proyecto, $path) = $this->seleccionar_proyectos(false, false);
				if ($id_proyecto == $path) {
					$path=null;
				}
			}
			$param = $this->get_parametros();
			if (isset($param['-d'])) {
				$path = realpath($param['-d']);
			}
		} else {
			$id_proyecto = $datos[0];
			$path = $datos[1];
		}
		$i = $this->get_instancia();
		if ( ! $i->existen_metadatos_proyecto( $id_proyecto ) ) {

			//-- 1 -- Cargar proyecto
			$this->consola->enter();
			$this->consola->subtitulo("Carga del Proyecto ".$id_proyecto);
			$i->vincular_proyecto( $id_proyecto, $path );
			$p = $this->get_proyecto($id_proyecto);
			$p->cargar_autonomo();
			$this->consola->mensaje("Vinculando usuarios", false);
			$usuarios = $this->seleccionar_usuarios( $p->get_instancia() );
			$grupo_acceso = $this->seleccionar_grupo_acceso($p);
			foreach ( $usuarios as $usuario ) {
				$p->vincular_usuario($usuario, array($grupo_acceso));
				toba_logger::instancia()->debug("Vinculando USUARIO: $usuario, GRUPO ACCESO: $grupo_acceso");
				$this->consola->progreso_avanzar();
			}
			$this->consola->progreso_fin();
			
			//-- 2 -- Exportar proyecto
			$this->consola->enter();
			// Exporto la instancia con la nueva configuracion (por fuera del request)
			$i->exportar_local();
		} else {
			$p = $this->get_proyecto($id_proyecto);
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
		$this->consola->mensaje("Version de la aplicación: ".$p->get_version_proyecto()."\n");
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
		try {
			$p = $this->get_proyecto();
			if ( $this->consola->dialogo_simple("Desea ELIMINAR los metadatos y DESVINCULAR el proyecto '"
					.$id_proyecto."' de la instancia '"
					.$this->get_id_instancia_actual()."'") ) {
				$p->eliminar_autonomo();
			}
		} catch (toba_error $e) {
			$this->consola->error($e->__toString());
		}
		$this->get_instancia()->desvincular_proyecto( $id_proyecto );
	}
	
	/**
	 * Exporta los METADATOS, actualiza el proyecto (usando svn) y regenera el proyecto en la instancia
	 * @gtk_icono refrescar.png
	 */
	function opcion__actualizar()
	{
		$this->consola->titulo("1.- Exportando METADATOS");		
		$this->opcion__exportar();

		$this->consola->titulo("2.- Actualizando el proyecto utilizando SVN");
		$p = $this->get_proyecto();		
		$p->actualizar();		
		
		$this->consola->titulo("3.- Regenerando el proyecto en la instancia");
		$p->regenerar();
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
	* Crea un instalador del proyeto/framework para produccion
	*/
	function opcion__empaquetar()
	{
		$this->get_proyecto()->empaquetar();
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
	* Importa y migra un proyecto desde otra instalacion de toba
	* @consola_parametros -d 'directorio'. Especifica el path de toba que contiene el proyecto a migrar
	* @gtk_icono importar.png 
	* @gtk_no_mostrar 1
	*/	
	function opcion__importar($datos = null)
	{
		if (isset($datos)) {
			list($id_proyecto, $dir_toba_viejo) = $datos;
		} else {
			$param = $this->get_parametros();
			$id_proyecto = $this->get_id_proyecto_actual(true);
 	        if (isset($param['-d'])) {
	            $dir_toba_viejo = $param['-d'];
	        } else {
	            throw new toba_error("Debe indicar el path del toba desde donde se quiere importar un proyecto con el parámetro -d");
 	        }			
		}		
		$this->get_instalacion()->importar_migrar_proyecto($this->get_id_instancia_actual(true), $id_proyecto, $dir_toba_viejo);
	}	
	
	
	
	/**
	 * Ejecuta las tareas planificadas pendientes
	 * @consola_parametros Opcional: [-v 0|1] Modo verbose
	 */
	function opcion__ejecutar_tareas()
	{
		$param = $this->get_parametros();
		$manejador_interface = null;
		if (isset($param['-v']) && $param['-v']) {
		    $manejador_interface = $this->consola;
		} else {
			$this->consola->set_verbose(false);
		}
		//Incluye el contexto consola
		require_once("nucleo/toba.php");
		toba::nucleo()->iniciar_contexto_desde_consola($this->get_id_instancia_actual(true), $this->get_id_proyecto_actual(true));
		
		//Ejecuta el planificador
		$planificador = new toba_planificador_tareas();
		$planificador->ejecutar_pendientes($manejador_interface);
	}
		
	/**
	 * Migra un proyecto entre dos versiones toba.
	 * @consola_parametros Opcionales: [-d 'desde']  [-h 'hasta'] [-R 0|1] [-m metodo puntual de migracion]
	 * @gtk_icono convertir.png 
	 * @consola_separador 1
	 * @gtk_separador 1
	 */
	function opcion__migrar_toba()
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

	/**
	* Muestra los LOGS del proyecto
	* @consola_parametros Opcional: [-n 'numero'] Muestra un log específico. Por defecto se muestra el último
	*/	
	function opcion__ver_log()
	{	
		$param = $this->get_parametros();
		$proyecto = isset($param["-p"]) ? $param["-p"] : $this->get_id_proyecto_actual(true);
		$instancia = isset($param["-i"]) ? $param["-i"] : $this->get_id_instancia_actual(true);
		toba_nucleo::instancia()->iniciar_contexto_desde_consola($instancia, $proyecto);

		$logger = toba_logger::instancia($proyecto);
		$archivo = $logger->directorio_logs()."/sistema.log";		
		$analizador = new toba_analizador_logger_fs($archivo);
		$analizador->procesar_entidades_html(false);

		//Identifico el ID de log a cargar
		$param = $this->get_parametros();
        if (isset($param['-n'])) {
       		$pedido = $param['-n'];
			if( $pedido < 1 || $pedido > $analizador->get_cantidad_pedidos() ) {
				$this->consola->mensaje("El log específico solicitado no existe.");
				return ;				
			}
        } else {
			$pedido = ($analizador->get_cantidad_pedidos());
        }

		//Muestro el log
		$res = $analizador->get_pedido($pedido);
		echo $res;
	}

	/**
	 *  Genera el script de creacion de roles en bd y asignacion de permisos a los mismos
	 */
	function opcion__roles_script()
	{
		$proyecto = $this->get_proyecto();		
		try {
			$proyecto->crear_script_generacion_roles_db();
			$this->consola->mensaje('Se generaron los archivos correspondientes a los roles');
		} catch (toba_error $e) {
			$this->consola->mensaje($e->getMessage());
		}
	}
}
?>