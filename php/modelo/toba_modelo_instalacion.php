<?php
/**
 * Clase que representa la instalacion de toba
 * 
 * TODO Control de que la estructura de los INIs sea correcta 
 * @package Centrales
 * @subpackage Modelo
 */	
class toba_modelo_instalacion extends toba_modelo_elemento
{
	static protected $conexion_externa;
	const db_encoding_estandar = 'LATIN1';
	const info_basica = 'instalacion.ini';
	const info_basica_titulo = 'Configuracion de la INSTALACION';
	const info_bases = 'bases.ini';
	const info_bases_titulo = 'Configuracion de BASES de DATOS';
	private $dir;							// Directorio con info de la instalacion.
	private $ini_bases;						// Informacion de bases de datos.
	private $ini_instalacion;				// Informacion basica de la instalacion.
	private $ini_cargado = false;

	function __construct()
	{
		$this->dir = self::dir_base();
		toba_logger::instancia()->debug('INSTALACION "'.$this->dir.'"');
	}

	function cargar_info_ini($forzar_recarga=false)
	{
		if ($forzar_recarga || !$this->ini_cargado) {
			//--- Levanto la CONFIGURACION de bases
			$archivo_ini_bases = $this->dir . '/' . self::info_bases;
			if ( ! is_file( $archivo_ini_bases ) ) {
				throw new toba_error("INSTALACION: La instalacion '".toba_dir()."' es invalida. (El archivo de configuracion '$archivo_ini_bases' no existe)");
			} else {
				//  BASE
				$this->ini_bases = parse_ini_file( $archivo_ini_bases, true );
				$pendientes = array();
				foreach ($this->ini_bases as $id_base => $parametros) {
					if (empty($parametros)) {
						//Meterlos en una cola de bases que toman su definicion de la siguiente
						$pendientes[] = $id_base;
					} else {
						//Llenar la cola de pendientes con alias hacia la def. actual
						foreach ($pendientes as $id_base_pendiente) {
							self::$this->ini_bases[$id_base_pendiente] = $parametros;
						}
						$pendientes = array();
					}
				}				
			}
			//--- Levanto la CONFIGURACION de bases
			$archivo_ini_instalacion = $this->dir . '/' . self::info_basica;
			if ( ! is_file( $archivo_ini_instalacion ) ) {
				throw new toba_error("INSTALACION: La instalacion '".toba_dir()."' es invalida. (El archivo de configuracion '$archivo_ini_instalacion' no existe)");
			} else {
				//  BASE
				$this->ini_instalacion = parse_ini_file( $archivo_ini_instalacion,true);
			}
			$this->ini_cargado = true;
		}
	}

	//-----------------------------------------------------------
	//	Manejo de subcomponentes
	//-----------------------------------------------------------

	function get_instancia($id)
	{
		return toba_modelo_catalogo::instanciacion()->get_instancia($id, 
													$this->manejador_interface);		
	}
	
	function get_lista_instancias()
	{
		return toba_modelo_instancia::get_lista();
	}
	
	function existe_instancia($id)
	{
		return toba_modelo_instancia::existe_carpeta_instancia($id);
	}
	
	static function set_conexion_externa($base)
	{
	    self::$conexion_externa = $base;
	}
	
	
	//-------------------------------------------------------------
	//-- Informacion general
	//-------------------------------------------------------------
	
	/**
	 * Retorna el nombre de la instalacion actual, cadena vacia si no esta seteado
	 * @return string
	 */
	function get_nombre()
	{
		if (! $this->ini_cargado) {
			$this->cargar_info_ini();
		}
		
		if (isset($this->ini_instalacion['nombre'])) {
			return $this->ini_instalacion['nombre'];
		}
		return '';		
	}
	
	function get_dir()
	{
		return $this->dir;	
	}
	
	/**
	 * Retorna la ruta a la carpeta 'instalacion'
	 */
	function get_path_carpeta_instalacion()
	{
		return self::dir_base();
	}
	
	/**
	* Retorna el id que distingue al grupo de desarrollo
	*/
	function get_id_grupo_desarrollo()
	{
		$this->cargar_info_ini();		
		if (isset($this->ini_instalacion['id_grupo_desarrollo'])) {
			return $this->ini_instalacion['id_grupo_desarrollo'];
		} else {
			return null;
		}
	}

	/**
	 * @return toba_estandar_convenciones
	 */
	function get_estandar_convenciones()
	{
		return new toba_estandar_convenciones();
	}

	
	/**
	* Retorna true si la instalación es de producción (implementación)
	*/
	function es_produccion()
	{
		$this->cargar_info_ini();		
		if (isset($this->ini_instalacion['es_produccion'])) { 
			return $this->ini_instalacion['es_produccion'];
		} else {
			return false;
		}
	}
	
