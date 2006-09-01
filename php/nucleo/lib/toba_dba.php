<?
/**
*	Administra la utilizacion de bases de datos durante la ejecucion
*
*	@todo 	- hay que sacar la suposicion de que si no se pasa un nombre se utilice la conexion 'instancia'
*				(algo puede funcionar en el administrador y dejar de andar en un proyecto)
*			- Hay que buscar una forma mejor de menejar las conexiones a la instancia que con 'instancia' + apex_pa_instancia
*/
class toba_dba
{
	const path_archivo_bases = '/instalacion/bases.ini';
	private static $dba;						// Implementacion del singleton.
	private static $info_bases;					// Parametros de las conexiones ABIERTAS
	private static $bases_definidas = null;		// Bases declaradas en BASES.INI
	private $bases_conectadas = array();		// Conexiones abiertas

	private function __construct()
	{
		self::cargar_bases_definidas();		
	}
	
	/**
	*	Levanta la lista de bases definidas
	*/
	static function cargar_bases_definidas()
	{
		$bases_definidas = array();
		self::$bases_definidas = parse_ini_file( toba_dir() . self::path_archivo_bases, true );
	}

	/**
	*	Busca la definicion de una base en 'bases.ini'
	*/
	static function get_parametros_base( $id_base )
	{
		if ( ! isset( self::$bases_definidas ) ) {
			self::cargar_bases_definidas();
		}
		if ( isset( self::$bases_definidas[ $id_base ] ) ) {
			return self::$bases_definidas[ $id_base ];
		} else {
			throw new toba_error("DBA: La BASE '$id_base' no esta definida en el archivo de definicion de BASES: '" . self::path_archivo_bases . "'" );
		}
	}
	
	function get_bases_definidas()
	{
		return self::$bases_definidas;	
	}
	
	//------------------------------------------------------------------------
	// Administracion de conexiones
	//------------------------------------------------------------------------

	/**
	*	Retorna una referencia a una CONEXION con una base
	*	@param string $nombre Por defecto toma la constante fuente_datos_defecto o la misma base de toba
	*	@return db
	*/
	static function get_db( $nombre )
	{
		return self::get_instancia()->get_conexion( $nombre );
	}
	
	/**
	*	¿Hay una conexión abierta a la base?
	*/
	static function existe_conexion( $nombre )
	{
		return self::get_instancia()->existe_conexion_privado( $nombre );	
	}

	/**
	*	Fuerza la recarga de los parametros de una conexion y reconecta a la base
	*/	
	static function refrescar( $nombre )
	{
		$dba = self::get_instancia();
		$dba->desconectar_db( $nombre );
		return self::get_db( $nombre );
	}

	/**
	*	Desconecta una DB
	*/	
	static function desconectar( $nombre )
	{
		$dba = self::get_instancia();
		$dba->desconectar_db( $nombre );
	}

	//------------------------------------------------------------------------
	// Servicios internos
	//------------------------------------------------------------------------

	/**
	*	Administracion interna de CONEXIONES.
	*/
	private function get_conexion( $nombre )
	{
		if( ! isset( $this->bases_conectadas[$nombre] ) ) {
			$this->bases_conectadas[$nombre] = self::conectar_db($nombre);
		}
		return $this->bases_conectadas[$nombre];
	}
	
	/**
	*	Creacion de conexiones
	*/
	private static function conectar_db($id_base)
	{
		$parametros = self::get_parametros_base( $id_base );
		//Controlo que esten todos los parametros
		if( !( isset($parametros['motor']) && isset($parametros['profile']) 
				&& isset($parametros['usuario']) && isset($parametros['clave'])
				&& isset($parametros['base']) ) ) {
			throw new toba_error("DBA: La BASE '$id_base' no esta definida correctamente." );
		}
		$archivo = "lib/db/db_" . $parametros['motor'] . ".php";
		$clase = "db_" . $parametros['motor'];
		require_once($archivo);
		$objeto_db = new $clase(	$parametros['profile'],
									$parametros['usuario'],
									$parametros['clave'],
									$parametros['base'] );
		$objeto_db->conectar();
		return $objeto_db;
	}

	/**
	*	Fuerza a reconectar en el proximo pedido de bases
	*/
	private function desconectar_db($nombre)
	{
		if ( isset( self::$info_bases[$nombre] ) ) {
			unset( self::$info_bases[$nombre] );
		}
		if ( isset( $this->bases_conectadas[$nombre] ) ) {
			$this->bases_conectadas[$nombre]->destruir();
			unset( $this->bases_conectadas[$nombre] );
		}
	}		

	private function existe_conexion_privado( $nombre )
	{
		return isset($this->bases_conectadas[$nombre]);
	}

	/**
	*	Devuelve una referencia a la instancia
	*/
	private static function get_instancia()
	{
	   if (!isset(self::$dba)){
		   $c =	__CLASS__;
		   self::$dba = new $c;
	   }
	   return self::$dba;
	}
}
?>