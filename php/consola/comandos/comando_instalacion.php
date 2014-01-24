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
		$nombre_toba = 'toba_'.toba_modelo_instalacion::get_version_actual()->get_release('_');
		$alias = '/'.'toba_'.toba_modelo_instalacion::get_version_actual()->get_release();
		$this->consola->titulo("Instalacion Toba ".toba_modelo_instalacion::get_version_actual()->__toString());

		//--- Verificar instalacion
		/*
		if (get_magic_quotes_gpc()) {
			$this->consola->mensaje("------------------------------------");
			throw new toba_error("ERROR: Necesita desactivar las 'magic_quotes_gpc' en el archivo php.ini (ver http://www.php.net/manual/es/security.magicquotes.disabling.php)");	
		}*/
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
		if (toba_modelo_instalacion::existe_info_basica() ) {
			toba_modelo_instalacion::borrar_directorio();
		}
		//--- Crea la INSTALACION		
		$id_desarrollo = $this->definir_id_grupo_desarrollo();
		$tipo_instalacion = $this->definir_tipo_instalacion_produccion();
		toba_modelo_instalacion::crear($id_desarrollo, $alias, $tipo_instalacion);
		$id_instancia = ($tipo_instalacion == '1') ? 'produccion' : $this->get_entorno_id_instancia(true);		
		
		//--- Crea la definicion de bases
		$base = $nombre_toba;
		$puerto = '5432';			//Asumo el puerto por defecto del servidor;
		if (! $this->get_instalacion()->existe_base_datos_definida( $base ) ) {
			do {
				$profile = $this->consola->dialogo_ingresar_texto( 'PostgreSQL - Ubicación (ENTER utilizará localhost)', false);
				if ($profile == ''){
					$profile = 'localhost';
				}				
				$puerto_tmp = $this->consola->dialogo_ingresar_texto( "PostgreSQL - Puerto (ENTER utilizará: $puerto)", false);
				if ($puerto_tmp != ''){		
					$puerto = $puerto_tmp;
				}
				$usuario = $this->consola->dialogo_ingresar_texto( 'PostgreSQL - Usuario (ENTER utilizará postgres)', false);
				if ($usuario == '') {
					$usuario = 'postgres';
				}
				$clave = $this->consola->dialogo_ingresar_texto( 'PostgreSQL - Clave  (ENTER para usar sin clave)', false);
				$base_temp = $this->consola->dialogo_ingresar_texto( "PostgreSQL - Base de datos (ENTER utilizará $base)", false);
				if ($base_temp != ''){
					$base = $base_temp;
				}
				if ($puerto_tmp != ''){		
					$puerto = $puerto_tmp;
				}
				$datos = array(
					'motor' => 'postgres7',
					'profile' => $profile,
					'usuario' => $usuario,
					'clave' => $clave,
					'base' => $base,
					'puerto' => $puerto,
					'encoding' => 'LATIN1',
					'schema' => $id_instancia
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
		//--- Pido el password para el usuario por defecto
		$pwd = $this->definir_clave_usuario_admin();
				
		//--- Si la base existe, pregunta por un nombre alternativo, por si no quiere pisarla
		if ($this->get_instalacion()->existe_base_datos($base, array(), false, $id_instancia)) {
			$nueva_base = $this->consola->dialogo_ingresar_texto("La base '$base' ya contiene un schema '$id_instancia', puede ingresar un nombre ".
																"de base distinto sino quiere sobrescribir los datos actuales: (ENTER sobrescribe la actual)", false);			
			if ($nueva_base != '') {																
				$datos['base'] = $nueva_base;
				$this->get_instalacion()->agregar_db( $base, $datos );
			}
		}
		
		//--- Crea la instancia
		$proyectos = toba_modelo_proyecto::get_lista();
		if (isset($proyectos['toba_testing'])) {
			//--- Elimina el proyecto toba_testing 
			unset($proyectos['toba_testing']);
		}
		if (isset($proyectos['curso_intro'])) {
			//--- Elimina el proyecto curso_intro 
			unset($proyectos['curso_intro']);
		}		
		toba_modelo_instancia::crear_instancia( $id_instancia, $base, $proyectos );
		
		//-- Carga la instancia
		$instancia = $this->get_instancia($id_instancia);
		$instancia->cargar( true );

		//--- Vincula un usuario a todos los proyectos y se instala el proyecto				
		$instancia->agregar_usuario( 'toba', 'Usuario Toba', $pwd);
		foreach( $instancia->get_lista_proyectos_vinculados() as $id_proyecto ) {
			$proyecto = $instancia->get_proyecto($id_proyecto);
			$grupo_acceso = $proyecto->get_grupo_acceso_admin();
			$proyecto->vincular_usuario('toba', array($grupo_acceso));
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
		$this->consola->titulo("Configuraciones Finales");
		$toba_conf = toba_modelo_instalacion::dir_base()."/toba.conf";
		if (toba_manejador_archivos::es_windows()) {		
			$toba_conf = toba_manejador_archivos::path_a_unix($toba_conf);
			$this->consola->mensaje("1) Agregar al archivo '\Apache2\conf\httpd.conf' la siguiente directiva: ");
			$this->consola->mensaje("");
			$this->consola->mensaje("     Include \"$toba_conf\"");;
		} else {
			$this->consola->mensaje("1) Ejecutar el siguiente comando como superusuario: ");
			$this->consola->mensaje("");			
			$this->consola->mensaje("     ln -s $toba_conf /etc/apache2/sites-enabled/$nombre_toba.conf");
		}
		$this->consola->mensaje("");
		$url = $instancia->get_proyecto('toba_editor')->get_url();
		$this->consola->mensaje("Reiniciar el servicio apache e ingresar al framework navegando hacia ");
		$this->consola->mensaje("");
		$this->consola->mensaje("     http://localhost$url");		
		$this->consola->mensaje("");
		
			
		$this->consola->mensaje("");

		$release = toba_modelo_instalacion::get_version_actual()->get_release();
		if (toba_manejador_archivos::es_windows()) {
			if (isset($_SERVER['USERPROFILE'])) {
				$path = $_SERVER['USERPROFILE'];
			} else {
				$path = toba_dir()."\\bin";
			}
			$path .= "\\entorno_toba_$release.bat";
			$bat = "@echo off\n";
			$bat .= "set TOBA_DIR=".toba_dir()."\n";
			$bat .= "set TOBA_INSTANCIA=$id_instancia\n";
			$bat .= "set PATH=%PATH%;%TOBA_DIR%/bin\n";
			$bat .= "echo Entorno cargado.\n";
			$bat .= "echo Ejecute 'toba' para ver la lista de comandos disponibles.\n";
			file_put_contents($path, $bat);
			$this->consola->mensaje("2) Se genero el siguiente .bat:");
			$this->consola->mensaje("");
			$this->consola->mensaje("   $path");
			$this->consola->mensaje("");
			$this->consola->mensaje("Para usar los comandos toba ejecute el .bat desde una sesión de consola (cmd.exe)");
			
		} else {
			$path = toba_dir()."/bin";
			$path .= "/entorno_toba_$release.sh";
			$bat = "export TOBA_DIR=".toba_dir()."\n";
			$bat .= "export TOBA_INSTANCIA=$id_instancia\n";
			$bat .= 'export PATH="$TOBA_DIR/bin:$PATH"'."\n";
			$bat .= "echo \"Entorno cargado.\"\n";
			$bat .= "echo \"Ejecute 'toba' para ver la lista de comandos disponibles.\"\n";
			file_put_contents($path, $bat);
			chmod($path, 0755);
			$this->consola->mensaje("2) Se genero el siguiente ejecutable:");
			$this->consola->mensaje("");
			$this->consola->mensaje("   $path");
			$this->consola->mensaje("");
			$sh = basename($path);
			$this->consola->mensaje("Para usar los comandos toba ejecute antes el .sh precedido por un punto y espacio");
			
		}
		$this->consola->mensaje("");
		$this->consola->mensaje("3) Entre otras cosas puede crear un nuevo proyecto ejecutando el comando");
		$this->consola->mensaje("");
		$this->consola->mensaje("   toba proyecto crear");		
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
	 * @consola_parametros [-u usuario apache, se asume www-data] [-g grupo de usuarios, no se asume ninguno]
	 * @gtk_icono  password.png
	 */
	function opcion__cambiar_permisos()
	{
		//Si es produccion dar permisos solo a apache, sino a usuario y grupo
		$subject = $this->get_instalacion()->es_produccion() ? "u" : "ug";
		$param = $this->get_parametros();
		$grupo = isset($param['-g']) ? $param['-g'] : null;
		$usuario = isset($param['-u']) ? $param['-u'] : 'www-data';
		$toba_dir = toba_dir();
		$this->consola->subtitulo('Cambiando permisos de archivos navegables');
		$comandos = array(
			array("chown -R $usuario $toba_dir/www", "Archivos navegables comunes:\n"),
			array("chmod -R $subject+rw $toba_dir/www", ''),			
			array("chown -R $usuario $toba_dir/instalacion", "Archivos de configuración:\n"),
			array("chmod -R $subject+rw $toba_dir/instalacion", ''),			
			array("chown -R $usuario $toba_dir/temp", "Archivos temporales comunes:\n"),
			array("chmod $subject+rw $toba_dir/temp", '')
		);
		foreach (toba_modelo_instalacion::get_lista_proyectos() as $proyecto) {
			$id_proyecto = basename($proyecto);
			$comandos[] = array("chown -R $usuario $proyecto/www", "Archivos navegables de $id_proyecto:\n");
			$comandos[] = array("chmod -R $subject+rw $proyecto/www", '');
			$comandos[] = array("chown -R $usuario $proyecto/temp", "Archivos temporales de $id_proyecto:\n");
			$comandos[] = array("chmod -R $subject+rw $proyecto/temp", '');
		}		
		foreach ($comandos as $comando) {
			$this->consola->mensaje($comando[1], false);
			$this->consola->mensaje("   ".$comando[0]. exec($comando[0]));
		}
		
		if (isset($grupo)) {
			$comando = "chgrp -R $grupo $toba_dir";
			$this->consola->subtitulo("\nCambiando permisos globales para el grupo $grupo");
			$this->consola->mensaje("   ".$comando. exec($comando));
			$comando = "chmod -R g+rw $toba_dir";
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
	 * Cambia el número de desarrollador y deja las instancias listas
	 */
	function opcion__cambiar_id_desarrollador()
	{
		$id_grupo_desarrollador = $this->definir_id_grupo_desarrollo();
		$this->get_instalacion()->set_id_desarrollador($id_grupo_desarrollador);		
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
		$desde = isset($param['-d']) ? new toba_version($param['-d']) : $instalacion->get_version_anterior();
		$hasta = isset($param['-h']) ? new toba_version($param['-h']) : $instalacion->get_version_actual();
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
		
		$instalacion->migrar_rango_versiones($desde, $hasta, $recursivo);
	}		
	
	/**
	* Incluye en el archivo toba.conf las configuraciones de alias definidas en instalacion.ini e instancia.ini
	*/
	function opcion__publicar()
	{
		if (! $this->get_instalacion()->esta_publicado()) {
			$this->get_instalacion()->publicar();
			$this->consola->mensaje('OK. Debe reiniciar el servidor web para que los cambios tengan efecto');
		} else {
			throw new toba_error("La instalación ya se encuentra publicada. Debe despublicarla primero");
		}
	}	
	
	/**
	* Quita del archivo toba.conf los alias de la instalacion y de los proyectos
	*/
	function opcion__despublicar()
	{
		if ($this->get_instalacion()->esta_publicado()) {
			$this->get_instalacion()->despublicar();
			$this->consola->mensaje('OK. Debe reiniciar el servidor web para que los cambios tengan efecto');
		} else {
			throw new toba_error("La instalación no se encuentra actualmente publicada.");
		}
	}		
	
	//-------------------------------------------------------------
	// Interface
	//-------------------------------------------------------------

	/**
	*	Consulta al usuario el ID del grupo de desarrollo
	*/
	protected function definir_id_grupo_desarrollo()
	{
		do {
			$es_invalido = false;
			$id_desarrollo = $this->consola->dialogo_ingresar_texto('Por favor, ingrese su número de desarrollador (ENTER utilizará 0)', false);
			$mensaje = "Debe ser un entero positivo, mas info en http://repositorio.siu.edu.ar/trac/toba/wiki/Referencia/CelulaDesarrollo";
			if ($id_desarrollo == '') {
				$id_desarrollo = 0;
			}
			if (! is_numeric($id_desarrollo)) {
				$es_invalido = true;
				$this->consola->mensaje($mensaje);
			}
			if ($id_desarrollo < 0) {
				$es_invalido = true;
				$this->consola->mensaje($mensaje);
			}				
		} while ($es_invalido);
		$id_desarrollo = (int) $id_desarrollo;
		return $id_desarrollo;		
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
	
	protected function definir_tipo_instalacion_produccion()
	{
		$tipo_desarrollo = $this->consola->dialogo_simple('Se trata de una instalacion de producción?');
		return ($tipo_desarrollo) ? 1: 0;		
	}
	
	protected function definir_clave_usuario_admin()
	{
		do {
			$es_invalido = false;
			$pwd = $this->consola->dialogo_ingresar_texto('Toba - Clave (usuario "toba")', true);
			//Verifico que la clave cumpla ciertos requisitos basicos
			if ($this->get_instalacion()->es_produccion()) {
				try {
					toba_usuario::verificar_composicion_clave($pwd, apex_pa_pwd_largo_minimo);			
				} catch (toba_error_pwd_conformacion_invalida $e) {
					$es_invalido = true;
					$this->consola->mensaje($e->getMessage(), true);
				}
			}			
		} while($es_invalido);
		if (strtoupper($pwd) == 'TOBA') {
			$this->consola->mensaje('Este password puede crear un OJO de seguridad, por favor cambialo lo antes posible', true);
		}
		return $pwd;
	}
}
?>