	/**
	* Retorna true si la instalación esta vinculada con Arai-Usuarios
	*/
	function vincula_arai_usuarios()
	{
		$this->cargar_info_ini();		
		if (isset($this->ini_instalacion['vincula_arai_usuarios'])) { 
			return $this->ini_instalacion['vincula_arai_usuarios'];
		} else {
			return false;
		}
	}
	
	/**
	 * Retorna si se debe realizar el chequeo de revisiones de metadatos desde toba_editor.
	 * Se usa el parametro 'chequea_sincro_svn' 0|1
	 * @return boolean $chequea
	 */
	function chequea_sincro_svn()
	{
		$this->cargar_info_ini();
		$chequea = false;
		if (isset($this->ini_instalacion['chequea_sincro_svn'])) {
			$chequea = ($this->ini_instalacion['chequea_sincro_svn'] == '1');
		}
		return $chequea;
	}	

	/**
	* Devuelve las claves utilizadas para encriptar
	*/
	function get_claves_encriptacion()
	{
		$this->cargar_info_ini();
		$claves['db'] = $this->ini_instalacion['clave_db'];
		$claves['get'] = $this->ini_instalacion['clave_querystring'];
		return $claves;
	}
	
	function get_archivos_certificado_ssl()
	{
		$this->cargar_info_ini();
		if (isset($this->ini_instalacion['cert'])) {
			return array($this->ini_instalacion['cert'], $this->ini_instalacion['key']);
		}
		return null;
	}
		
	function get_parametros_base( $id_base )
	{
		$this->cargar_info_ini();		
		if ( isset( $this->ini_bases[$id_base] ) ) {
			return $this->ini_bases[$id_base];			
		} else {
			throw new toba_error("INSTALACION: La base '$id_base' no existe en el archivo bases.ini");
		}
	}

	function existe_base_datos_definida( $id_base )
	{
		$this->cargar_info_ini();		
		return isset( $this->ini_bases[$id_base] );
	}

	function hay_bases()
	{
		$this->cargar_info_ini();		
		return count( $this->ini_bases ) > 0 ;
	}

	function get_lista_bases()
	{
		$this->cargar_info_ini();		
		return array_keys( $this->ini_bases );
	}

	function agregar_db( $id_base, $parametros )
	{
		self::validar_parametros_db($parametros);
		$ini = new toba_ini( self::archivo_info_bases() );
		$ini->agregar_titulo( self::info_bases_titulo );
		$ini->agregar_entrada( $id_base, $parametros );
		$ini->guardar();
		toba_logger::instancia()->debug("Agregada definicion base '$id_base'");		
		$this->ini_cargado=false;
	}
		
	/**
	 * Actualiza una entrada en el archivo bases.ini
	 */
	function actualizar_db($id_base, $parametros)
	{
		self::validar_parametros_db($parametros);
		$ini = new toba_ini( self::archivo_info_bases() );	
		if ($ini->existe_entrada($id_base)) {
			$ini->set_datos_entrada($id_base, $parametros);
		} else {
			$ini->agregar_titulo( self::info_bases_titulo );
			$ini->agregar_entrada( $id_base, $parametros );
		}
		$ini->guardar();
		$this->ini_cargado=false;		
		toba_logger::instancia()->debug("Actualizada definicion base '$id_base'");				
	}			
	

	function get_xslfo_fop()
	{
		if (isset($this->ini_instalacion['xslfo']) && isset($this->ini_instalacion['xslfo']['fop']) && $this->ini_instalacion['xslfo']['fop'] != '') {
			return $this->ini_instalacion['xslfo']['fop'];
		}
		return false;
	}

	//------------------------------------------------------------------------
	// Relacion con el MOTOR de base de datos
	//------------------------------------------------------------------------

	/**
	*	Conecta una base de datos definida en bases.ini
	*	@param string $nombre Nombre de la base
	* 	@return toba_db Objeto db resultante
	*/
	function conectar_base( $nombre )
	{
		toba_logger::instancia()->debug("Conectando a base '$nombre'");
		return $this->conectar_base_parametros( $this->get_parametros_base( $nombre ) );	
	}

