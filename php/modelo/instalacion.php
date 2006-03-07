<?
require_once('modelo/lib/elemento_modelo.php');
require_once('nucleo/lib/manejador_archivos.php');
require_once('modelo/version_toba.php');
require_once('nucleo/lib/ini.php');

/**
*	@todo:	Control de que la estructura de los INIs sea correcta
*/
class instalacion extends elemento_modelo
{
	const db_encoding_estandar = 'LATIN1';
	const directorio_base = 'instalacion';
	const info_basica = 'instalacion.ini';
	const info_bases = 'bases.ini';
	private $dir;							// Directorio con info de la instalacion.
	private $ini_bases;						// Informacion de bases de datos.
	private $ini_instalacion;				// Informacion basica de la instalacion.

	function __construct()
	{
		$this->dir = self::dir_base();
		$this->cargar_info_ini();
	}

	function cargar_info_ini()
	{
		//--- Levanto la CONFIGURACION de bases
		$archivo_ini_bases = $this->dir . '/' . self::info_bases;
		if ( ! is_file( $archivo_ini_bases ) ) {
			throw new excepcion_toba("INSTALACION: La instalacion '".toba_dir()."' es invalida. (El archivo de configuracion '$archivo_ini_bases' no existe)");
		} else {
			//  BASE
			$this->ini_bases = parse_ini_file( $archivo_ini_bases, true );
		}
		//--- Levanto la CONFIGURACION de bases
		$archivo_ini_instalacion = $this->dir . '/' . self::info_basica;
		if ( ! is_file( $archivo_ini_instalacion ) ) {
			throw new excepcion_toba("INSTALACION: La instalacion '".toba_dir()."' es invalida. (El archivo de configuracion '$archivo_ini_instalacion' no existe)");
		} else {
			//  BASE
			$this->ini_instalacion = parse_ini_file( $archivo_ini_instalacion );
		}
	}

	//-----------------------------------------------------------
	//	Manejo de subcomponentes
	//-----------------------------------------------------------
		
	function get_instancias()
	{
		$instancias = array();
		foreach( instancia::get_lista() as $instancia ) {
			$instancias[ $instancia ] = new instancia( $this, $instancia );	
			$instancias[ $instancia ]->set_manejador_interface( $this->manejador_interface );	
		}
		return $instancias;
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
		return $this->ini_instalacion['id_grupo_desarrollo'];
	}

	/**
	* Devuelve las claves utilizadas para encriptar
	*/
	function get_claves_encriptacion()
	{
		$claves['db'] = $this->ini_instalacion['clave_querystring'];
		$claves['get'] = $this->ini_instalacion['clave_db'];
		return $claves;
	}

	function get_parametros_base( $id_base )
	{
		if ( isset( $this->ini_bases[$id_base] ) ) {
			return $this->ini_bases[$id_base];			
		} else {
			throw new excepcion_toba("INSTALACION: La base '$id_base' no existe en el archivo de instancias.");
		}
	}

	function existe_base_datos_definida( $id_base )
	{
		return isset( $this->ini_bases[$id_base] );
	}

	function hay_bases()
	{
		return count( $this->ini_bases ) > 0 ;
	}

	function get_lista_bases()
	{
		return array_keys( $this->ini_bases );
	}

	//------------------------------------------------------------------------
	// Relacion con el MOTOR de base de datos
	//------------------------------------------------------------------------

