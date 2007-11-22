<?php
require_once('comando_toba.php');

class comando_instalacion extends comando_toba
{
	static function get_info()
	{
		return "Administracion de la INSTALACION";
	}

	function mostrar_observaciones()
	{
		$this->consola->mensaje("Directorio BASE: " . toba_dir() );
		$this->consola->enter();
	}

	function get_info_extra()
	{
		$salida = "Path: ".toba_dir();
		$salida .= "\nVersión: ".toba_modelo_instalacion::get_version_actual()->__toString();
		$instalacion = $this->get_instalacion();
		if ($instalacion->existe_info_basica()) {
			$grupo = $instalacion->get_id_grupo_desarrollo();
			if (isset($grupo)) {
				$salida .= "\nGrupo de desarrollo: ".$grupo;
			}
		}
		return $salida;
	}
	
	//-------------------------------------------------------------
	// Opciones
	//-------------------------------------------------------------
	
	/**
	 * Ejecuta una instalacion completa del framework para desarrollar un nuevo proyecto
	 * @gtk_icono instalacion.png
	 */
	function opcion__instalar()
	{
		$nombre_toba = 'toba_'.toba_modelo_instalacion::get_version_actual()->get_string_partes();
		$alias = '/'.$nombre_toba;
		$this->consola->titulo("Instalación Toba ".toba_modelo_instalacion::get_version_actual()->__toString());

		//--- Verificar instalacion
		if (get_magic_quotes_gpc()) {
			$this->consola->mensaje("------------------------------------");
			throw new toba_error("ERROR: Necesita desactivar las 'magic_quotes_gpc' en el archivo php.ini (ver http://www.php.net/manual/es/security.magicquotes.disabling.php)");	
		}
		if (! extension_loaded('pdo')) {
			$this->consola->mensaje("------------------------------------");
			throw new toba_error("ERROR: Necesita activar la extension 'pdo' en el archivo php.ini");
		}
		if (! extension_loaded('pdo_pgsql')) {
			$this->consola->mensaje("------------------------------------");
			throw new toba_error("ERROR: Necesita activar la extension 'pdo_pgsql' en el archivo php.ini");
		}		
		$version_php = shell_exec('php -v');
		if ($version_php == '') {
			$this->consola->mensaje("------------------------------------");
			throw new toba_error("ERROR: El comando 'php' no se encuentra en el path actual del sistema");
		}

		//--- Borra la instalacion anterior??
		if ( ! toba_modelo_instalacion::existe_info_basica() ) {
			$forzar_instalacion = true;
		} else {
			$forzar_instalacion = ! $this->consola->dialogo_simple("Ya existe una instalación anterior desea conservarla ?");
			if ($forzar_instalacion) {
				toba_modelo_instalacion::borrar_directorio();
			}
		}
		//--- Crea la INSTALACION		
		if ($forzar_instalacion) {
			toba_modelo_instalacion::crear( 0, $alias );			
		}
		
		//--- Crea la definicion de bases
		$base = $nombre_toba;
		if (! $this->get_instalacion()->existe_base_datos_definida( $base ) ) {
			do {
				$profile = $this->consola->dialogo_ingresar_texto( 'Ubicación del servidor Postgres (ej. localhost)', true);
				$usuario = $this->consola->dialogo_ingresar_texto( 'Usuario del servidor (ej. dba)', true);
				$clave = $this->consola->dialogo_ingresar_texto( 'Clave de conexión', false);
				$datos = array(
					'motor' => 'postgres7',
					'profile' => $profile,
					'usuario' => $usuario,
					'clave' => $clave,
					'base' => $base
				);
				$this->get_instalacion()->agregar_db( $base, $datos );
				//--- Intenta conectar al servidor
				$puede_conectar = $this->get_instalacion()->existe_base_datos($base, array('base' => 'template1'), true);
				if ($puede_conectar !== true) {
					$this->consola->mensaje("\nNo es posible conectar con el servidor, por favor reeingrese la información de conexión. Mensaje:");
					$this->consola->mensaje($puede_conectar."\n");
				}
			} while ($puede_conectar !== true);
		}	

		//--- Si la base existe, pregunta por un nombre alternativo, por si no quiere pisarla
		if ($this->get_instalacion()->existe_base_datos($base)) {
			$nueva_base = $this->consola->dialogo_ingresar_texto("La base '$base' ya está siendo utiliza en este servidor, puede ingresar un nombre ".
																"distinto sino quiere sobrescribirla: (ENTER sobrescribe la actual)", false);			
			if ($nueva_base != '') {																
				$datos['base'] = $nueva_base;
				$this->get_instalacion()->agregar_db( $base, $datos );
			}
		}
		
		//--- Pregunta identificador del Proyecto
		$id_proyecto = $this->consola->dialogo_ingresar_texto( 'Identificador del proyecto a crear (no utilizar mayusculas o espacios, puede ser vacio si no se quiere crear)', false);

		//--- Si ingreso un proyecto y existe, lo borra
		if ($id_proyecto != '') {
			$existe_proyecto = toba_modelo_proyecto::existe($id_proyecto);
			if ($existe_proyecto && $forzar_instalacion) {
				toba_manejador_archivos::eliminar_directorio(  toba_dir() . "/proyectos/" . $id_proyecto );
				$existe_proyecto = false;
			}
		}
		
		//--- Crea la instancia
		$id_instancia = $this->get_entorno_id_instancia(true);
		$proyectos = toba_modelo_proyecto::get_lista();
		if (isset($proyectos['toba_testing'])) {
			//--- Elimina el proyecto toba_testing 
			unset($proyectos['toba_testing']);
		}
		toba_modelo_instancia::crear_instancia( $id_instancia, $base, $proyectos );
		
		//-- Carga la instancia
		$instancia = $this->get_instancia();
		if (!$instancia->existe_modelo() || $forzar_instalacion) {
			$instancia->cargar( true );
		}
		
		//--- Crea el proyecto
		if ($id_proyecto != '' && !$existe_proyecto ) {
			toba_modelo_proyecto::crear( $instancia, $id_proyecto, array() );
			$nuevo_proyecto = $this->get_proyecto($id_proyecto);			
		}
		
		//--- Vincula un usuario a todos los proyectos y se instala el proyecto
		$instancia->agregar_usuario( 'toba', 'Usuario Toba', 'toba');
		foreach( $instancia->get_lista_proyectos_vinculados() as $id_proyecto ) {
			$proyecto = $instancia->get_proyecto($id_proyecto);
			$grupo_acceso = $proyecto->get_grupo_acceso_admin();
			$proyecto->vincular_usuario( 'toba', $grupo_acceso );
		}
		
		//--- Crea el login y exporta el proyecto
		if (isset($nuevo_proyecto)) {
			$nuevo_proyecto->actualizar_login();	
			$nuevo_proyecto->exportar();	
		}

		$instancia->exportar_local();
		
		//--- Crea los nuevos alias
		$instancia->crear_alias_proyectos();
		
		//--- Ejecuta instalaciones particulares de cada proyecto
		foreach( $instancia->get_lista_proyectos_vinculados() as $id_proyecto ) {
			$instancia->get_proyecto($id_proyecto)->instalar();
		}		

		//--- Mensajes finales
		$toba_conf = toba_modelo_instalacion::dir_base()."/toba.conf";
		$this->consola->separador();
		$this->consola->mensaje("La instalación del framework ha finalizado, para su correcta ejecución se necesita notificar a Apache y a la consola de su presencia");
		$this->consola->mensaje("");
		if (toba_manejador_archivos::es_windows()) {		
			$toba_conf = toba_manejador_archivos::path_a_unix($toba_conf);
			$this->consola->mensaje("Para Apache: agregar en el archivo '\Apache2\conf\httpd.conf' la siguiente directiva y reiniciarlo: ");
			$this->consola->mensaje("     Include \"$toba_conf\"");;
		} else {
			$this->consola->mensaje("Para Apache: ejecutar el siguiente comando como superusuario y reiniciarlo (se asume una distro tipo debian): ");
			$this->consola->mensaje("  ln -s $toba_conf /etc/apache2/sites-enabled/$nombre_toba");
		}
		$this->consola->mensaje("");
		$this->consola->mensaje("Para la consola: se necesitan agregar al entorno las siguientes directivas:");
		if (toba_manejador_archivos::es_windows()) {
			$this->consola->mensaje("   set toba_dir=".toba_dir());
			$this->consola->mensaje("   set toba_instancia=desarrollo");
			$this->consola->mensaje("   set PATH=%PATH%;%toba_dir%/bin");
		} else {
			$this->consola->mensaje("   export toba_dir=".toba_dir());
			$this->consola->mensaje("   export toba_instancia=desarrollo");
			$this->consola->mensaje('   export PATH="$toba_dir/bin:$PATH"');
		}
		$this->consola->mensaje("");
	}
	