	/**
	*	Crea una base de datos definida en bases.ini
	*	@param string $nombre Nombre de la base
	*	@param boolean $con_encoding Trata de generarle el encoding a la base, sino deja el del cluster
	*   @param boolean $cant_intentos Al tener postgres problemas de crear dos veces al mismo tiempo, existe la posibilidad de que justo otro proceso lo haga. Esto permite disminuir la posibiilidad
	*/
	function crear_base_datos($nombre, $con_encoding = false, $cant_intentos = 3)
	{
		$info_db = $this->get_parametros_base( $nombre );
		$base_a_crear = $info_db['base'];
		if ($info_db['motor']=='postgres7') {
			dormir(1000);    //Para esperar que el script se desconecte
			$info_db['base'] = 'template1';
			$db = $this->conectar_base_parametros($info_db);
			$encoding = isset($info_db['encoding']) ? $info_db['encoding'] : self::db_encoding_estandar;
			$sql = "CREATE DATABASE \"$base_a_crear\" ";
			if ($con_encoding) {
				$sql .= " ENCODING '$encoding' ";
			}
			$intentos = 0;
			do {
				$intentos++;
				try {
					$db->ejecutar($sql);
					break;
				} catch (toba_error_db $e) {
					if ($intentos < $cant_intentos) {
						$this->manejador_interface->mensaje("Error al crear la base, reintentando una vez mas por si hay concurrencia en la creacion..\n");
						$db->destruir();
						dormir(2000);
						$db = $this->conectar_base_parametros($info_db);
					} else {
						throw $e;
					}
				}
			} while ($intentos <= $cant_intentos);

			$db->destruir();
			toba_logger::instancia()->debug("Creada base '$base_a_crear'");
		} else {
			throw new toba_error("INSTALACION: El metodo no esta definido para el motor especificado");
		}
	}

	/**
	*	Borra una base de datos definida en bases.ini
	*	@param string $nombre Nombre de la base
	*/	
	function borrar_base_datos( $nombre )
	{
		$info_db = $this->get_parametros_base( $nombre );
		$base_a_borrar = $info_db['base'];
		if($info_db['motor']=='postgres7')
		{
			dormir(1000);	//Para esperar que el script se desconecte
			$info_db['base'] = 'template1';
			$db = $this->conectar_base_parametros( $info_db );
			$sql = "DROP DATABASE \"$base_a_borrar\";";
			toba_logger::instancia()->debug("Borrada base '$base_a_borrar'");			
			$db->ejecutar($sql);
			$db->destruir();
		}else{
			throw new toba_error("INSTALACION: El metodo no esta definido para el motor especificado");
		}
	}

	/**
	*	Determina si una base de datos definida en bases.ini existe
	*	@param string $nombre Nombre de la base
	*/
	function existe_base_datos( $nombre, $otra_info = array(), $mostrar_salida = false, $schema=null )
	{
		try{
			$this->ini_cargado = false;
			$info_db = $this->get_parametros_base( $nombre );
			$info_db = array_merge($info_db, $otra_info);
			if (! $mostrar_salida) {
				$db = @$this->conectar_base_parametros( $info_db );
			} else {
				$db = $this->conectar_base_parametros( $info_db );
			}
			if (isset($schema) && !$db->existe_schema($schema)) {
				return false;
			}
			$db->destruir();
			return true;
		}catch(toba_error $e){
			if ($mostrar_salida) {
				return $e->getMessage();
			}
			return false;
		}
	}

	/**
	*	Conecta una BASE a partir de un juego de parametros
	*	@param array $parametros Parametros de conexion
	* @return toba_db_postgres7
	*/
	function conectar_base_parametros( $parametros )
	{
		if (! isset(self::$conexion_externa)) {
			$logger = toba_logger::instancia();
			$clase = "toba_db_" . $parametros['motor'];
			$db = new $clase(	$parametros['profile'],
							$parametros['usuario'],
							$parametros['clave'],
							$parametros['base'],
							isset($parametros['puerto']) ? $parametros['puerto'] : '' );
			$db->set_logger($logger);
			$db->conectar();
			if (isset($parametros['schema'])) {
				try {
					$db->set_schema($parametros['schema']);
				} catch (toba_error_db $error) {
					$logger->warning("No pudo cambiarse la sesion postgres al schema '{$parametros['schema']}' porque el mismo no existe");
				}
			}
			//Si existe el parametro del encoding, ponerlo por defecto para la conexión
			if (isset($parametros['encoding'])) {
				$db->set_encoding($parametros['encoding']);
			}		
			$datos_base = var_export($parametros, true);
			$logger->debug("Parametros de conexion: $datos_base");
			return $db;
		} else {
		    return self::$conexion_externa;
		}
	} 


