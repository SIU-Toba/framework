<?
/**
	Esta clase administra la utilizacion de bases de datos
*/
class dba
{
	private static $dba;			// Implementacion del singleton.
	private static $info_bases;		// Parametros de conexion.
	private $bases_conectadas;		// Conexiones abiertas

	//------------------------------------------------------------------------
	// Administracion de conexiones
	//------------------------------------------------------------------------

	private function __construct()
	{
		$this->bases_conectadas = array();
	}

	public static function get_db($nombre)
	//Devuelve una referencia a una CONEXION con una base
	{
		//ATENCION:	El nombre NULL deberia buscar la base por defecto
		$dba = self::get_instancia();
		return $dba->get_conexion($nombre);
	}

	private function get_conexion($nombre)
	//Manejo interno de las conexiones relaizadas
	{
		if(!isset($this->bases_conectadas[$nombre])){
			$parametros = self::get_info_db($nombre);
			$this->bases_conectadas[$nombre] = self::conectar_db($parametros);
		}
		return $this->bases_conectadas[$nombre];
	}

	//------------------------------------------------------------------------
	// Mantenimiento de BASES de DATOS
	//------------------------------------------------------------------------

	public static function crear_base_datos($nombre)
	//Crea una base de datos
	{
		$info_db = self::get_info_db($nombre);
		$base_a_crear = $info_db['base'];
		if($info_db['motor']=='postgres7')
		{
			$info_db['base'] = 'template1';
			$db = self::conectar_db($info_db);
			$sql = "CREATE DATABASE $base_a_crear;";
			$db->ejecutar($sql);
		}else{
			throw new excepcion_toba("El metodo no esta definido para el motor especificado");
		}
	}

	public static function borrar_base_datos($nombre)
	//Crea una base de datos
	{
		$info_db = self::get_info_db($nombre);
		$base_a_borrar = $info_db['base'];
		if($info_db['motor']=='postgres7')
		{
			$info_db['base'] = 'template1';
			$db = self::conectar_db($info_db);
			$sql = "DROP DATABASE $base_a_borrar;";
			$db->ejecutar($sql);
		}else{
			throw new excepcion_toba("El metodo no esta definido para el motor especificado");
		}
	}

	public static function existe_base_datos($nombre)
	{
		try{
			$db = self::conectar_db( self::get_info_db($nombre) );
			$db->destruir();
		}catch(excepcion_toba $e){
			return false;
		}
		return true;
	}

	//------------------------------------------------------------------------
	// Servicios internos
	//------------------------------------------------------------------------

	private static function get_info_db($nombre)
	{
		if (!isset(self::$info_bases)){
			include_once("../instancias/bases.php");
			self::$info_bases = $base;
		}
		if(!isset( self::$info_bases[$nombre])){
			throw new excepcion_toba("La base '$nombre' no fue definida");
		}
		return self::$info_bases[$nombre];
	}

	private static function conectar_db($parametros)
	//Fabrica de conexiones
	{
			$clase = "db_" . $parametros['motor'];
			$archivo = "db_" . $parametros['motor'] . ".php";
			require_once($archivo);
			return new $clase(	$parametros['profile'],
								$parametros['usuario'],
								$parametros['clave'],
								$parametros['base'] );
	}

	private static function get_instancia()
	//Devuelve una referencia a la instancia de la clase
	{
	   if (!isset(self::$dba)){
		   $c =	__CLASS__;
		   self::$dba = new $c;
	   }
	   return self::$dba;
	}
	//------------------------------------------------------------------------
}
?>