	/**
	* Crea una instalación básica.
	* @gtk_icono nucleo/agregar.gif
	*/
	function opcion__crear()
	{
		if( ! toba_modelo_instalacion::existe_info_basica() ) {
			$this->consola->titulo( "Configurando INSTALACION en: " . toba_modelo_instalacion::dir_base() );
			$id_grupo_desarrollo = self::definir_id_grupo_desarrollo();
			$alias = self::definir_alias_nucleo();
			toba_modelo_instalacion::crear( $id_grupo_desarrollo, $alias );
			$this->consola->enter();
			$this->consola->mensaje("La instalacion ha sido inicializada");
			$this->consola->mensaje("Para definir bases de datos, utilize el comando 'toba base registrar -d [nombre_base]'");
		} else {
			$this->consola->enter();
			$this->consola->mensaje( 'Ya existe una INSTALACION.' );
			$this->consola->enter();
		}
	}
	
	
	/**
	 * Muestra información de la instalación.
	 * @gtk_icono info_chico.gif
	 * @gtk_no_mostrar 1
	 * @gtk_separador 1 
	 */
	function opcion__info()
	{
		if ( toba_modelo_instalacion::existe_info_basica() ) {
			$this->consola->enter();
			//VERSION
			$this->consola->lista(array(toba_modelo_instalacion::get_version_actual()->__toString()), "VERSION");
			// INSTANCIAS
			$instancias = toba_modelo_instancia::get_lista();
			if ( $instancias ) {
				$this->consola->lista( $instancias, 'INSTANCIAS' );
			} else {
				$this->consola->enter();
				$this->consola->mensaje( 'ATENCION: No existen INSTANCIAS definidas.');
			}
			// BASES
			$this->mostrar_bases_definidas();
			// ID de grupo de DESARROLLO
			$grupo = $this->get_instalacion()->get_id_grupo_desarrollo();
			if ( isset ( $grupo ) ) {
				$this->consola->lista( array( $grupo ), 'ID grupo desarrollo' );
			} else {
				$this->consola->enter();
				$this->consola->mensaje( 'ATENCION: No esta definido el ID del GRUPO de DESARROLLO.');
			}
			// PROYECTOS
			$proyectos = toba_modelo_proyecto::get_lista();
			if ( $proyectos ) {
				$lista_proyectos = array();
				foreach ($proyectos as $dir => $id) {
					$lista_proyectos[] = "$id ($dir)";
				}
				$this->consola->lista( $lista_proyectos, 'PROYECTOS (sólo en la carpeta por defecto)' );
			} else {
				$this->consola->enter();
				$this->consola->mensaje( 'ATENCION: No existen PROYECTOS definidos.');
			}
		} else {
			$this->consola->enter();
			$this->consola->mensaje( 'La INSTALACION no ha sido inicializada.');
		}
	}
	