	/**
	*	Determina si el Encoding estandar es compatible con el cluster, actualiza la entrada de bases.ini en consecuencia
	*	@param string $nombre Nombre de la base
	*/	
	function determinar_encoding($id_base)
	{
		/*
		* Trata de crear la base con el encoding por defecto, si falla entonces intenta crearla sin encoding, en caso de
		* que funcione la creacion actualiza la entrada en bases.ini dejando el encoding a usar en la conexion.
		*/
		
		$this->manejador_interface->mensaje("Determinando Encoding de base de datos... \n");
		if (! $this->existe_base_datos($id_base)) {
			try{
				$this->crear_base_datos($id_base, true);	
				toba_logger::instancia()->debug("Base: $id_base -> Encoding estandar compatible!: ". self::db_encoding_estandar );
			} catch(toba_error $e) {				
				$this->crear_base_datos($id_base, false);
				$info_db = $this->get_parametros_base($id_base);
				$nuevos_parametros = array('encoding' => self::db_encoding_estandar);												
				$info_db = array_merge($info_db, $nuevos_parametros);				
				$this->actualizar_db($id_base, $info_db);
				toba_logger::instancia()->info("Base: $id_base -> Encoding no compatible!, redefiniendo conexion para uso con: ". self::db_encoding_estandar );
			}//try
					
			//--- Borro la base de datos recien creada
			dormir(1000);
			$this->borrar_base_datos($id_base);	
			$this->manejador_interface->mensaje("El Encoding ha sido definido, revise el archivo bases.ini \n");
		}else{
			$this->manejador_interface->mensaje("La base ya existe, no se puede determinar el encoding \n");
		}//if
	}
	
	//-------------------------------------------------------------------------
	//-- Funcionalidad estatica relacionada a la CREACION de INSTALACIONES
	//-------------------------------------------------------------------------

	static function crear( $id_grupo_desarrollo, $alias_nucleo , $nombre, $es_produccion = 0)
	{
		self::crear_directorio();
		self::actualizar_version( toba_modelo_instalacion::get_version_actual() );
		$apex_clave_get = md5(uniqid(rand(), true)); 
		$apex_clave_db = md5(uniqid(rand(), true)); 
		$editor = toba_manejador_archivos::es_windows() ? 'start' : '';
		self::crear_info_basica( $apex_clave_get, $apex_clave_db, $id_grupo_desarrollo, $editor, $alias_nucleo, $nombre, $es_produccion);
		copy(toba_dir(). '/php/modelo/var/smtp.ini',	self::dir_base().'/smtp.ini');
		copy(toba_dir(). '/php/modelo/var/ldap.ini', 	self::dir_base().'/ldap.ini');
		copy(toba_dir(). '/php/modelo/var/openid.ini', 	self::dir_base().'/openid.ini');
		copy(toba_dir(). '/php/modelo/var/cas.ini', 	self::dir_base().'/cas.ini');
		copy(toba_dir(). '/php/modelo/var/rdi.ini', 	self::dir_base().'/rdi.ini');
		copy(toba_dir(). '/php/modelo/var/saml_onelogin.ini', 	self::dir_base().'/saml_onelogin.ini');	
		
		//Se genera archivo configuracion saml
		copy(toba_dir(). '/php/modelo/var/saml.ini', 	self::dir_base().'/saml.ini');		
		$template = file_get_contents(toba_dir(). '/php/modelo/var/saml.ini');
		$editor = new toba_editor_archivos();
		$editor->agregar_sustitucion( '|__toba_alias__|', $alias_nucleo);
		$editor->procesar_archivo(self::dir_base().'/saml.ini' );
				
		//Se genera archivo configuracion openssl
		$template = file_get_contents(toba_dir(). '/php/modelo/var/openssl.ini');
		$editor = new toba_editor_texto();
		$editor->agregar_sustitucion( '|__password__|', md5(uniqid(rand(), true)));
		$salida = $editor->procesar( $template );
		file_put_contents(self::dir_base().'/openssl.ini', $salida);
				
		self::crear_info_bases();
		self::crear_directorio_proyectos();
		self::crear_archivo_apache($alias_nucleo);
		
	}
	
	static function crear_archivo_apache($alias_nucleo)
	{
		$archivo = self::get_archivo_alias_apache();
		copy( toba_dir(). '/php/modelo/var/toba.conf', $archivo );
		$editor = new toba_editor_archivos();
		$editor->agregar_sustitucion( '|__toba_dir__|', toba_manejador_archivos::path_a_unix( toba_dir() ) );		
		$editor->agregar_sustitucion( '|__toba_alias__|', $alias_nucleo ); 
		$editor->agregar_sustitucion( '|__instalacion_dir__|', toba_manejador_archivos::path_a_unix(self::dir_base()));
		$editor->procesar_archivo( $archivo );
	}
	
	
	static function get_archivo_alias_apache()
	{
		return self::dir_base() . '/toba.conf';
	}
	
	/**
	 * Agrega al archivo toba.conf la definicion del proyecto
	 */
	static function agregar_alias_apache($alias, $dir, $instancia, $id_proyecto, $pers = false)
	{
		$archivo = self::get_archivo_alias_apache();
				
		//--- Se agrega el proyecto al archivo
		if ($pers) {
			$template = file_get_contents(toba_dir(). '/php/modelo/var/proyecto_pers.conf');
		} else {
			$template = file_get_contents(toba_dir(). '/php/modelo/var/proyecto.conf');
		}
		
		$editor = new toba_editor_texto();
		$editor->agregar_sustitucion( '|__toba_dir__|', toba_manejador_archivos::path_a_unix( toba_dir() ) );		
		$editor->agregar_sustitucion( '|__proyecto_dir__|', toba_manejador_archivos::path_a_unix($dir) );
		$editor->agregar_sustitucion( '|__proyecto_alias__|', $alias ); 
		$editor->agregar_sustitucion( '|__proyecto_id__|', $id_proyecto); 
		$editor->agregar_sustitucion( '|__instancia__|', $instancia );
		$editor->agregar_sustitucion( '|__instalacion_dir__|', toba_manejador_archivos::path_a_unix(self::dir_base()));
		$salida = $editor->procesar( $template );
		file_put_contents($archivo, $salida, FILE_APPEND);
	}
	
