<?
require_once('modelo/lib/elemento_modelo.php');
require_once('nucleo/lib/reflexion/clase_datos.php');

class instalacion extends elemento_modelo
{
	const directorio_base = 'instalacion';
	const info_basica = 'info_instalacion';
	const info_bases = 'info_bases';
	const instancia_prefijo = 'i__';
	const instancia_info = 'info_instancia';

	function dir_base()
	{
		return toba_dir() .'/'. self::directorio_base .'/';
	}

	/**
	* Crea la informacion basica de una instalacion
	*/
	static function crear_directorio()
	{
		if( ! is_dir( self::dir_base() ) ) {
			mkdir( self::dir_base() );
		}		
	}

	//-------------------------------------------------------------
	//-- Informacion basica
	//-------------------------------------------------------------

	/**
	* path del archivo con informacion basica
	*/
	static function archivo_info_basica()
	{
		return self::dir_base() . self::info_basica . '.php';
	}

	/**
	* Crea el archivo con la informacion basica sobre la instalacion	
	*/
	static function crear_info_basica($clave_qs=null, $clave_db=null)
	{
		if( ! $clave_qs ) $clave_qs = md5(uniqid(rand(), true));
		if( ! $clave_db ) $clave_db = md5(uniqid(rand(), true));
		$clase = new clase_datos( self::info_basica );
		$clase->agregar_metodo_datos( 'get_clave_querystring', apex_clave_get );	
		$clase->agregar_metodo_datos( 'get_clave_db', apex_clave_db );	
		$clase->guardar( self::archivo_info_basica() );
	}
	
	/**
	* Indica si el archivo de informacion basica existe
	*/
	function existe_info_basica()
	{
		return ( is_file( self::archivo_info_basica() ) );
	}

	//-------------------------------------------------------------
	//-- Informacion bases
	//-------------------------------------------------------------

	/**
	* path del archivo con informacion basica
	*/
	static function archivo_info_bases()
	{
		return self::dir_base() . self::info_bases . '.php';
	}

	/**
	* Crea el archivo con la lista de bases disponibles
	*/
	static function crear_info_bases( $lista_bases )
	{
		$clase = new clase_datos( self::info_bases );
		foreach( $lista_bases as $id => $base ) {
			//Valido que la definicion sea correcta
			if( isset( $base['motor'] ) &&
				isset( $base['profile'] ) &&
				isset( $base['usuario'] ) &&
				isset( $base['clave'] ) &&
				isset( $base['base'] ) ) {
				$clase->agregar_metodo_datos( $id, $base );	
			}
		}
		$clase->guardar( self::archivo_info_bases() );
	}
	
	function existe_info_bases()
	{
		return ( is_file( self::archivo_info_bases() ) );
	}
	
	//-------------------------------------------------------------
	//-- INSTANCIAS
	//-------------------------------------------------------------

	static function dir_instancia( $nombre )
	{
		return self::dir_base() . 'i__' . $nombre;
	}

	static function existe_carpeta_instancia( $nombre )
	{
		return is_dir( self::dir_instancia( $nombre) );
	}

	/**
	* Agrega una instancia
	*/
	static function crear_instancia( $nombre, $base, $lista_proyectos )
	{
		//Creo la carpeta
		if( ! self::existe_carpeta_instancia( $nombre ) ) {
			mkdir( self::dir_instancia( $nombre ) );
		}
		//Creo la clase que proporciona informacion sobre la instancia
		$clase = new clase_datos( self::instancia_info );
		$clase->agregar_metodo_datos( 'get_base', $base );
		$clase->agregar_metodo_datos( 'get_lista_proyectos', $lista_proyectos );
		$clase->guardar( self::dir_instancia( $nombre ) . '/' . self::instancia_info . '.php');
	}

	//-------------------------------------------------------------
	//-- Informacion general
	//-------------------------------------------------------------
		
	/**
	* Devuelve la lista de las INSTANCIAS
	*/
	function get_lista_instancias()
	{
		$proyectos = array();
		$directorio_proyectos = toba_dir() . '/proyectos';
		if( is_dir( $directorio_proyectos ) ) {
			if ($dir = opendir($directorio_proyectos)) {	
			   while (false	!==	($archivo = readdir($dir)))	{ 
					if( is_dir($directorio_proyectos . '/' . $archivo) 
						&& ($archivo != '.' ) && ($archivo != '..' ) ){
						$proyectos[] = $archivo;
					}
			   } 
			   closedir($dir); 
			}
		}		
		return $proyectos;
	}

	/**
	* Devuelve la lista de las BASES
	*/
	function get_lista_bases()
	{
		$datos = array();
		foreach( dba::get_lista_bases_archivo() as $base ) {
			$datos[ $base ] = dba::get_parametros_base( $base );
		}
		return $datos;
	}

	/**
	* Devuelve la lista de los proyectos que estan en la carpeta 'proyectos'
	*/
	function get_lista_proyectos()
	{
		$proyectos = array();
		$directorio_proyectos = toba_dir() . '/proyectos';
		if( is_dir( $directorio_proyectos ) ) {
			if ($dir = opendir($directorio_proyectos)) {	
			   while (false	!==	($archivo = readdir($dir)))	{ 
					if( is_dir($directorio_proyectos . '/' . $archivo) 
						&& ($archivo != '.' ) && ($archivo != '..' ) ){
						$proyectos[] = $archivo;
					}
			   } 
			   closedir($dir); 
			}
		}		
		return $proyectos;
	}
}
?>