	/**
	* Crea una instancia
	* @consola_no_mostrar 1 
	* @gtk_icono instancia.gif 
	* @gtk_param_extra crear_instancia
	*/	
	function opcion__crear_instancia($datos)
	{
		//------ESTO ES UN ALIAS DE INSTANCIA::CREAR
		require_once('comando_instancia.php');
		$comando = new comando_instancia($this->consola);
		$comando->opcion__crear($datos);
	}
	
	/**
	 * Cambia los permisos de los archivo para que el usuario Apache cree directorios y pueda crear y leer carpetas navegables 
	 * @consola_parametros [-g grupo de usuario, se asume www-data] [-u usuario, no se asume ninguno]
	 * @gtk_icono  password.png
	 */
	function opcion__cambiar_permisos()
	{
		$param = $this->get_parametros();
		$grupo = isset($param['-g']) ? $param['-g'] : 'www-data';
		$usuario = isset($param['-u']) ? $param['-u'] : null;
		$toba_dir = toba_dir();
		$this->consola->subtitulo('Cambiando permisos de apache');
		$comandos = array(
			array("chgrp $grupo $toba_dir/www -R", "Archivos navegables comunes:\n"),
			array("chmod g+w $toba_dir/www -R", ''),			
			array("chgrp $grupo $toba_dir/instalacion -R", "Archivos de configuración:\n"),
			array("chmod g+w $toba_dir/instalacion -R", ''),			
			array("chgrp $grupo $toba_dir/temp -R", "Archivos temporales comunes:\n"),
			array("chmod g+w $toba_dir/temp", '')
		);
		foreach (toba_modelo_instalacion::get_lista_proyectos() as $proyecto) {
			$id_proyecto = basename($proyecto);
			$comandos[] = array("chgrp $grupo $proyecto/www -R", "Archivos navegables de $id_proyecto:\n");
			$comandos[] = array("chmod g+w $proyecto/www -R", '');
			$comandos[] = array("chgrp $grupo $proyecto/temp -R", "Archivos temporales de $id_proyecto:\n");
			$comandos[] = array("chmod g+w $proyecto/temp -R", '');
		}		
		foreach ($comandos as $comando) {
			$this->consola->mensaje($comando[1], false);
			$this->consola->mensaje("   ".$comando[0]. exec($comando[0]));
		}
		
		if (isset($usuario)) {
			$comando = "chown $usuario $toba_dir -R";
			$this->consola->subtitulo("\nCambiando permisos del usuario $usuario");
			$this->consola->mensaje('Se lo pone como owner de todos los archivos');			
			$this->consola->mensaje("   ".$comando. exec($comando));			
		}
	}