	static function existe_alias_apache($id_proyecto, $pers = false)
	{
		$archivo = self::get_archivo_alias_apache();
		$conf = file_get_contents($archivo);
		if ($pers) {
			$encontre = preg_match('/^(?:\s)*#Proyecto_pers:(?:\s)*'.$id_proyecto.'/im', $conf);
		} else {
			$encontre = preg_match('/^(?:\s)*#Proyecto:(?:\s)*'.$id_proyecto.'/im', $conf);
		}
		
		return ($encontre !== 0 && $encontre !== false);
	}
	
	static function quitar_alias_apache($id_proyecto, $pers = false)
	{
		$archivo = self::get_archivo_alias_apache();
		$conf = file_get_contents($archivo);
        
		if ($pers) {
			$str_inicio = '#Proyecto_pers: '.$id_proyecto;
		} else {
			$str_inicio = '#Proyecto: '.$id_proyecto;
		}
		
		$str_fin = '</Directory>';
		$inicio = strpos($conf, $str_inicio);
		if ($inicio !== false) {
			$fin = strpos($conf, $str_fin, $inicio) + strlen($str_fin);
			if ($fin !== false) {
				$salida = rtrim(substr($conf, 0, $inicio)) . substr($conf, $fin);
				file_put_contents($archivo, $salida);
			} else {
				throw new toba_error('No es posible encontrar el fin del alias');
			}
		} else {
			throw new toba_error('No es posible encontrar el inicio del alias');
		}
	}
	

	static function dir_base()
	{
		if (isset($_SERVER['TOBA_INSTALACION_DIR'])) {
			return $_SERVER['TOBA_INSTALACION_DIR'];
		} /*elseif (isset($_SERVER['toba_instalacion_dir'])) {
			return $_SERVER['toba_instalacion_dir'];
		}*/ else {
			return toba_dir().'/instalacion';
		}
	}

	/**
	* Crea el directorio de la instalacion
	*/
	static function crear_directorio()
	{
		if( ! is_dir( self::dir_base() ) ) {
			mkdir( self::dir_base() );
			toba_logger::instancia()->debug("Creado directorio ".self::dir_base());			
		}
	}
	
	static function borrar_directorio()
	{
		if (is_dir( self::dir_base() ) ) {
			toba_manejador_archivos::eliminar_directorio(self::dir_base());
			toba_logger::instancia()->debug("Borrado directorio ".self::dir_base());			
		}		
	}
	
	function eliminar_logs()
	{
		$dir = self::dir_base().'/logs_comandos';
		$this->manejador_interface->mensaje('Eliminando logs', false);
		if (file_exists($dir)) {
			toba_manejador_archivos::eliminar_directorio($dir);
			$this->manejador_interface->progreso_avanzar();
		}
		foreach ($this->get_lista_instancias() as $id_inst) {
			$instancia = $this->get_instancia($id_inst);
			$instancia->eliminar_logs();
		}
		$this->manejador_interface->progreso_fin();		
		foreach ($this->get_lista_instancias() as $id_inst) {
			$instancia = $this->get_instancia($id_inst);
			$instancia->exportar_local();
		}		
	}
	

	/**
	* Crea el directorio de proyectos
	*/
	static function crear_directorio_proyectos()
	{
		$dir = toba_dir() .'/proyectos';
		if( ! is_dir( $dir ) ) {
			mkdir( $dir );
			toba_logger::instancia()->debug("Creado directorio $dir");			
		}		
	}
	
	/**
	 * Retorna los nombres de los directorios contenidos en la carpeta proyectos
	 */
	static function get_lista_proyectos()
	{
		$dir = toba_dir() .'/proyectos';
		return toba_manejador_archivos::get_subdirectorios($dir);
	}
	
	//-- Archivo de CONFIGURACION de la INSTALACION  --------------------------------------

