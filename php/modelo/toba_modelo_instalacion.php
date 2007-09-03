<?php
/**
*	@todo:	Control de que la estructura de los INIs sea correcta
*/
class toba_modelo_instalacion extends toba_modelo_elemento
{
	const db_encoding_estandar = 'LATIN1';
	const directorio_base = 'instalacion';
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
				$this->ini_instalacion = parse_ini_file( $archivo_ini_instalacion );
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
	
	
	//-------------------------------------------------------------
	//-- Informacion general
	//-------------------------------------------------------------
	
	function get_dir()
	{
		return $this->dir;	
	}
	
	/**
	* Devuelve la lista de las INSTANCIAS
	*/
	function get_id_grupo_desarrollo()
	{
		$this->cargar_info_ini();		
		return $this->ini_instalacion['id_grupo_desarrollo'];
	}

	/**
	* Devuelve las claves utilizadas para encriptar
	*/
	function get_claves_encriptacion()
	{
		$this->cargar_info_ini();
		$claves['db'] = $this->ini_instalacion['clave_querystring'];
		$claves['get'] = $this->ini_instalacion['clave_db'];
		return $claves;
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
	*/
	function crear_base_datos( $nombre )
	{
		$info_db = $this->get_parametros_base( $nombre );
		$base_a_crear = $info_db['base'];
		if($info_db['motor']=='postgres7')
		{
			dormir(1000);	//Para esperar que el script se desconecte			
			$info_db['base'] = 'template1';
			$db = $this->conectar_base_parametros( $info_db );
			$sql = "CREATE DATABASE \"$base_a_crear\" ENCODING '" . self::db_encoding_estandar . "';";
			$db->ejecutar( $sql );
			$db->destruir();
			toba_logger::instancia()->debug("Creada base '$base_a_crear'");
		}else{
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
	function existe_base_datos( $nombre, $otra_info = array(), $mostrar_salida = false )
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
	*/
	function conectar_base_parametros( $parametros )
	{
		$clase = "toba_db_" . $parametros['motor'];
		$db = new $clase(	$parametros['profile'],
							$parametros['usuario'],
							$parametros['clave'],
							$parametros['base'],
							isset($parametros['puerto']) ? $parametros['puerto'] : '' );
		$db->conectar();
		$datos_base = var_export($parametros, true);
		toba_logger::instancia()->debug("Parametros de conexion: $datos_base");
		return $db;
	}

	//-------------------------------------------------------------------------
	//-- Funcionalidad estatica relacionada a la CREACION de INSTALACIONES
	//-------------------------------------------------------------------------

	static function crear( $id_grupo_desarrollo, $alias_nucleo )
	{
		toba_modelo_instalacion::crear_directorio();
		toba_modelo_instalacion::actualizar_version( toba_modelo_instalacion::get_version_actual() );
		$apex_clave_get = md5(uniqid(rand(), true)); 
		$apex_clave_db = md5(uniqid(rand(), true)); 
		$editor = toba_manejador_archivos::es_windows() ? 'start' : '';
		toba_modelo_instalacion::crear_info_basica( $apex_clave_get, $apex_clave_db, $id_grupo_desarrollo, $editor, $alias_nucleo );
		toba_modelo_instalacion::crear_info_bases();
		toba_modelo_instalacion::crear_directorio_proyectos();
		self::crear_archivo_apache($alias_nucleo);
	}
	
	static function crear_archivo_apache($alias_nucleo)
	{
		$archivo = self::get_archivo_alias_apache();
		copy( toba_dir(). '/php/modelo/var/toba.conf', $archivo );
		$editor = new toba_editor_archivos();
		$editor->agregar_sustitucion( '|__toba_dir__|', toba_manejador_archivos::path_a_unix( toba_dir() ) );		
		$editor->agregar_sustitucion( '|__toba_alias__|', $alias_nucleo ); 
		$editor->procesar_archivo( $archivo );
	}
	
	
	static function get_archivo_alias_apache()
	{
		return self::dir_base() . '/toba.conf';
	}
	
	/**
	 * Agrega al archivo toba.conf la definicion del proyecto
	 */
	static function agregar_alias_apache($alias, $dir, $instancia)
	{
		$archivo = self::get_archivo_alias_apache();
				
		//--- Se agrega el proyecto al archivo
		$template = file_get_contents(toba_dir(). '/php/modelo/var/proyecto.conf');
		$editor = new toba_editor_texto();
		$editor->agregar_sustitucion( '|__toba_dir__|', toba_manejador_archivos::path_a_unix( toba_dir() ) );		
		$editor->agregar_sustitucion( '|__proyecto_dir__|', toba_manejador_archivos::path_a_unix($dir) );
		$editor->agregar_sustitucion( '|__proyecto_alias__|', $alias ); 
		$editor->agregar_sustitucion( '|__instancia__|', $instancia );
		$salida = $editor->procesar( $template );
		$salida = "\n\t#Creado automáticamente por Toba - ".date('d/m/y H:m:s').$salida;
		file_put_contents($archivo, $salida, FILE_APPEND);
	}

	static function dir_base()
	{
		return toba_dir() . '/' . self::directorio_base;
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
	static function crear_info_basica($clave_qs, $clave_db, $id_grupo_desarrollo, $editor, $url)
	{
		$ini = new toba_ini();
		$ini->agregar_titulo( self::info_basica_titulo );
		$ini->agregar_entrada( 'id_grupo_desarrollo', $id_grupo_desarrollo );
		$ini->agregar_entrada( 'clave_querystring', $clave_qs );	
		$ini->agregar_entrada( 'clave_db', $clave_db );	
		$ini->agregar_entrada( 'editor_php', $editor );
		$ini->agregar_entrada( 'url', $url );
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
	function existe_info_basica()
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
			$motores = array('postgres7', 'informix', 'mysql', 'odbc');
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
	
	//------------------------------------------------------------------------
	//-------------------------- Manejo de Versiones -------------------------
	//------------------------------------------------------------------------
	
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
	
	private function actualizar_version($version)
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
