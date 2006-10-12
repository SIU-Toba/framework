<?
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
	
	//-------------------------------------------------------------
	// Opciones
	//-------------------------------------------------------------

	/**
	*	Muestra informacion de la instalacion.
	*/
	function opcion__info()
	{
		if ( instalacion::existe_info_basica() ) {
			$this->consola->enter();
			//VERSION
			$this->consola->lista(array(instalacion::get_version_actual()->__toString()), "VERSION");
			// INSTANCIAS
			$instancias = instancia::get_lista();
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
			$proyectos = proyecto::get_lista();
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
	*	Agrega una BASE en la instalacion [-d 'id_base']. Opcionalmente toma los datos de otra base  [-o base_origen]
	*/
	function opcion__agregar_db()
	{
		$def = $this->get_id_base_actual();
		if ( $this->get_instalacion()->existe_base_datos_definida( $def ) ) {
			throw new toba_error( "Ya existe una base definida con el ID '$def'");
		}
		$param = $this->get_parametros();
		if ( isset($param['-o']) &&  (trim($param['-o']) != '') ) {
			$origen =  $param['-o'];
			if (! $this->get_instalacion()->existe_base_datos_definida($origen)) {
				throw new toba_error( "No existe la base origen '$origen'");
			}
			$datos = $this->get_instalacion()->get_parametros_base($origen);
			
		} else {
			$form = $this->consola->get_formulario("Definir una nueva BASE de DATOS");
			$form->agregar_campo( array( 'id' => 'motor', 	'nombre' => 'MOTOR' ) );
			$form->agregar_campo( array( 'id' => 'profile',	'nombre' => 'HOST/PROFILE' ) );
			$form->agregar_campo( array( 'id' => 'usuario', 'nombre' => 'USUARIO' ) );
			$form->agregar_campo( array( 'id' => 'clave', 	'nombre' => 'CLAVE' ) );
			$form->agregar_campo( array( 'id' => 'base', 	'nombre' => 'BASE' ) );
			$datos = $form->procesar();
			
		}		
		$this->get_instalacion()->agregar_db( $def, $datos );
	}

	/**
	*	Elimina una BASE en la instalacion. [-d 'id_base']
	*/
	function opcion__eliminar_db()
	{
		$i = $this->get_instalacion();
		$def = $this->get_id_base_actual();
		if ( $i->existe_base_datos_definida( $def ) ) {
			$this->consola->enter();
			$this->consola->subtitulo("DEFINICION: $def");
			$this->consola->lista_asociativa( $i->get_parametros_base( $def ), array('Parametro','Valor') );
			$this->consola->enter();
			if ( $this->consola->dialogo_simple("Desea eliminar la definicion?") ) {
				$i->eliminar_db( $def );
			}
		} else {
			throw new toba_error( "NO EXISTE una base definida con el ID '$def'");
		}
	}
	
	/**
	*	Crea una BASE DEFINIDA en el motor. [-d 'id_base']
	*/
	function opcion__crear_base()
	{
		$def = $this->get_id_base_actual();
		if( $this->get_instalacion()->existe_base_datos( $def ) !== true ) {
			$this->get_instalacion()->crear_base_datos( $def );
		} else {
			throw new toba_error( "Ya EXISTE una base '$def' en el MOTOR");
		}
	}

	/**
	*	Elimina una BASE DEFINIDA existente dentro del motor. [-d 'id_base']
	*/
	function opcion__eliminar_base()
	{
		$def = $this->get_id_base_actual();
		if ( $this->get_instalacion()->existe_base_datos( $def ) ) {
			$this->consola->enter();
			$this->consola->subtitulo("BASE de DATOS: $def");
			$this->consola->lista_asociativa( $this->get_instalacion()->get_parametros_base( $def ), array('Parametro','Valor') );
			$this->consola->enter();
			if ( $this->consola->dialogo_simple("Desea eliminar la BASE de DATOS?") ) {
				$this->get_instalacion()->borrar_base_datos( $def );
			}
		} else {
			throw new toba_error( "NO EXISTE una base '$def' en el MOTOR");
		}
	}

	/**
	*	Chequea la conexion con una base. [-d 'id_base']
	*/
	function opcion__test_conexion()
	{
		$def = $this->get_id_base_actual();
		if ( $this->get_instalacion()->existe_base_datos( $def ) ) {
			$this->consola->mensaje('Conexion OK!');
		} else {
			$this->consola->error("No es posible conectarse a '$def'");
		}
	}
	
	/**
	* Ejecuta un archivo sql contra una base. [-d 'id_base'] -a archivo
	*/
	function opcion__ejecutar_sql()
	{
		$param = $this->get_parametros();
		if ( isset($param['-a']) &&  (trim($param['-a']) != '') ) {
			$archivo = $param['-a'];
		} else {
			throw new toba_error("Es necesario indicar el archivo a ejecutar. Utilice el modificador '-a'");
		}		
		$db = $this->get_instalacion()->conectar_base($this->get_id_base_actual());
		$db->ejecutar_archivo($archivo);
	}	

	/**
	*	Crea una instalacion.
	*/
	function opcion__crear()
	{
		if( ! instalacion::existe_info_basica() ) {
			$this->consola->titulo( "Configurando INSTALACION en: " . instalacion::dir_base() );
			$id_grupo_desarrollo = self::definir_id_grupo_desarrollo();
			$alias = self::definir_alias_nucleo();
			instalacion::crear( $id_grupo_desarrollo, $alias );
			$this->consola->enter();
			$this->consola->mensaje("La instalacion ha sido inicializada");
			$this->consola->mensaje("Para definir bases de datos, utilize el comando 'toba instalacion agregar_db -d [nombre_base]'");
		} else {
			$this->consola->enter();
			$this->consola->mensaje( 'Ya existe una INSTALACION.' );
			$this->consola->enter();
		}
	}

	/**
	 * Migra la instalación actual. [-d 'desde']  [-h 'hasta'] [-R 0|1].
	 * -d se asume la versión de toba de la última instancia cargada.
	 * -h se asume la versión de toba del código actual.
	 * -R asume 1, esto quiere decir que migra también todas las instancias y proyectos que encuentre
	 */
	function opcion__migrar()
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
	
	/**
	 * Ejecuta una instalacion inicial básica del framework 
	 */
	function opcion__autoinstalar()
	{
		$nombre_toba = 'toba_'.instalacion::get_version_actual()->get_string_partes();		
		$this->consola->titulo("Instalación Toba ".instalacion::get_version_actual()->__toString());

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
/*		
		$version_svn = shell_exec('svn --version');
		if ($version_svn == '') {
			$this->consola->mensaje("\n------------------------------------");
			throw new toba_error("ERROR: El comando 'svn' no se encuentra en el path actual del sistema,.");
		}		
*/
	
		//--- Borra la instalacion anterior??
		if ( ! instalacion::existe_info_basica() ) {
			$forzar_instalacion = true;
		} else {
			$forzar_instalacion = ! $this->consola->dialogo_simple("Ya existe una instalación anterior desea conservarla ?");
			if ($forzar_instalacion) {
				instalacion::borrar_directorio();
			}
		}
		//--- Crea la INSTALACION		
		if ($forzar_instalacion) {
			instalacion::crear( 0, $nombre_toba );			
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
		$id_proyecto = $this->consola->dialogo_ingresar_texto( 'Identificador del proyecto a crear (no utilizar mayusculas o espacios)', true);

		//--- Si el proyecto existe, lo borra
		$existe_proyecto = proyecto::existe($id_proyecto);
		if ($existe_proyecto && $forzar_instalacion) {
			toba_manejador_archivos::eliminar_directorio(  toba_dir() . "/proyectos/" . $id_proyecto );
			$existe_proyecto = false;
		}		
		
		//--- Crea la instancia
		$id_instancia = $this->get_entorno_id_instancia(true);
		$proyectos = proyecto::get_lista();
		if (isset($proyectos['toba_testing'])) {
			//--- Elimina el proyecto toba_testing 
			unset($proyectos['toba_testing']);
		}
		instancia::crear_instancia( $id_instancia, $base, $proyectos );
		
		//-- Carga la instancia
		$instancia = $this->get_instancia();
		if (!$instancia->existe_modelo() || $forzar_instalacion) {
			$instancia->cargar( true );
		}
		$instancia->set_version( instalacion::get_version_actual());
		
		//--- Crea el proyecto
		if (!$existe_proyecto ) {
			proyecto::crear( $instancia, $id_proyecto, array() );
			$nuevo_proyecto = $this->get_proyecto($id_proyecto);			
			$nuevo_proyecto->actualizar_login();
			$nuevo_proyecto->exportar();			
		}
		
		//--- Vincula un usuario a todos los proyectos
		$instancia->agregar_usuario( 'toba', 'Usuario Toba', 'toba');
		foreach( $instancia->get_proyectos() as $proyecto ) {
			$grupo_acceso = $this->seleccionar_grupo_acceso( $proyecto );
			$proyecto->vincular_usuario( 'toba', $grupo_acceso );
		}

		$instancia->exportar_local();
		
		//--- Crea los nuevos alias
		$instancia->crear_alias_proyectos();

		//--- Mensajes finales
		$toba_conf = instalacion::dir_base()."/toba.conf";
		$this->consola->separador();
		$this->consola->mensaje("La instalación del framework ha finalizado, para su correcta ejecución se necesita notificar a Apache y a la consola de su presencia");
		$this->consola->mensaje("");
		if (toba_manejador_archivos::es_windows()) {		
			$toba_conf = toba_manejador_archivos::path_a_unix($toba_conf);
			$this->consola->mensaje("Para Apache: agregar en el archivo '\Apache2\conf\httpd.conf' la siguiente directiva: ");
			$this->consola->mensaje("     Include \"$toba_conf\"");;
		} else {
			$this->consola->mensaje("Para Apache: ejecutar el siguiente comando como superusuario (se asume una distro tipo debian): ");
			$this->consola->mensaje("  ln -s $toba_conf /etc/apache2/sites-enabled/$nombre_toba");;			
		}
		$this->consola->mensaje("");
		$this->consola->mensaje("Para la consola: se necesitan agregar al entorno las siguientes directivas:");
		if (toba_manejador_archivos::es_windows()) {
			$dir_base = toba_manejador_archivos::path_a_windows(instalacion::dir_base());
			$this->consola->mensaje("   set toba_dir=".$dir_base);
			$this->consola->mensaje("   set toba_instancia=desarrollo");
			$this->consola->mensaje("   set PATH=%PATH%;%toba_dir%/bin");
		} else {
			$this->consola->mensaje("   export toba_dir=".instalacion::dir_base());
			$this->consola->mensaje("   export toba_instancia=desarrollo");
			$this->consola->mensaje('   export PATH="$toba_dir/bin:$PATH"');
		}
		$this->consola->mensaje("");
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
			return 'toba';
		} else {
			return $resultado;	
		}
		
	}
	
	/**
	*	Determina sobre que base definida en 'info_bases' se va a trabajar
	*/
	private function get_id_base_actual()
	{
		$param = $this->get_parametros();
		if ( isset($param['-d']) &&  (trim($param['-d']) != '') ) {
			return $param['-d'];
		} else {
			throw new toba_error("Es necesario indicar el ID de la BASE a utilizar. Utilice el modificador '-d'");
		}
	}
	

}
?>