	/**
	* Crea el archivo con la informacion basica sobre la instalacion	
	*/
	static function crear_info_basica($clave_qs, $clave_db, $id_grupo_desarrollo, $editor, $url, $nombre_inst, $es_produccion = 0)
	{
		$ini = new toba_ini();
		$ini->agregar_titulo( self::info_basica_titulo );
		$ini->agregar_entrada('nombre', $nombre_inst);
		$ini->agregar_entrada( 'id_grupo_desarrollo', $id_grupo_desarrollo );
		$ini->agregar_entrada( 'clave_querystring', $clave_qs );	
		$ini->agregar_entrada( 'clave_db', $clave_db );	
		$ini->agregar_entrada( 'editor_php', $editor );
		$ini->agregar_entrada( 'url', $url );
		$ini->agregar_entrada( 'es_produccion', $es_produccion);
		$ini->agregar_entrada( ';autenticacion', 'toba|openid|ldap|cas|saml|saml_onelogin');
		$ini->agregar_entrada( ';session_name', 'TOBA_SESSID');		
		if (!toba_manejador_archivos::es_windows()) {
			$ini->agregar_entrada(';fonts_path', '/usr/share/fonts/truetype/');
		}
		$ini->guardar( self::archivo_info_basica() );
		toba_logger::instancia()->debug("Creado archivo ".self::archivo_info_basica());
	}
	
	/**
	 * Cambia o agrega algunos parametros al archivo de información de la instalación
	 * @param array $datos clave => valor
	 */
	function cambiar_info_basica($datos)
	{
		$ini = new toba_ini(self::archivo_info_basica());
		foreach ($datos as $entrada => $valor) {
			if ($ini->existe_entrada($entrada)) {
				$ini->set_datos_entrada($entrada, $valor);
			} else {
				$ini->agregar_entrada($entrada, $valor);
			}
		}
		$ini->guardar();
	}
	
	/**
	* Indica si el archivo de informacion basica existe
	*/
	static function existe_info_basica()
	{
		return ( is_file( self::archivo_info_basica() ) );
	}

	/**
	* path del archivo con informacion basica
	*/
	static function archivo_info_basica()
	{
		return self::dir_base() . '/' . self::info_basica;
	}
	
	//-- Archivo de CONFIGURACION de BASES  -----------------------------------------------

	/**
	* Crea el archivo con la lista de bases disponibles
	*/
	static function crear_info_bases( $lista_bases = array() )
	{
		$ini = new toba_ini();
		$ini->agregar_titulo( self::info_bases_titulo );
		foreach( $lista_bases as $id => $base ) {
			//Valido que la definicion sea correcta
			if( isset( $base['motor'] ) &&
				isset( $base['profile'] ) &&
				isset( $base['usuario'] ) &&
				isset( $base['clave'] ) &&
				isset( $base['base'] ) ) {
				$ini->agregar_entrada( $id, $base );	
			} else {
				throw new toba_error("La definicion de la BASE '$id' es INCORRECTA.");	
			}
		}
		$ini->guardar( self::archivo_info_bases() );
		toba_logger::instancia()->debug("Creado archivo ".self::archivo_info_bases());		
	}
	
	static private function validar_parametros_db($parametros)
	{
		if ( ! is_array( $parametros ) ) {
			throw new toba_error("INSTALACION: Los parametros definidos son incorrectos");	
		} else {
			// Estan todos los parametros
			if ( !isset( $parametros['motor']  )
				|| !isset( $parametros['profile'] ) 
				|| !isset( $parametros['usuario'] )
				|| !isset( $parametros['base'] ) ) {
				throw new toba_error("INSTALACION: Los parametros definidos son incorrectos");	
			}
			// El motor es reconocido
			$motores = array('postgres7', 'informix', 'mysql', 'odbc', 'sqlserver');
			if( ! in_array( $parametros['motor'], $motores ) ) {
				throw new toba_error("INSTALACION: El motor tiene que pertenecer a la siguente lista: " . implode(', ',$motores) );	
			}
		}		
	}
	
	static function eliminar_db( $id_base )
	{
		$ini = new toba_ini( self::archivo_info_bases() );
		$ini->agregar_titulo( self::info_bases_titulo );
		$ini->eliminar_entrada( $id_base );
		$ini->guardar();
		toba_logger::instancia()->debug("Eliminada definicion base '$id_base'");				
	}
	
	function existe_info_bases()
	{
		return ( is_file( self::archivo_info_bases() ) );
	}

	static function archivo_info_bases()
	{
		return self::dir_base() . '/' . self::info_bases;
	}
	
	//-- Archivo de configuracion de alias

	function publicar()
	{
		if (! $this->esta_publicado()) {
			$this->cargar_info_ini();
			if (isset($this->ini_instalacion['url'])) {
				$url = $this->ini_instalacion['url'];
			} else {
				$url = 'toba_'.self::get_version_actual()->get_string_partes();
			}
			self::crear_archivo_apache($url);
			foreach ($this->get_lista_instancias() as $instancia) {
				$this->get_instancia($instancia)->crear_alias_proyectos();
			}			
		}
	}
	