	/**
	 * Elimina los logs locales de la instalacion, instancias y proyectos contenidos
	 */
	function opcion__eliminar_logs()
	{
		$this->get_instalacion()->eliminar_logs();
	}
	

	/**
	 * Migra la instalación de versión. 
	 * @consola_parametros Opcionales: [-d 'desde']  [-h 'hasta'] [-R 0|1].
	 * @gtk_icono convertir.png
	 */
	function opcion__migrar_toba()
	{
		$instalacion = $this->get_instalacion();
		//--- Parametros
		$param = $this->get_parametros();
		$desde = isset($param['-d']) ? new version_toba($param['-d']) : $instalacion->get_version_anterior();
		$hasta = isset($param['-h']) ? new version_toba($param['-h']) : $instalacion->get_version_actual();
		$recursivo = (!isset($param['-R']) || $param['-R'] == 1);
		//$verbose = (isset($param['-V']));

		if ($recursivo) {
			$texto_recursivo = ", sus instancias y proyectos";
		}
		$desde_texto = $desde->__toString();
		$hasta_texto = $hasta->__toString();
		$this->consola->titulo("Migración de la instalación actual".$texto_recursivo." desde la versión $desde_texto hacia la $hasta_texto.");

		$versiones = $desde->get_secuencia_migraciones($hasta);
		if (empty($versiones)) {
			$this->consola->mensaje("No es necesario ejecutar una migración entre estas versiones");
			return ;
		} 
		
		//$this->consola->lista($versiones, "Migraciones disponibles");
		//$this->consola->dialogo_simple("");		
		$instalacion->migrar_rango_versiones($desde, $hasta, $recursivo);
	}		
	
	//-------------------------------------------------------------
	// Interface
	//-------------------------------------------------------------

	/**
	*	Consulta al usuario el ID del grupo de desarrollo
	*/
	protected function definir_id_grupo_desarrollo()
	{
		$this->consola->subtitulo('Definir el ID del grupo de desarrollo');
		$this->consola->mensaje('Este codigo se utiliza para permitir el desarrollo paralelo de equipos '.
								'de trabajo geograficamente distribuidos.');
		$this->consola->enter();
		$resultado = $this->consola->dialogo_ingresar_texto( 'ID Grupo', false );
		if ( $resultado == '' ) {
			return null;	
		} else {
			return $resultado;	
		}
	}

	protected function definir_alias_nucleo()
	{
		$this->consola->enter();		
		$this->consola->subtitulo('Definir el nombre del ALIAS del núcleo Toba');
		$this->consola->mensaje('Este alias se utiliza para consumir todo el contenido navegable de Toba');
		$this->consola->enter();
		$resultado = $this->consola->dialogo_ingresar_texto( 'Nombre del Alias (por defecto "toba")', false );
		if ( $resultado == '' ) {
			return '/toba';
		} else {
			return '/'.$resultado;	
		}
		
	}
	

	

}
?>
