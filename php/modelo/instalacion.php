<?
require_once('nucleo/lib/reflexion/clase_datos.php');

class instalacion
{
	const directorio_base = 'instalacion';
	const info_basica = 'info_instalacion';
	const info_bases = 'info_bases';
	const instancia_prefijo = 'i__';
	const instancia_info = 'info_instancia';
	private static $toba_dir ='';

	static function set_toba_dir( $toba_dir )
	{
		self::$toba_dir = $toba_dir;
	}
	
	static function dir_base()
	{
		if (self::$toba_dir == '') {
			throw new excepcion_toba("Es necesario definir el directorio toba con el metodo 'set_toba_dir( directorio )'");	
		}
		return self::$toba_dir .'/'. self::directorio_base .'/';
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
	//-- Proyectos
	//-------------------------------------------------------------
		
	/**
	* Devuelve la lista de los proyectos que estan en la carpeta 'proyectos'
	*/
	function get_lista_proyectos()
	{
		$proyectos = array();
		$directorio_proyectos = self::$toba_dir . '/proyectos';
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