	function despublicar()
	{
		if ($this->esta_publicado()) {
			$archivo = $this->get_archivo_alias_apache();
			file_put_contents($archivo, '');
		}
	}
	
	function esta_publicado()
	{
		$archivo = $this->get_archivo_alias_apache();
		if (! file_exists($archivo)) {
			return false;
		}
		if (trim(file_get_contents($archivo)) == '') {
			return false;
		}
		return true;
	}	
	
	
	/**
	 * Cambia el id de desarrollo y deja las instancias listas para trabajar
	 */
	function set_id_desarrollador($id)
	{
		$this->cambiar_info_basica(array('id_grupo_desarrollo' => $id));	
		foreach ($this->get_lista_instancias() as $id_inst) {
			$instancia = $this->get_instancia($id_inst);
			$instancia->actualizar_secuencias();
		}
	}
	
	function empaquetar_en_carpeta($destino, $librerias_en_uso = array(), $proyectos_en_uso = array())
	{
		$path_base = toba_dir();
		$excepciones = $this->get_lista_excepciones_instalacion($path_base, $librerias_en_uso, $proyectos_en_uso);		
		//Carpeta php
		toba_manejador_archivos::crear_arbol_directorios( $destino);
		toba_manejador_archivos::copiar_directorio($path_base, $destino, 
													$excepciones, $this->manejador_interface, false);

		//Crea un archivo revision con la actual de toba
		file_put_contents($destino.'/REVISION', revision_svn(toba_dir(), true));
	}
	
	function get_lista_excepciones_instalacion($path_base, $librerias_en_uso, $proyectos_en_uso)
	{
		$excepciones = array();
		$excepciones[] = $path_base.'/doc';
		$excepciones[] = $path_base.'/instalacion';
		$excepciones[] = $path_base.'/var';
		
		//Excepciones de php/3eros
		foreach (toba_manejador_archivos::get_subdirectorios($path_base.'/php/3ros') as $libreria) {
			if (! in_array(basename($libreria), $librerias_en_uso)) {
				$excepciones[] = $libreria;
			}
		}
		//Excepciones de www/js
		$candidatas = array('fckeditor', 'junit', 'yui');
		foreach (toba_manejador_archivos::get_subdirectorios($path_base.'/www/js') as $libreria) {
			$nombre = basename($libreria);
			if (in_array($nombre, $candidatas) && !in_array($nombre, $librerias_en_uso)) {
				$excepciones[] = $libreria;
			}
		}		
		
		//Excepciones de proyectos
		foreach (toba_manejador_archivos::get_subdirectorios($path_base.'/proyectos') as $proyecto) {
			$nombre = basename($proyecto);
			if (!in_array($nombre, $proyectos_en_uso)) {
				$excepciones[] = $proyecto;
			}
		}		
		return $excepciones;
	}
	
	//------------------------------------------------------------------------
	//-------------------------- Manejo de Versiones -------------------------
	//------------------------------------------------------------------------
	