	/**
	*	Conecta una base de datos definida en bases.ini
	*	@param string $nombre Nombre de la base
	*/
	function conectar_base( $nombre )
	{
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
			$info_db['base'] = 'template1';
			$db = $this->conectar_base_parametros( $info_db );
			$sql = "CREATE DATABASE $base_a_crear ENCODING '" . self::db_encoding_estandar . "';";
			$db->ejecutar( $sql );
		}else{
			throw new excepcion_toba("INSTALACION: El metodo no esta definido para el motor especificado");
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
			$info_db['base'] = 'template1';
			$db = $this->conectar_base_parametros( $info_db );
			$sql = "DROP DATABASE $base_a_borrar;";
			$db->ejecutar($sql);
		}else{
			throw new excepcion_toba("INSTALACION: El metodo no esta definido para el motor especificado");
		}
	}

	/**
	*	Determina si una base de datos definida en bases.ini existe
	*	@param string $nombre Nombre de la base
	*/
	function existe_base_datos( $nombre )
	{
		try{
			$info_db = $this->get_parametros_base( $nombre );
			$db = $this->conectar_base_parametros( $info_db );
			$db->destruir();
		}catch(excepcion_toba $e){
			return false;
		}
		return true;
	}

	/**
	*	Conecta una BASE a partir de un juego de parametros
	*	@param array $parametros Parametros de conexion
	*/
	function conectar_base_parametros( $parametros )
	{
		$archivo = "nucleo/lib/db/db_" . $parametros['motor'] . ".php";
		$clase = "db_" . $parametros['motor'];
		require_once($archivo);
		$db = new $clase(	$parametros['profile'],
							$parametros['usuario'],
							$parametros['clave'],
							$parametros['base'] );
		$db->conectar();
		return $db;
	}

	//-------------------------------------------------------------------------
	//-- Funcionalidad estatica relacionada a la CREACION de INSTALACIONES
	//-------------------------------------------------------------------------

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
		}		
	}
	
	//-- Archivo de CONFIGURACION de la INSTALACION  --------------------------------------

	/**
	* Crea el archivo con la informacion basica sobre la instalacion	
	*/
	static function crear_info_basica($clave_qs, $clave_db, $id_grupo_desarrollo=null )
	{
		$ini = new ini();
		$ini->agregar_titulo("Configuracion de la INSTALACION");
		$ini->agregar_directiva( 'id_grupo_desarrollo', $id_grupo_desarrollo );
		$ini->agregar_directiva( 'clave_querystring', $clave_qs );	
		$ini->agregar_directiva( 'clave_db', $clave_db );	
		$ini->guardar( self::archivo_info_basica() );
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
		$ini = new ini();
		$ini->agregar_titulo("Configuracion de BASES de DATOS");
		foreach( $lista_bases as $id => $base ) {
			//Valido que la definicion sea correcta
			if( isset( $base['motor'] ) &&
				isset( $base['profile'] ) &&
				isset( $base['usuario'] ) &&
				isset( $base['clave'] ) &&
				isset( $base['base'] ) ) {
				$ini->agregar_seccion( $id, $base );	
			} else {
				throw new excepcion_toba("La definicion de la BASE '$id' es INCORRECTA.");	
			}
		}
		$ini->guardar( self::archivo_info_bases() );
	}
	
	static function agregar_db( $id_base, $parametros )
	{
		if ( ! is_array( $parametros ) ) {
			throw new excepcion_toba("INSTALACION: Los parametros definidos son incorrectos");	
		} else {
			if ( !isset( $parametros['motor']  )
				|| !isset( $parametros['profile'] ) 
				|| !isset( $parametros['usuario'] )
				|| !isset( $parametros['clave'] )
				|| !isset( $parametros['base'] ) ) {
				throw new excepcion_toba("INSTALACION: Los parametros definidos son incorrectos");	
			}
		}
		$ini = new ini( self::archivo_info_bases() );
		$ini->agregar_seccion( $id_base, $parametros );
		$ini->guardar();
	}
	
	static function eliminar_db( $id_base )
	{
		$ini = new ini( self::archivo_info_bases() );
		$ini->eliminar_seccion( $id_base );
		$ini->guardar();		
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

	function migrar_rango_versiones($desde, $hasta, $recursivo)
	{
		$versiones = $desde->get_secuencia_migraciones($hasta);
		foreach ($versiones as $version) {
			$this->manejador_interface->titulo("Versión ".$version->__toString());
			$this->migrar_version($version, $recursivo);
		}
	}
	
	function migrar_version($version, $recursivo)
	{
		$this->manejador_interface->mensaje("Migrando instalación");
		$version->ejecutar_migracion('instalacion', $this);
		
		//-- Se migran las instancias incluidas		
		if ($recursivo) {
			foreach ($this->get_instancias() as $instancia) {
				$instancia->migrar_version($version,$recursivo);
			}
		}
	}
	

	static function get_version_actual()
	{
		return new version_toba(file_get_contents(toba_dir()."/VERSION"));
	}
	
	
	function get_version_anterior()
	{
		$version_menor = null;
		foreach ($this->get_instancias() as $instancia) {
			$version_instancia = $instancia->get_version_actual();
			if (! isset($version_menor) || $version_instancia->es_menor_que($version_menor)) {
				$version_menor = $version_instancia;
			}
		}
		return $version_menor;
	}	
}
?>
