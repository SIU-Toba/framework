<?php
require_once('comando_toba.php');

/**
 *  Clase que implementa los comandos de la instalacion de toba.
 *
 * Class comando_instalacion.
 * @package consola
 */
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
		$salida .= "\nVersi�n: ".toba_modelo_instalacion::get_version_actual()->__toString();
		$instalacion = $this->get_instalacion();
		if ($instalacion->existe_info_basica()) {
			$grupo = $instalacion->get_id_grupo_desarrollo();
			if (isset($grupo)) {
				$salida .= "\nGrupo de desarrollo: ".$grupo;
			}
		}
		return $salida;
	}

	function recuperar_parametro($lista, $parametro, $leyenda_alternativa)
	{
		if (! isset($lista[$parametro])) {
			$resultado = $this->consola->dialogo_ingresar_texto($leyenda_alternativa, false);
		} else {
			$resultado = $lista[$parametro];
		}
		return $resultado;
	}

	function mostrar_ayuda_desatendida()
	{
		$this->consola->titulo( $this->get_info() );

		$clase = new ReflectionClass(get_class($this));
		$metodo = $clase->getMethod('opcion__instalar');
		$comentario = $metodo->getDocComment();
		$opcion = array(
			'ayuda' => parsear_doc_comment($comentario),
			'tags' => parsear_doc_tags($comentario)
		);

		$salida = array();
		$id = 'instalar';
		if (!isset($opcion['tags']['consola_no_mostrar'])) {
			$salida[$id] = $opcion['ayuda'];
			if (isset($opcion['tags']['consola_parametros'])) {
				$salida[$id] .= "\n".$opcion['tags']['consola_parametros'];
			}
		}
		$this->consola->coleccion($salida);
	}
	//-------------------------------------------------------------
	// Opciones
	//-------------------------------------------------------------

	/**
	 * Ejecuta una instalacion completa del framework para desarrollar un nuevo proyecto
	 * @consola_parametros Opcionales: [-d 'iddesarrollo'] [-t 0| 1] [-n 'nombre inst'] [-h 'ubicacion bd'] [-p 'puerto'] [-u 'usuario bd'] [-b nombre bd] [-c 'archivo clave bd'] [-k 'archivo clave toba'] [--no-interactive][--alias-nucleo 'aliastoba'][--schema-toba 'schemaname'].
	 * @gtk_icono instalacion.png
	 */
	function opcion__instalar()
	{
		$nombre_toba = 'toba_'.toba_modelo_instalacion::get_version_actual()->get_release('_');
		$this->consola->titulo("Instalacion Toba ".toba_modelo_instalacion::get_version_actual()->__toString());

		//--- Verificar instalacion
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

		$param = $this->get_parametros();
		$interactive = !isset($param['--no-interactive']);
		if (isset($param['help'])) {
			$this->mostrar_ayuda_desatendida();
			return;
		}
		//--- Borra la instalacion anterior??
		if (toba_modelo_instalacion::existe_info_basica() ) {
			toba_modelo_instalacion::borrar_directorio();
		}

		//--- Crea la INSTALACION
		$alias = $this->definir_alias_nucleo($param);
		if ($alias ==  '/toba') {												//Si viene el alias por defecto, le agrego el nro de version
			$alias = $alias. '_' . toba_modelo_instalacion::get_version_actual()->get_release();
		}
		$id_desarrollo = (isset($param['-d'])) ? $param['-d'] : $this->definir_id_grupo_desarrollo();
		$tipo_instalacion = (isset($param['-t'])) ? $param['-t'] : $this->definir_tipo_instalacion_produccion();
		$nombre = (isset($param['-n'])) ? $param ['-n'] : $this->definir_nombre_instalacion();
		toba_modelo_instalacion::crear($id_desarrollo, $alias, $nombre, $tipo_instalacion);
		$id_instancia = $this->get_entorno_id_instancia(true);
		if (is_null($id_instancia) || trim($id_instancia) == '') {
			$id_instancia = ($tipo_instalacion == '1') ? 'produccion' : 'desarrollo';
		}

		//--- Crea la definicion de bases
		$base = $nombre_toba;
		$puerto = '5432';			//Asumo el puerto por defecto del servidor;
		$schema = $id_instancia;
		if (! $this->get_instalacion()->existe_base_datos_definida( $base ) ) {
			do {
				$profile = $this->recuperar_parametro($param, '-h',  'PostgreSQL - Ubicaci�n (ENTER utilizar� localhost)');
				if (trim($profile) == '') {
					$profile = 'localhost';
				}

				$puerto_tmp = $this->recuperar_parametro($param, '-p', "PostgreSQL - Puerto (ENTER utilizar�: $puerto)");
				if (trim($puerto_tmp) != '') {
					$puerto = $puerto_tmp;
				}

				$usuario = $this->recuperar_parametro($param, '-u',  'PostgreSQL - Usuario (ENTER utilizar� postgres)');
				if (trim($usuario) == '') {
					$usuario = 'postgres';
				}

				if (! isset($param['-c'])) {
					$clave = $this->consola->dialogo_ingresar_texto( 'PostgreSQL - Clave  (ENTER para usar sin clave)', false);
				} else {
					$clave = $this->recuperar_contenido_archivo($param['-c']);
				}

				$base_temp = $this->recuperar_parametro($param, '-b', "PostgreSQL - Base de datos (ENTER utilizar� $base)");
				if (trim($base_temp) != '') {
					$base = $base_temp;
				}

				$base_schema = $this->recuperar_parametro($param, '--schema-toba', "Nombre del schema a usar (ENTER utilizar� $id_instancia)");
				if (trim($base_schema) != '') {
					$schema = $base_schema;
				}

				$datos = array(
					'motor' => 'postgres7',
					'profile' => $profile,
					'usuario' => $usuario,
					'clave' => $clave,
					'base' => $base,
					'puerto' => $puerto,
					'encoding' => 'LATIN1',
					'schema' => $schema
				);
				$this->get_instalacion()->agregar_db( $base, $datos );
				$puede_conectar = true;
				if ($interactive) {
					//--- Intenta conectar al servidor
					$puede_conectar = $this->get_instalacion()->existe_base_datos($base, array('base' => 'template1'), true);
					if ($puede_conectar !== true) {
						$this->consola->mensaje("\nNo es posible conectar con el servidor, por favor reeingrese la informaci�n de conexi�n. Mensaje:");
						$this->consola->mensaje($puede_conectar . "\n");
						sleep(1);
					}
				}
			} while ($puede_conectar !== true);
		}
		//--- Pido el password para el usuario por defecto
		if (! isset($param['-k'])) {
			$pwd = $this->definir_clave_usuario_admin(false);           //El parametro es irrelevante solo esta por Strict
		} else {
			$pwd = $this->recuperar_contenido_archivo($param['-k']);
		}
		//--- Si la base existe, pregunta por un nombre alternativo, por si no quiere pisarla
		if ($interactive && $this->get_instalacion()->existe_base_datos($base, array(), false, $id_instancia)) {
			$nueva_base = $this->consola->dialogo_ingresar_texto("La base '$base' ya contiene un schema '$id_instancia', puede ingresar un nombre ".
																"de base distinto sino quiere sobrescribir los datos actuales: (ENTER sobrescribe la actual)", false);
			if ($nueva_base != '') {
				$datos['base'] = $nueva_base;
				$this->get_instalacion()->agregar_db( $base, $datos );
			}
		}

		//--- Crea la instancia
                $proyectos = $this->quitar_proyectos_sin_directorio(toba_modelo_proyecto::get_lista());
		toba_modelo_instancia::crear_instancia($id_instancia, $base, $proyectos);

		//-- Carga la instancia
		$instancia = $this->get_instancia($id_instancia);
		$instancia->cargar(true, ! $interactive);	//Si no es interactivo, crea siempre la BD

		//--- Vincula un usuario a todos los proyectos y se instala el proyecto
		$instancia->agregar_usuario( 'toba', 'Usuario Toba', $pwd);
		foreach( $instancia->get_lista_proyectos_vinculados() as $id_proyecto ) {
			$proyecto = $instancia->get_proyecto($id_proyecto);
			$grupo_acceso = $proyecto->get_grupo_acceso_admin();
			$proyecto->vincular_usuario('toba', array($grupo_acceso));
		}

		$instancia->exportar_local();

		//--- Crea los nuevos alias
		$instancia->crear_alias_proyectos();

		//--- Ejecuta instalaciones particulares de cada proyecto
		foreach( $instancia->get_lista_proyectos_vinculados() as $id_proyecto ) {
			$instancia->get_proyecto($id_proyecto)->instalar();
		}

		//--- Mensajes finales
		if ($interactive) {
			$this->consola->titulo("Configuraciones Finales");
			$toba_conf = toba_modelo_instalacion::dir_base() . "/toba.conf";
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
                        if (in_array('toba_editor', $instancia->get_lista_proyectos_vinculados())) {
                            $url = $instancia->get_proyecto('toba_editor')->get_url();
                            $this->consola->mensaje("Reiniciar el servicio apache e ingresar al framework navegando hacia ");
                            $this->consola->mensaje("");
                            $this->consola->mensaje("     http://localhost$url");
                        }
			$this->consola->mensaje("");
			$this->consola->mensaje("");
		}

		$release = toba_modelo_instalacion::get_version_actual()->get_release();
		$instal_dir = toba_modelo_instalacion::dir_base();
		$pos = posicion_ruta_vendor(toba_dir());

		//--Genero el archivo de entorno
		$path = $instal_dir. "/entorno_toba.env";
		file_put_contents($path, generar_archivo_entorno($instal_dir, $id_instancia));
		chmod($path, 0755);

		//Lo copio a la raiz del proyecto para cargarlo en el lanzamiento del comando	(solo si estoy dentro de vendor)
		if ($pos !== FALSE) {
			$path_copia = substr(toba_dir(), 0, $pos) . '/entorno_toba.env';
			copy($path, $path_copia);
		} else {
			$path_copia = toba_dir() . '/entorno_toba.env';
			copy($path, $path_copia);
		}

		//Si estoy en windows genero un archivo de entorno por si acaso.
		if (toba_manejador_archivos::es_windows()) {
			if (isset($_SERVER['USERPROFILE'])) {
				$pathw = $_SERVER['USERPROFILE'];
			} else {
				$pathw = $instal_dir;
			}
			$pathw .= "\\entorno_toba_$release.bat";
			file_put_contents($pathw, generar_archivo_entorno($instal_dir, $id_instancia, true));
			if ($interactive) {
				$this->consola->mensaje("2) Se genero el siguiente .bat:");
				$this->consola->mensaje("");
				$this->consola->mensaje("   $pathw");
				$this->consola->mensaje("");
				$this->consola->mensaje("Para usar los comandos toba ejecute el .bat desde una sesi�n de consola (cmd.exe)");
			}
		} else {
			if ($interactive) {
				$this->consola->mensaje("2) Se genero el siguiente archivo:");
				$this->consola->mensaje("");
				$this->consola->mensaje("   $path");
				$this->consola->mensaje("");
				$this->consola->mensaje("Para usar los comandos toba ejecute antes el .env precedido por un punto y espacio");
			}
		}
		if ($interactive) {
			$this->consola->mensaje("");
			$this->consola->mensaje("3) Entre otras cosas puede crear un nuevo proyecto ejecutando el comando");
			$this->consola->mensaje("");
			$this->consola->mensaje("   toba proyecto crear");
		}
	}

	/**
	* Crea una instalaci�n b�sica.
	* @gtk_icono nucleo/agregar.gif
	*/
	function opcion__crear()
	{
		if( ! toba_modelo_instalacion::existe_info_basica() ) {
			$this->consola->titulo( "Configurando INSTALACION en: " . toba_modelo_instalacion::dir_base() );
			$id_grupo_desarrollo = self::definir_id_grupo_desarrollo();
			$alias = self::definir_alias_nucleo();
			$tipo_instalacion = $this->definir_tipo_instalacion_produccion();
			$nombre = $this->definir_nombre_instalacion();
			toba_modelo_instalacion::crear( $id_grupo_desarrollo, $alias, $nombre, $tipo_instalacion);
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
	 * Muestra informaci�n de la instalaci�n.
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
				$this->consola->lista( $lista_proyectos, 'PROYECTOS (s�lo en la carpeta por defecto)' );
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
		$instalacion = $this->get_instalacion();
		$subject = $instalacion->es_produccion() ? "u" : "ug";

		$param = $this->get_parametros();
		$grupo = isset($param['-g']) ? $param['-g'] : null;
		$usuario = isset($param['-u']) ? $param['-u'] : 'www-data';
		$toba_dir = toba_dir();
		$instalacion_dir = $instalacion->get_path_carpeta_instalacion();
		$this->consola->subtitulo('Cambiando permisos de archivos navegables');
		if (isset($grupo)) {
			$usuario .= ":$grupo";
		}
		$comandos = array(
			array("chown -R $usuario $toba_dir/www", "Archivos navegables comunes:\n"),
			array("chmod -R $subject+rw $toba_dir/www", ''),
			array("chown -R $usuario $instalacion_dir", "Archivos de configuraci�n:\n"),
			array("chmod -R $subject+rw $instalacion_dir", ''),
			array("chown -R $usuario $toba_dir/temp", "Archivos temporales comunes:\n"),
			array("chmod $subject+rw $toba_dir/temp", '')
		);

		foreach ($instalacion->get_lista_instancias() as $id_inst) {
			$lista = $this->get_instancia($id_inst)->get_lista_proyectos_vinculados();
			foreach($lista as $proy) {
				$path = $this->get_instancia($id_inst)->get_path_proyecto($proy);
				var_dump($path); echo "\n";
				$comandos[] = array("chown -R $usuario $path/www", "Archivos navegables de $proy:\n");
				$comandos[] = array("chmod -R $subject+rw $path/www", '');
				$comandos[] = array("chown -R $usuario $path/temp", "Archivos temporales de $proy:\n");
				$comandos[] = array("chmod -R $subject+rw $path/temp", '');
			}
		}

		foreach ($comandos as $comando) {
			$this->consola->mensaje($comando[1], false);
			$this->consola->mensaje("   ".$comando[0]. exec($comando[0]));
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
	 * Cambia el n�mero de desarrollador y deja las instancias listas
                    * @consola_parametros Opcionales: [-d 'iddesarrollo'].
	 */
	function opcion__cambiar_id_desarrollador()
	{
		$id_grupo_desarrollador = $this->definir_id_grupo_desarrollo();
		$this->get_instalacion()->set_id_desarrollador($id_grupo_desarrollador);
	}


	/**
	 * Migra la instalaci�n de versi�n.
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
		$this->consola->titulo("Migraci�n de la instalaci�n actual".$texto_recursivo." desde la versi�n $desde_texto hacia la $hasta_texto.");

		$versiones = $desde->get_secuencia_migraciones($hasta);
		if (empty($versiones)) {
			$this->consola->mensaje("No es necesario ejecutar una migraci�n entre estas versiones");
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
			throw new toba_error("La instalaci�n ya se encuentra publicada. Debe despublicarla primero");
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
			throw new toba_error("La instalaci�n no se encuentra actualmente publicada.");
		}
	}

	//-------------------------------------------------------------
	// Interface
	//-------------------------------------------------------------

	/**
	*  Consulta al usuario el ID del grupo de desarrollo
	*/
	protected function definir_id_grupo_desarrollo()
	{
                            $param = $this->get_parametros();
                            $nombre_parametro = array( '-d', '--id-desarrollador', 'toba-id-desarrollador');
                            $mensaje = "Debe ser un entero positivo, mas info en http://repositorio.siu.edu.ar/trac/toba/wiki/Referencia/CelulaDesarrollo";

                            //Busco primeramente los parametros con modificadores
                            do {
                                    $ind = current($nombre_parametro);
                                    $es_invalido = (! isset($param[$ind]));
                                    if (! $es_invalido) {
                                            $id_desarrollo = $param[$ind];
                                            if (! is_numeric($id_desarrollo) || $id_desarrollo < 0) {
                                                    $es_invalido = true;
                                            }
                                    }
                            } while ($es_invalido && next($nombre_parametro) !== false);

                            //Termine de recorrer los posibles parametros  y sigue mal
                            if ( $es_invalido) {
                                    do {
                                            $es_invalido = false;
                                            $id_desarrollo = $this->consola->dialogo_ingresar_texto('Por favor, ingrese su n�mero de desarrollador (ENTER utilizar� 0)', false);

                                            if ($id_desarrollo == '') {
                                                    $id_desarrollo = 0;
                                            }
                                            if (! is_numeric($id_desarrollo) || $id_desarrollo < 0) {
                                                    $es_invalido = true;
                                                    $this->consola->mensaje($mensaje);
                                            }
                                    } while ($es_invalido);
                            }
                            $id_desarrollo = (int) $id_desarrollo;
                            return $id_desarrollo;
	}


	protected function definir_alias_nucleo()
	{
		$this->consola->enter();
		$this->consola->subtitulo('Definir el nombre del ALIAS del n�cleo Toba');
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
		$tipo_desarrollo = $this->consola->dialogo_simple('Se trata de una instalacion de producci�n?');
		return ($tipo_desarrollo) ? 1: 0;
	}

	protected function definir_nombre_instalacion()
	{
		return $this->consola->dialogo_ingresar_texto('Nombre de la instalaci�n (ej: Andromeda): ', true);
	}

	protected function definir_clave_usuario_admin($param)
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

        protected function quitar_proyectos_sin_directorio($proyectos)
        {
            $no_basicos = array('toba_testing', 'curso_intro');
            foreach ($proyectos as $id_pro => $path_pro) {
                $path = toba_dir().'/proyectos/'.$path_pro;
                if (! is_dir($path)) {
                    $no_basicos[] = $id_pro;
                }
            }

            $eliminar = \array_fill_keys($no_basicos, '1');
            return \array_diff_key($proyectos, $eliminar);
        }
}
?>