	/**
	 * Toma un proyecto de una instancia de un toba en un versión anterior e importa el mismo a esta versión
	 * La instancia origen se debe llamar igual que la destino
	 * @param string $id_instancia Instancia origen/destino de la migración
	 * @param string $id_proyecto Proyecto origen/destino de la migración
	 * @param string $dir_toba_viejo Directorio del toba que contiene la instancia/proyecto a migrar
	 * @param string $destino Directorio del toba que contiene el proyecto actual
	 */
	function importar_migrar_proyecto($id_instancia, $id_proyecto, $dir_toba_viejo, $destino=null)
	{
		//$path_proyecto = toba_dir().'/proyectos/'.$id_proyecto;		
		$excepcion = null;
		$url = null;
		$dir_original = $this->get_dir();
		$dir_backup = $dir_original.'.'.date('YmdHms');
		try {
			//--- Hacer un backup del directorio actual
			$this->manejador_interface->titulo("1.- Haciendo backup directorio instalacion del nuevo toba");	
			if (file_exists($dir_original)) {
				if (! toba_manejador_archivos::copiar_directorio($dir_original, $dir_backup)) {
					throw new toba_error("No es posible hacer una copia de seguridad de la carpeta '$dir_original'. Verifique los permisos de escritura del usuario actual");
				}
			}
			
			//--- Traer configuraciones de la instancia vieja
			$instancia = $this->get_instancia($id_instancia);
											
			$archivo_ini_bases = $dir_toba_viejo.'/instalacion/bases.ini';
			if (! file_exists($archivo_ini_bases)) {
				throw new toba_error("No se encuentra el archivo $archivo_ini_bases");
			}
			$archivo_instancia = $dir_toba_viejo."/instalacion/i__$id_instancia/instancia.ini";
			if (! file_exists($archivo_instancia)) {
				throw new toba_error("No se encuentra el archivo $archivo_instancia");
			}
			$conf_instancia = parse_ini_file($archivo_instancia, true);
			$id_base_instancia = $instancia->get_ini_base();
			if (isset($conf_instancia['base'])) {
				$id_base_instancia = $conf_instancia['base'];
			}
			$bases_viejas = parse_ini_file($archivo_ini_bases, true);
			if (! isset($bases_viejas[$id_base_instancia])) {
				throw new toba_error("No se encuentra la definición de la instancia $id_base_instancia en el archivo $archivo_ini_bases");
			} 

			//--- Incluir solo el proyecto a importar en la instancia
			$this->manejador_interface->titulo("2.- Apuntando la instancia nueva a la de la versión anterior");
			$instancia->get_lista_proyectos_vinculados();
			$instancia->set_proyectos_vinculados(array($id_proyecto));
			$path_proyecto = (is_null($destino)) ? $instancia->get_path_proyecto($id_proyecto): $destino;			
			$instancia->vincular_proyecto($id_proyecto, $path_proyecto, $url);			

			//--- Apuntar la instancia actual a la instancia externa			
			$this->actualizar_db($instancia->get_ini_base(), $bases_viejas[$id_base_instancia]);
			$this->cargar_info_ini(true);

			//--- Migrar la instancia vieja
			$this->manejador_interface->titulo("3.- Migrando el proyecto de versión toba");

			$instancia->get_db()->destruir();
			$instancia->get_db(true);	//Refresca la base			
			$desde = $instancia->get_version_actual();
			$hasta = toba_modelo_instalacion::get_version_actual();		
			$instancia->get_db()->abrir_transaccion();	
			$instancia->migrar_rango_versiones($desde, $hasta, 1, false);
			$instancia->get_proyecto($id_proyecto)->exportar();
			$instancia->get_db()->abortar_transaccion();	//Aborta la transaccion para que no afecte la instancia vieja
			$instancia->get_db()->destruir();

	
			//---Restaurar el backup
			if (file_exists($dir_backup)) {
				$this->manejador_interface->titulo("4.- Restaurando backup directorio instalacion del nuevo toba");	
				if (file_exists($dir_original)) {
					toba_manejador_archivos::eliminar_directorio($dir_original);
				}
				rename($dir_backup, $dir_original);
			} else {
				throw new toba_error('Imposible restaurar el estado previo a la migración');
			}
			
			//--- Agrega el proyecto a la instancia nueva (por si no estaba) y regenera la misma
			$this->manejador_interface->titulo("5.- Regenerando la instancia actual para tomar los cambios");			
			$this->cargar_info_ini(true);
			$instancia->cargar_info_ini();
			$instancia->get_db(true);	//Refresca la base
			//Si existe una entrada actual en el instancias.ini viejo, replicarla en el actual
			$instancia->vincular_proyecto($id_proyecto, $path_proyecto, $url);
			$proyecto = $instancia->get_proyecto($id_proyecto);
			$proyecto->regenerar();
			
		} catch (Exception  $e) {
			$excepcion = $e;
			//---Restaurar el backup
			if (file_exists($dir_backup)) {
				$this->manejador_interface->titulo("Restaurando backup directorio instalacion del nuevo toba");	
				if (file_exists($dir_original)) {
					if (! toba_manejador_archivos::eliminar_directorio($dir_original)) {
						throw new toba_error("Imposible restaurar backup desde '$dir_backup' hacia '$dir_original', deberá hacerlo manualmente.");
					}
				}
				rename($dir_backup, $dir_original);
			}
			throw $excepcion;
		}		
	}
	
	function migrar_version($version, $recursivo)
	{
		toba_logger::instancia()->debug("Migrando instalación hacia version ".$version->__toString());
		$this->manejador_interface->mensaje("Migrando instalación.", false);
		$version->ejecutar_migracion('instalacion', $this, null, $this->manejador_interface);
		$this->manejador_interface->progreso_fin();
		
		//-- Se migran las instancias incluidas		
		if ($recursivo) {
			foreach ($this->get_lista_instancias() as $instancia) {
				$this->get_instancia($instancia)->migrar_version($version,$recursivo);
			}
		}
		$this->actualizar_version($version);		
	}
	
	private static function actualizar_version($version)
	{
		$numero = $version->__toString();
		file_put_contents(self::dir_base()."/VERSION", $numero );
		toba_logger::instancia()->debug("Actualizada instalación a versión $numero");
	}
	
	/**
	 * @return toba_version 
	 */
	static function get_version_actual()
	{
		return new toba_version(file_get_contents(toba_dir()."/VERSION"));
	}
	
	function get_version_anterior()
	{
		if (file_exists($this->dir_base()."/VERSION")) {
			return new toba_version(file_get_contents($this->dir_base()."/VERSION"));
		} else {
			return toba_version::inicial();
		}
	}	

}
?>
